<?php

namespace App\Services\Mail;

use App\Models\User;
use App\Models\Event;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationService
{
    private PhpMailerService $mailerService;
    
    public function __construct()
    {
        $this->mailerService = new PhpMailerService();
    }
    
    /**
     * Send meeting created notification
     */
    public function meetingCreated(int $eventId): void
    {
        try {
            $event = Event::find($eventId);
            if (!$event) {
                Log::warning("Event not found for notification: {$eventId}");
                return;
            }
            
            // Get attendees
            $attendeeIds = json_decode($event->attendees ?? '[]', true);
            $attendees = User::whereIn('id', $attendeeIds)
                ->where('notify_email', true)
                ->get();
            
            $data = [
                'title' => $event->title,
                'start_time' => Carbon::parse($event->start_date)->format('F j, Y g:i A'),
                'location' => $event->location ?? 'TBD',
                'description' => $event->description ?? ''
            ];
            
            foreach ($attendees as $attendee) {
                $this->mailerService->queue(
                    $attendee->email,
                    'meeting_created',
                    array_merge($data, ['user_name' => $attendee->name])
                );
            }
            
            // Schedule reminder 2 hours before
            $reminderTime = Carbon::parse($event->start_date)->subHours(2);
            if ($reminderTime->isFuture()) {
                foreach ($attendees as $attendee) {
                    $this->mailerService->queue(
                        $attendee->email,
                        'meeting_reminder_2h',
                        array_merge($data, ['user_name' => $attendee->name]),
                        $reminderTime
                    );
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error sending meeting created notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send approval requested notification
     */
    public function approvalRequested(string $itemType, int $itemId, int $approverId, array $data = []): void
    {
        try {
            $approver = User::find($approverId);
            if (!$approver || !$approver->notify_email) {
                return;
            }
            
            $templateData = array_merge([
                'approver_name' => $approver->name,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'submitted_date' => now()->format('F j, Y g:i A')
            ], $data);
            
            $this->mailerService->queue(
                $approver->email,
                'approval_requested',
                $templateData
            );
            
        } catch (\Exception $e) {
            Log::error('Error sending approval requested notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send approval completed notification
     */
    public function approvalCompleted(string $itemType, int $itemId, int $userId, bool $approved, string $approverName, array $data = []): void
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->notify_email) {
                return;
            }
            
            $status = $approved ? 'Approved' : 'Rejected';
            $statusLower = $approved ? 'approved' : 'rejected';
            
            $templateData = array_merge([
                'user_name' => $user->name,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'status' => $status,
                'status_lower' => $statusLower,
                'approved' => $approved,
                'approver_name' => $approverName,
                'action_date' => now()->format('F j, Y g:i A')
            ], $data);
            
            $this->mailerService->queue(
                $user->email,
                'approval_done',
                $templateData
            );
            
        } catch (\Exception $e) {
            Log::error('Error sending approval completed notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send order submitted chain notification
     */
    public function orderSubmittedChain(int $orderId, array $data = []): void
    {
        try {
            // Get order submitter's management chain up to NSM
            $submitterId = $data['submitted_by'] ?? null;
            if (!$submitterId) {
                Log::warning('No submitter ID provided for order chain notification');
                return;
            }
            
            $submitter = User::find($submitterId);
            if (!$submitter) {
                Log::warning("Submitter not found: {$submitterId}");
                return;
            }
            
            // Get management chain based on role hierarchy
            $managementChain = $this->getManagementChain($submitter);
            
            $templateData = array_merge([
                'order_id' => $orderId,
                'submitted_by' => $submitter->name,
                'submitted_date' => now()->format('F j, Y g:i A'),
                'territory' => $submitter->territory ?? 'N/A'
            ], $data);
            
            foreach ($managementChain as $manager) {
                if ($manager->notify_email) {
                    $this->mailerService->queue(
                        $manager->email,
                        'order_submitted_chain',
                        array_merge($templateData, [
                            'recipient_name' => $manager->name,
                            'requires_approval' => in_array($manager->role, ['ASM', 'RSM', 'ZSM', 'NSM'])
                        ])
                    );
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error sending order submitted chain notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Get management chain for a user
     */
    private function getManagementChain(User $user): array
    {
        $chain = [];
        $roleHierarchy = ['TSO', 'ASM', 'RSM', 'ZSM', 'NSM', 'AGM', 'DGM', 'GM'];
        
        $currentRoleIndex = array_search($user->role, $roleHierarchy);
        if ($currentRoleIndex === false) {
            return $chain;
        }
        
        // Get managers up the chain
        for ($i = $currentRoleIndex + 1; $i < count($roleHierarchy) && $i <= 4; $i++) { // Up to NSM
            $managers = User::where('role', $roleHierarchy[$i])
                ->where('territory', $user->territory)
                ->where('notify_email', true)
                ->get();
            
            foreach ($managers as $manager) {
                $chain[] = $manager;
            }
        }
        
        return $chain;
    }
    
    /**
     * Send visit reminder notification
     */
    public function visitReminder(int $visitId, array $data = []): void
    {
        try {
            // This can be implemented when visit model is available
            Log::info("Visit reminder notification queued for visit: {$visitId}");
        } catch (\Exception $e) {
            Log::error('Error sending visit reminder notification: ' . $e->getMessage());
        }
    }

    /**
     * Send order created notification
     */
    public function sendOrderCreatedNotification($order): void
    {
        try {
            $this->orderSubmittedChain($order->id, [
                'submitted_by' => $order->user_id,
                'customer_name' => $order->customer_name,
                'amount' => $order->amount,
                'description' => $order->description
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending order created notification: ' . $e->getMessage());
        }
    }

    /**
     * Send order approved notification
     */
    public function sendOrderApprovedNotification($order): void
    {
        try {
            $this->approvalCompleted(
                'order',
                $order->id,
                $order->user_id,
                true,
                $order->approver->name ?? 'System',
                [
                    'customer_name' => $order->customer_name,
                    'amount' => $order->amount,
                    'notes' => $order->notes
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending order approved notification: ' . $e->getMessage());
        }
    }

    /**
     * Send approval request notification
     */
    public function sendApprovalRequestNotification(int $submitterId, string $itemType, int $itemId, array $data = []): void
    {
        try {
            // Get all users with approval permissions (Admin, Manager roles)
            $approvers = User::whereHas('role', function($query) {
                $query->whereIn('name', ['Admin', 'Manager', 'ASM', 'RSM', 'ZSM', 'NSM']);
            })->where('notify_email', true)->get();

            foreach ($approvers as $approver) {
                $this->approvalRequested($itemType, $itemId, $approver->id, $data);
            }
        } catch (\Exception $e) {
            Log::error('Error sending approval request notification: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Mail\PhpMailerService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BuildDailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:build-daily-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build daily digest emails for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Building daily digest emails...');
        
        try {
            $today = Carbon::today('Asia/Dhaka');
            $users = User::where('notify_email', true)->get();
            $mailerService = new PhpMailerService();
            $digestCount = 0;
            
            foreach ($users as $user) {
                $digestData = $this->buildDigestData($user, $today);
                
                if ($this->hasContent($digestData)) {
                    $mailerService->queue(
                        $user->email,
                        'daily_digest_10am',
                        array_merge($digestData, ['user_id' => $user->id]),
                        $today->copy()->setTime(10, 0)
                    );
                    $digestCount++;
                }
            }
            
            $this->info("Successfully queued {$digestCount} daily digest emails.");
            Log::info("BuildDailyDigest command completed. Queued {$digestCount} emails.");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Error building daily digest: ' . $e->getMessage());
            Log::error('BuildDailyDigest command failed: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Build digest data for a user
     */
    private function buildDigestData(User $user, Carbon $date): array
    {
        $data = [
            'user_name' => $user->name,
            'date' => $date->format('F j, Y'),
            'meetings' => [],
            'visits' => [],
            'pending_approvals' => []
        ];
        
        // Get today's meetings/events for the user
        try {
            $meetings = DB::table('events')
                ->where('start_date', '>=', $date->startOfDay())
                ->where('start_date', '<', $date->copy()->addDay()->startOfDay())
                ->where(function($query) use ($user) {
                    $query->where('created_by', $user->id)
                          ->orWhereRaw('JSON_CONTAINS(attendees, ?)', [json_encode($user->id)]);
                })
                ->select('title', 'start_date', 'location')
                ->get();
            
            $data['meetings'] = $meetings->toArray();
        } catch (\Exception $e) {
            Log::warning('Could not fetch meetings for daily digest: ' . $e->getMessage());
        }
        
        // Get today's visits for the user
        try {
            $visits = DB::table('visits')
                ->where('visit_date', $date->format('Y-m-d'))
                ->where('user_id', $user->id)
                ->select('customer_name', 'visit_time', 'purpose')
                ->get();
            
            $data['visits'] = $visits->toArray();
        } catch (\Exception $e) {
            Log::warning('Could not fetch visits for daily digest: ' . $e->getMessage());
        }
        
        // Get pending approvals for managers
        if (in_array($user->role, ['ASM', 'RSM', 'ZSM', 'NSM', 'AGM', 'DGM', 'GM'])) {
            try {
                $pendingApprovals = [];
                
                // Check orders pending approval
                $orders = DB::table('orders')
                    ->where('status', 'like', '%pending%')
                    ->where('next_approver_id', $user->id)
                    ->select('id', 'total_amount', 'created_by', 'created_at')
                    ->get();
                
                foreach ($orders as $order) {
                    $pendingApprovals[] = [
                        'type' => 'Order',
                        'id' => $order->id,
                        'amount' => $order->total_amount,
                        'submitted_by' => $order->created_by
                    ];
                }
                
                $data['pending_approvals'] = $pendingApprovals;
            } catch (\Exception $e) {
                Log::warning('Could not fetch pending approvals for daily digest: ' . $e->getMessage());
            }
        }
        
        return $data;
    }
    
    /**
     * Check if digest has any content worth sending
     */
    private function hasContent(array $digestData): bool
    {
        return !empty($digestData['meetings']) || 
               !empty($digestData['visits']) || 
               !empty($digestData['pending_approvals']);
    }
}

<?php

namespace App\Services\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\EmailQueue;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhpMailerService
{
    private array $smtpConfig;
    
    public function __construct()
    {
        $this->loadSmtpConfig();
    }
    
    /**
     * Load SMTP configuration from settings or .env
     */
    private function loadSmtpConfig(): void
    {
        // Try to load from settings table first
        $settings = $this->getEmailSettings();
        
        $this->smtpConfig = [
            'host' => $settings['smtp_host'] ?? env('MAIL_HOST', 'localhost'),
            'port' => $settings['smtp_port'] ?? env('MAIL_PORT', 587),
            'username' => $settings['smtp_username'] ?? env('MAIL_USERNAME', ''),
            'password' => $settings['smtp_password'] ?? env('MAIL_PASSWORD', ''),
            'encryption' => $settings['smtp_encryption'] ?? env('MAIL_ENCRYPTION', 'tls'),
            'from_email' => $settings['from_email'] ?? env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'from_name' => $settings['from_name'] ?? env('MAIL_FROM_NAME', 'Pharma Sales Force'),
        ];
    }
    
    /**
     * Get email settings from database
     */
    private function getEmailSettings(): array
    {
        try {
            $settings = DB::table('settings')
                ->where('type', 'email')
                ->pluck('value', 'key')
                ->toArray();
            
            return $settings;
        } catch (\Exception $e) {
            Log::warning('Could not load email settings from database: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Send email immediately
     */
    public function sendNow(string $toEmail, string $subject, string $htmlBody): bool
    {
        try {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtpConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpConfig['username'];
            $mail->Password = $this->smtpConfig['password'];
            $mail->SMTPSecure = $this->smtpConfig['encryption'];
            $mail->Port = (int)$this->smtpConfig['port'];
            $mail->CharSet = 'UTF-8';
            
            // Recipients
            $mail->setFrom($this->smtpConfig['from_email'], $this->smtpConfig['from_name']);
            $mail->addAddress($toEmail);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            Log::error('PHPMailer Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Queue email for later sending
     */
    public function queue(string $toEmail, string $templateSlug, array $data = [], \DateTime $scheduleAt = null): EmailQueue
    {
        $template = EmailTemplate::findBySlug($templateSlug);
        
        if (!$template) {
            throw new \Exception("Email template '{$templateSlug}' not found");
        }
        
        $rendered = $template->render($data);
        
        return EmailQueue::create([
            'to_email' => $toEmail,
            'to_user_id' => $data['user_id'] ?? null,
            'subject' => $rendered['subject'],
            'body' => $rendered['body'],
            'template_slug' => $templateSlug,
            'data_json' => $data,
            'scheduled_at' => $scheduleAt ?? now(),
            'status' => 'queued'
        ]);
    }
    
    /**
     * Send due emails from queue
     */
    public function sendDueEmails(): int
    {
        $dueEmails = EmailQueue::due()->limit(50)->get();
        $sentCount = 0;
        
        foreach ($dueEmails as $email) {
            $success = $this->sendNow($email->to_email, $email->subject, $email->body);
            
            if ($success) {
                $email->markAsSent();
                $sentCount++;
            } else {
                $this->handleFailedEmail($email);
            }
        }
        
        return $sentCount;
    }
    
    /**
     * Handle failed email with retry logic
     */
    private function handleFailedEmail(EmailQueue $email): void
    {
        $maxAttempts = 3;
        $retryDelays = [5, 15, 60]; // minutes
        
        if ($email->attempts < $maxAttempts) {
            $delayMinutes = $retryDelays[$email->attempts] ?? 60;
            $email->update([
                'scheduled_at' => now()->addMinutes($delayMinutes),
                'attempts' => $email->attempts + 1
            ]);
        } else {
            $email->markAsFailed('Max retry attempts exceeded');
        }
    }
    
    /**
     * Test SMTP connection
     */
    public function testConnection(): array
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->smtpConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpConfig['username'];
            $mail->Password = $this->smtpConfig['password'];
            $mail->SMTPSecure = $this->smtpConfig['encryption'];
            $mail->Port = (int)$this->smtpConfig['port'];
            
            // Test connection without sending
            $mail->smtpConnect();
            $mail->smtpClose();
            
            return ['success' => true, 'message' => 'SMTP connection successful'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'SMTP connection failed: ' . $e->getMessage()];
        }
    }
}
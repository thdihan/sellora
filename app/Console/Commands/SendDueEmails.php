<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Mail\PhpMailerService;
use Illuminate\Support\Facades\Log;

class SendDueEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due email notifications from the queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to send due emails...');
        
        try {
            $mailerService = new PhpMailerService();
            $sentCount = $mailerService->sendDueEmails();
            
            $this->info("Successfully sent {$sentCount} emails.");
            Log::info("SendDueEmails command completed. Sent {$sentCount} emails.");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Error sending emails: ' . $e->getMessage());
            Log::error('SendDueEmails command failed: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}

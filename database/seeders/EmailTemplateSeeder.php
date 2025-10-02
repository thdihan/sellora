<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'meeting_created',
                'subject_tpl' => 'New Meeting: {{title}}',
                'body_tpl' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #2c3e50; margin-bottom: 20px;">New Meeting Scheduled</h2>
        <p>Hello {{user_name}},</p>
        <p>A new meeting has been scheduled:</p>
        <div style="background-color: #e8f4f8; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Title:</strong> {{title}}<br>
            <strong>Date & Time:</strong> {{start_time}}<br>
            <strong>Location:</strong> {{location}}<br>
            <strong>Description:</strong> {{description}}
        </div>
        <p>Please mark your calendar accordingly.</p>
        <p>Best regards,<br>Sellora Team</p>
    </div>
</div>',
                'description' => 'Sent when a new meeting is created',
                'enabled' => true
            ],
            [
                'slug' => 'meeting_reminder_2h',
                'subject_tpl' => 'Reminder: {{title}} in 2 hours',
                'body_tpl' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #e74c3c; margin-bottom: 20px;">Meeting Reminder</h2>
        <p>Hello {{user_name}},</p>
        <p>This is a reminder that you have a meeting starting in 2 hours:</p>
        <div style="background-color: #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Title:</strong> {{title}}<br>
            <strong>Date & Time:</strong> {{start_time}}<br>
            <strong>Location:</strong> {{location}}
        </div>
        <p>Please prepare accordingly.</p>
        <p>Best regards,<br>Sellora Team</p>
    </div>
</div>',
                'description' => 'Sent 2 hours before a meeting starts',
                'enabled' => true
            ],
            [
                'slug' => 'daily_digest_10am',
                'subject_tpl' => 'Daily Digest - {{date}}',
                'body_tpl' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #2c3e50; margin-bottom: 20px;">Daily Digest - {{date}}</h2>
        <p>Good morning {{user_name}},</p>
        <p>Here\'s your daily summary:</p>
        
        {{#if meetings}}
        <div style="margin: 20px 0;">
            <h3 style="color: #3498db;">Today\'s Meetings</h3>
            {{#each meetings}}
            <div style="background-color: #e8f4f8; padding: 10px; margin: 5px 0; border-radius: 5px;">
                <strong>{{title}}</strong> at {{start_date}} - {{location}}
            </div>
            {{/each}}
        </div>
        {{/if}}
        
        {{#if visits}}
        <div style="margin: 20px 0;">
            <h3 style="color: #27ae60;">Scheduled Visits</h3>
            {{#each visits}}
            <div style="background-color: #d5f4e6; padding: 10px; margin: 5px 0; border-radius: 5px;">
                <strong>{{customer_name}}</strong> at {{visit_time}} - {{purpose}}
            </div>
            {{/each}}
        </div>
        {{/if}}
        
        {{#if pending_approvals}}
        <div style="margin: 20px 0;">
            <h3 style="color: #f39c12;">Pending Approvals</h3>
            {{#each pending_approvals}}
            <div style="background-color: #fef9e7; padding: 10px; margin: 5px 0; border-radius: 5px;">
                <strong>{{type}} #{{id}}</strong> - Amount: {{amount}} (Submitted by: {{submitted_by}})
            </div>
            {{/each}}
        </div>
        {{/if}}
        
        <p>Have a productive day!</p>
        <p>Best regards,<br>Sellora Team</p>
    </div>
</div>',
                'description' => 'Daily digest sent at 10 AM',
                'enabled' => true
            ],
            [
                'slug' => 'approval_requested',
                'subject_tpl' => 'Approval Required: {{item_type}} #{{item_id}}',
                'body_tpl' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #f39c12; margin-bottom: 20px;">Approval Required</h2>
        <p>Hello {{approver_name}},</p>
        <p>A new {{item_type}} requires your approval:</p>
        <div style="background-color: #fef9e7; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>{{item_type}} ID:</strong> #{{item_id}}<br>
            <strong>Submitted by:</strong> {{submitted_by}}<br>
            <strong>Amount:</strong> {{amount}}<br>
            <strong>Date:</strong> {{submitted_date}}<br>
            <strong>Comments:</strong> {{comments}}
        </div>
        <p>Please review and take appropriate action.</p>
        <p>Best regards,<br>Sellora Team</p>
    </div>
</div>',
                'description' => 'Sent when approval is requested',
                'enabled' => true
            ],
            [
                'slug' => 'approval_done',
                'subject_tpl' => '{{status}}: {{item_type}} #{{item_id}}',
                'body_tpl' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: {{#if approved}}#27ae60{{else}}#e74c3c{{/if}}; margin-bottom: 20px;">{{status}}</h2>
        <p>Hello {{user_name}},</p>
        <p>Your {{item_type}} has been {{status_lower}}:</p>
        <div style="background-color: {{#if approved}}#d5f4e6{{else}}#fadbd8{{/if}}; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>{{item_type}} ID:</strong> #{{item_id}}<br>
            <strong>{{status_lower}} by:</strong> {{approver_name}}<br>
            <strong>Date:</strong> {{action_date}}<br>
            {{#if comments}}<strong>Comments:</strong> {{comments}}{{/if}}
        </div>
        <p>{{#if approved}}You can now proceed with the next steps.{{else}}Please review the comments and resubmit if necessary.{{/if}}</p>
        <p>Best regards,<br>Sellora Team</p>
    </div>
</div>',
                'description' => 'Sent when approval is completed (approved/rejected)',
                'enabled' => true
            ],
            [
                'slug' => 'order_submitted_chain',
                'subject_tpl' => 'Order Submitted: #{{order_id}} - {{customer_name}}',
                'body_tpl' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #3498db; margin-bottom: 20px;">New Order Submitted</h2>
        <p>Hello {{recipient_name}},</p>
        <p>A new order has been submitted in your chain:</p>
        <div style="background-color: #e8f4f8; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Order ID:</strong> #{{order_id}}<br>
            <strong>Customer:</strong> {{customer_name}}<br>
            <strong>Total Amount:</strong> {{total_amount}}<br>
            <strong>Submitted by:</strong> {{submitted_by}}<br>
            <strong>Date:</strong> {{submitted_date}}<br>
            <strong>Territory:</strong> {{territory}}
        </div>
        <p>{{#if requires_approval}}This order requires approval before processing.{{else}}This order has been processed automatically.{{/if}}</p>
        <p>Best regards,<br>Sellora Team</p>
    </div>
</div>',
                'description' => 'Sent to management chain when order is submitted',
                'enabled' => true
            ]
        ];
        
        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
        
        $this->command->info('Email templates seeded successfully!');
    }
}

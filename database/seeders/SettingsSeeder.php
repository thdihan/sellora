<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing settings
        DB::table('settings')->truncate();

        $settings = [
            // Profile Settings
            [
                'type' => 'profile',
                'key_name' => 'profile_photo_max_size',
                'value' => json_encode('2048'), // 2MB in KB
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'profile',
                'key_name' => 'profile_photo_allowed_types',
                'value' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'profile',
                'key_name' => 'email_notifications_enabled',
                'value' => json_encode(true),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'profile',
                'key_name' => 'sms_notifications_enabled',
                'value' => json_encode(false),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'profile',
                'key_name' => 'push_notifications_enabled',
                'value' => json_encode(true),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'profile',
                'key_name' => 'password_min_length',
                'value' => json_encode('8'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],

            // Company Settings
            [
                'type' => 'company',
                'key_name' => 'company_name',
                'value' => json_encode('Sellora'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'company',
                'key_name' => 'company_email',
                'value' => json_encode('info@sellora.com'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'company',
                'key_name' => 'company_phone',
                'value' => json_encode('+1-234-567-8900'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'company',
                'key_name' => 'company_address',
                'value' => json_encode('123 Business Street, City, State 12345'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'company',
                'key_name' => 'company_timezone',
                'value' => json_encode('UTC'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'company',
                'key_name' => 'primary_color',
                'value' => json_encode('#007bff'),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'company',
                'key_name' => 'secondary_color',
                'value' => json_encode('#6c757d'),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'company',
                'key_name' => 'footer_brand_html',
                'value' => json_encode('<p>&copy; 2024 Sellora. All rights reserved.</p>'),
                'is_locked' => false,
                'locked_by_role' => null,
            ],

            // Application Settings
            [
                'type' => 'app',
                'key_name' => 'app_name',
                'value' => json_encode('Sellora'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'app_url',
                'value' => json_encode('http://localhost'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'app_debug',
                'value' => json_encode(false),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'maintenance_mode',
                'value' => json_encode(false),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'user_registration_enabled',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'multi_language_enabled',
                'value' => json_encode(false),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'api_enabled',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'cache_enabled',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'session_lifetime',
                'value' => json_encode('120'), // minutes
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'app',
                'key_name' => 'max_upload_size',
                'value' => json_encode('10240'), // 10MB in KB
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],

            // Email/Notifications Settings
            [
                'type' => 'email',
                'key_name' => 'mail_driver',
                'value' => json_encode('smtp'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'mail_host',
                'value' => json_encode('smtp.mailtrap.io'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'mail_port',
                'value' => json_encode('587'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'mail_username',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'mail_password',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'mail_encryption',
                'value' => json_encode('tls'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'mail_from_address',
                'value' => json_encode('noreply@sellora.com'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'mail_from_name',
                'value' => json_encode('Sellora'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'email',
                'key_name' => 'order_notifications',
                'value' => json_encode(true),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'email',
                'key_name' => 'user_notifications',
                'value' => json_encode(true),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'email',
                'key_name' => 'system_notifications',
                'value' => json_encode(true),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'email',
                'key_name' => 'marketing_emails',
                'value' => json_encode(false),
                'is_locked' => false,
                'locked_by_role' => null,
            ],

            // Security Settings
            [
                'type' => 'security',
                'key_name' => 'password_min_length',
                'value' => json_encode('8'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'password_require_uppercase',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'password_require_lowercase',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'password_require_numbers',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'password_require_symbols',
                'value' => json_encode(false),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'session_timeout',
                'value' => json_encode('30'), // minutes
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'max_login_attempts',
                'value' => json_encode('5'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'lockout_duration',
                'value' => json_encode('15'), // minutes
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'two_factor_enabled',
                'value' => json_encode(false),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'ip_whitelist_enabled',
                'value' => json_encode(false),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'allowed_ips',
                'value' => json_encode([]),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'security',
                'key_name' => 'api_rate_limit',
                'value' => json_encode('60'), // requests per minute
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],

            // Backup/Integrations Settings
            [
                'type' => 'backup',
                'key_name' => 'backup_enabled',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'backup_frequency',
                'value' => json_encode('daily'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'backup_time',
                'value' => json_encode('02:00'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'backup_retention_days',
                'value' => json_encode('30'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'backup_storage',
                'value' => json_encode('local'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'google_analytics_id',
                'value' => json_encode(''),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'backup',
                'key_name' => 'google_maps_api_key',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'aws_access_key',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'aws_secret_key',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'aws_region',
                'value' => json_encode('us-east-1'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'stripe_public_key',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'stripe_secret_key',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'paypal_client_id',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'paypal_client_secret',
                'value' => json_encode(''),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'backup',
                'key_name' => 'facebook_app_id',
                'value' => json_encode(''),
                'is_locked' => false,
                'locked_by_role' => null,
            ],
            [
                'type' => 'backup',
                'key_name' => 'twitter_api_key',
                'value' => json_encode(''),
                'is_locked' => false,
                'locked_by_role' => null,
            ],

            // Updates Settings
            [
                'type' => 'updates',
                'key_name' => 'auto_backup_before_update',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'updates',
                'key_name' => 'update_notifications',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'updates',
                'key_name' => 'rollback_enabled',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'updates',
                'key_name' => 'version_logging',
                'value' => json_encode(true),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'updates',
                'key_name' => 'max_rollback_versions',
                'value' => json_encode('5'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'updates',
                'key_name' => 'update_timeout',
                'value' => json_encode('30'), // minutes
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'updates',
                'key_name' => 'current_version',
                'value' => json_encode('1.0.0'),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'updates',
                'key_name' => 'last_update_check',
                'value' => json_encode(now()->toDateTimeString()),
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],

            // VAT & TAX Settings
            [
                'type' => 'tax',
                'key_name' => 'vat_rate',
                'value' => json_encode('15'), // 15% VAT rate
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
            [
                'type' => 'tax',
                'key_name' => 'tax_rate',
                'value' => json_encode('5'), // 5% TAX rate
                'is_locked' => true,
                'locked_by_role' => 'Admin',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create([
                'type' => $setting['type'],
                'key_name' => $setting['key_name'],
                'value' => $setting['value'],
                'is_locked' => $setting['is_locked'],
                'locked_by_role' => $setting['locked_by_role'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Settings seeded successfully!');
    }
}
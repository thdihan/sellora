<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('phone');
            $table->string('timezone', 50)->default('UTC')->after('bio');
            $table->boolean('email_notifications')->default(true)->after('timezone');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            $table->boolean('marketing_emails')->default(false)->after('sms_notifications');
            $table->boolean('security_alerts')->default(true)->after('marketing_emails');
            $table->timestamp('last_login_at')->nullable()->after('security_alerts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'timezone',
                'email_notifications',
                'sms_notifications',
                'marketing_emails',
                'security_alerts',
                'last_login_at'
            ]);
        });
    }
};

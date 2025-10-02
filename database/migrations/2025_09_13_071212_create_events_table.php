<?php

/**
 * Events Table Migration
 * 
 * Creates the events table with comprehensive fields for event management
 * including scheduling, attendees, recurring events, and role-based access.
 * 
 * @category Migration
 * @package  App\Database\Migrations
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 * @since    1.0.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Events Table Migration Class
 * 
 * This migration creates the events table with all necessary fields
 * for comprehensive event management functionality.
 * 
 * @category Migration
 * @package  App\Database\Migrations
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the events table with comprehensive fields including:
     * - Basic event information (title, description, type)
     * - Scheduling (start/end dates and times, all-day flag)
     * - Event management (priority, status, color coding)
     * - Attendees and notifications (JSON attendees, reminder settings)
     * - Recurring events support
     * - File attachments and notes
     * - User ownership and role-based access
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Basic event information
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('event_type', [
                'meeting',
                'appointment', 
                'deadline',
                'reminder',
                'personal',
                'holiday',
                'other'
            ])->default('meeting');
            
            // Scheduling fields
            $table->date('start_date');
            $table->date('end_date');
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_all_day')->default(false);
            
            // Event management
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent'
            ])->default('medium');
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'cancelled',
                'postponed'
            ])->default('scheduled');
            $table->string('color', 7)->nullable();
            
            // Notifications and attendees
            $table->integer('reminder_minutes')->nullable();
            $table->json('attendees')->nullable();
            $table->text('notes')->nullable();
            
            // User ownership for role-based access
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Recurring events support
            $table->enum('recurring_type', [
                'none',
                'daily',
                'weekly',
                'monthly',
                'yearly'
            ])->default('none');
            $table->date('recurring_end_date')->nullable();
            $table->json('recurring_days')->nullable();
            
            // File attachments
            $table->json('attachments')->nullable();
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drops the events table if it exists.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

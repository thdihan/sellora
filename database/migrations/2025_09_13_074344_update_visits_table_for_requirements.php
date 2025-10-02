<?php

/**
 * Update Visits Table Migration
 *
 * Updates the visits table to match requirements:
 * - Rename customer_name to client_name
 * - Add rescheduled_to field
 * - Simplify status workflow to: planned, completed, rescheduled, cancelled
 *
 * @category Migration
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Check if client_name column exists, if not rename customer_name
        if (Schema::hasColumn('visits', 'customer_name')) {
            Schema::table(
                'visits',
                function (Blueprint $table) {
                    $table->renameColumn('customer_name', 'client_name');
                }
            );
        }
        
        // Check if rescheduled_to column exists, if not add it
        if (!Schema::hasColumn('visits', 'rescheduled_to')) {
            Schema::table(
                'visits',
                function (Blueprint $table) {
                    $table->timestamp('rescheduled_to')->nullable()->after('rescheduled_from');
                }
            );
        }
        
        // Update status values from old to new format
        DB::table('visits')->where('status', 'scheduled')->update(['status' => 'planned']);
        DB::table('visits')->where('status', 'in_progress')->update(['status' => 'planned']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(
            'visits',
            function (Blueprint $table) {
                // Drop the new index
                $table->dropIndex('visits_status_scheduled_at_index');
            }
        );
        
        Schema::table(
            'visits',
            function (Blueprint $table) {
                // Reverse the changes
                $table->renameColumn('client_name', 'customer_name');
                $table->dropColumn('rescheduled_to');
                
                // Restore original status enum
                $table->dropColumn('status');
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'rescheduled'])
                      ->default('scheduled')
                      ->after('scheduled_at');
                      
                // Recreate original index
                $table->index(['status', 'scheduled_at']);
            }
        );
    }
};
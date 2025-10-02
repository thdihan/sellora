<?php

/**
 * Sales Targets Migration
 *
 * This migration creates the sales_targets table for managing
 * role-based target assignment and tracking.
 *
 * @category Database
 * @package  Database\Migrations
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            'sales_targets',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assigned_to_user_id')->constrained('users')->onDelete('cascade');
                $table->year('target_year');
                
                // Weekly targets
                $table->decimal('week_1_target', 15, 2)->default(0);
                $table->decimal('week_2_target', 15, 2)->default(0);
                $table->decimal('week_3_target', 15, 2)->default(0);
                $table->decimal('week_4_target', 15, 2)->default(0);
                
                // Monthly targets
                $table->decimal('january_target', 15, 2)->default(0);
                $table->decimal('february_target', 15, 2)->default(0);
                $table->decimal('march_target', 15, 2)->default(0);
                $table->decimal('april_target', 15, 2)->default(0);
                $table->decimal('may_target', 15, 2)->default(0);
                $table->decimal('june_target', 15, 2)->default(0);
                $table->decimal('july_target', 15, 2)->default(0);
                $table->decimal('august_target', 15, 2)->default(0);
                $table->decimal('september_target', 15, 2)->default(0);
                $table->decimal('october_target', 15, 2)->default(0);
                $table->decimal('november_target', 15, 2)->default(0);
                $table->decimal('december_target', 15, 2)->default(0);
                
                // Total yearly target (calculated field)
                $table->decimal('total_yearly_target', 15, 2)->default(0);
                
                // Status and metadata
                $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                // Indexes for performance
                $table->index(['assigned_to_user_id', 'target_year']);
                $table->index(['assigned_by_user_id', 'target_year']);
                $table->index(['status', 'target_year']);
                
                // Unique constraint to prevent duplicate targets for same user/year
                $table->unique(['assigned_to_user_id', 'target_year']);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};

<?php

/**
 * Bills Table Migration
 *
 * This migration creates the bills table with all necessary columns
 * for bill management including status workflow and approval tracking.
 *
 * @category Migrations
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
            'bills',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->string('purpose');
                $table->text('description')->nullable();
                $table->string('vendor')->nullable();
                $table->string('receipt_number')->nullable();
                $table->date('expense_date')->nullable();
                $table->string('category')->nullable();
                $table->string('payment_method')->nullable();
                $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
                $table->text('notes')->nullable();
                $table->enum('status', ['Pending', 'Approved', 'Forwarded', 'Paid', 'Rejected'])->default('Pending');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->text('rejected_reason')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('bills');
    }
};

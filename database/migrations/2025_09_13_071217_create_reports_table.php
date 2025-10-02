<?php

/**
 * Reports table migration
 *
 * Creates the reports table for storing generated report metadata
 * including filters, file paths, and generation status.
 *
 * @category Database
 * @package  Sellora\Migrations
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
            'reports',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['sales', 'expenses', 'visits', 'budgets', 'custom']);
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->json('filters')->nullable();
                $table->enum('format', ['pdf', 'excel', 'csv']);
                $table->string('file_path')->nullable();
                $table->integer('file_size')->nullable();
                $table->timestamp('generated_at');
                $table->timestamp('expires_at')->nullable();
                $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                $table->text('error_message')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'type']);
                $table->index(['status', 'generated_at']);
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
        Schema::dropIfExists('reports');
    }
};

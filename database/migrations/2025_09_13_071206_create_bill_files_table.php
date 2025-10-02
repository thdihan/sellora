<?php

/**
 * Bill Files Table Migration
 *
 * This migration creates the bill_files table for storing
 * file attachments associated with bills.
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
            'bill_files',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('bill_id')->constrained()->onDelete('cascade');
                $table->string('original_name');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->timestamps();
                
                $table->index('bill_id');
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
        Schema::dropIfExists('bill_files');
    }
};

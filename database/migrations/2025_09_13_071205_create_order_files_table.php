<?php

/**
 * Order Files Migration
 *
 * Creates the order_files table for storing file attachments related to orders.
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
            'order_files',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('original_name');
                $table->string('file_path');
                $table->string('file_type', 50);
                $table->unsignedBigInteger('file_size');
                $table->timestamps();
                
                $table->index('order_id');
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
        Schema::dropIfExists('order_files');
    }
};

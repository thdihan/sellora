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
        Schema::create('import_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->string('module'); // target module
            $table->integer('source_row_no')->nullable(); // row number in source file
            $table->json('payload'); // original data from source
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'skipped'])->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable(); // created/updated entity ID
            $table->timestamps();
            
            $table->foreign('job_id')->references('id')->on('import_jobs')->onDelete('cascade');
            $table->index(['job_id', 'status']);
            $table->index('module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_items');
    }
};

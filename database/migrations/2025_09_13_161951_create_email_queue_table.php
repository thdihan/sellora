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
        Schema::create('email_queue', function (Blueprint $table) {
            $table->id();
            $table->string('to_email', 191);
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->string('subject', 255);
            $table->mediumText('body');
            $table->string('template_slug', 100)->nullable();
            $table->json('data_json')->nullable();
            $table->datetime('scheduled_at');
            $table->datetime('sent_at')->nullable();
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->text('error')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'scheduled_at']);
            $table->index('to_user_id');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_queue');
    }
};

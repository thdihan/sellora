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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_id')->nullable(); // user who performed action
            $table->string('action'); // create, update, delete, import, export
            $table->string('entity_type'); // model class or module name
            $table->unsignedBigInteger('entity_id')->nullable(); // affected entity ID
            $table->json('metadata')->nullable(); // additional context data
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('actor_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['entity_type', 'entity_id']);
            $table->index(['actor_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address');
            $table->string('visit_type')->default('sales'); // sales, service, follow_up, delivery
            $table->text('purpose')->nullable();
            $table->dateTime('scheduled_at');
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('notes')->nullable();
            $table->text('outcome')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_address')->nullable();
            $table->json('attachments')->nullable();
            $table->decimal('estimated_duration', 4, 2)->default(1.00); // hours
            $table->decimal('actual_duration', 4, 2)->nullable(); // hours
            $table->boolean('requires_follow_up')->default(false);
            $table->dateTime('follow_up_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('rescheduled_from')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'scheduled_at']);
            $table->index(['status', 'scheduled_at']);
            $table->index('visit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

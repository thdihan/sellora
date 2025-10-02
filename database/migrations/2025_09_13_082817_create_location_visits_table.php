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
        Schema::create('location_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('visited_at');
            $table->timestamp('left_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->enum('check_in_method', ['manual', 'gps', 'qr_code', 'nfc', 'beacon'])->default('manual');
            $table->enum('check_out_method', ['manual', 'gps', 'auto', 'timeout'])->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->string('weather')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->tinyInteger('mood_rating')->nullable()->comment('1-5 rating');
            $table->tinyInteger('productivity_rating')->nullable()->comment('1-5 rating');
            $table->json('photos')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['location_id', 'visited_at']);
            $table->index(['user_id', 'visited_at']);
            $table->index('visited_at');
            $table->index(['visited_at', 'left_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_visits');
    }
};

<?php

/**
 * Location Tracking Migration
 *
 * Creates the location tracking table for real-time pharma sales force tracking
 * with the exact schema required for the Location Tracking module.
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
     * Creates the location tracking table for real-time pharma sales force tracking
     * with the exact schema required for the Location Tracking module.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            'location_tracking',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('latitude', 10, 6); // DECIMAL(10,6) as specified
                $table->decimal('longitude', 10, 6); // DECIMAL(10,6) as specified
                $table->decimal('accuracy', 10, 2)->nullable(); // DECIMAL(10,2) as specified
                $table->timestamp('captured_at')->useCurrent(); // DEFAULT CURRENT_TIMESTAMP
                $table->timestamps();
                
                // Indexes as specified in requirements
                $table->index('user_id');
                $table->index('captured_at');
                
                // Additional index for performance
                $table->index(['user_id', 'captured_at']);
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
        Schema::dropIfExists('location_tracking');
    }
};
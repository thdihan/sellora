<?php

/**
 * Add Order Number Column Migration
 *
 * Adds the order_number column to the orders table to store unique order identifiers.
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
        Schema::table(
            'orders',
            function (Blueprint $table) {
                $table->string('order_number', 20)->unique()->nullable()->after('id');
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
        Schema::table(
            'orders',
            function (Blueprint $table) {
                $table->dropColumn('order_number');
            }
        );
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            'warehouses',
            function (Blueprint $table) {
                $table->boolean('is_main')->default(false)->after('status');
                $table->string('phone', 20)->nullable()->after('address');
                $table->string('email', 100)->nullable()->after('phone');
                
                $table->index('is_main');
            }
        );
        
        // Set the first warehouse as the main warehouse
        DB::statement("UPDATE warehouses SET is_main = true WHERE id = (SELECT id FROM (SELECT id FROM warehouses ORDER BY id LIMIT 1) as temp)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(
            'warehouses',
            function (Blueprint $table) {
                $table->dropColumn(['is_main', 'phone', 'email']);
            }
        );
    }
};
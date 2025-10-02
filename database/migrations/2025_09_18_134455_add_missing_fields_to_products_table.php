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
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('sku');
            $table->integer('min_stock_level')->default(0)->after('current_stock');
            $table->integer('max_stock_level')->default(0)->after('min_stock_level');
            $table->decimal('weight', 8, 2)->nullable()->after('max_stock_level');
            $table->string('dimensions', 100)->nullable()->after('weight');
            $table->string('tax_code', 50)->nullable()->after('tax_rate');
            $table->boolean('is_taxable')->default(false)->after('tax_code');
            $table->json('meta_data')->nullable()->after('is_taxable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'min_stock_level',
                'max_stock_level', 
                'weight',
                'dimensions',
                'tax_code',
                'is_taxable',
                'meta_data'
            ]);
        });
    }
};

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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('vat_condition', ['client_bears', 'company_bears'])->default('client_bears')->after('total_amount');
            $table->enum('tax_condition', ['client_bears', 'company_bears'])->default('client_bears')->after('vat_condition');
            $table->decimal('vat_amount', 10, 2)->default(0)->after('tax_condition');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('vat_amount');
            $table->decimal('net_revenue', 10, 2)->nullable()->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['vat_condition', 'tax_condition', 'vat_amount', 'tax_amount', 'net_revenue']);
        });
    }
};

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
        Schema::create('import_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('source_type'); // API, CSV, Excel, SQL_DUMP
            $table->string('module'); // target module
            $table->json('column_map'); // field mapping configuration
            $table->json('options')->nullable(); // additional import options
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('created_by');
            $table->index(['source_type', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_presets');
    }
};

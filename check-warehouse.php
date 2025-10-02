<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Checking warehouse status...\n";
    
    $warehouseCount = \App\Models\Warehouse::count();
    echo "Total warehouses: $warehouseCount\n";
    
    $mainWarehouses = \App\Models\Warehouse::where('is_main', true)->count();
    echo "Main warehouses: $mainWarehouses\n";
    
    if ($warehouseCount > 0) {
        echo "\nWarehouse details:\n";
        \App\Models\Warehouse::all()->each(function ($warehouse) {
            echo "ID: {$warehouse->id}, Name: {$warehouse->name}, Is Main: " . 
                 ($warehouse->is_main ? 'Yes' : 'No') . ", Status: " . 
                 ($warehouse->status ? 'Active' : 'Inactive') . "\n";
        });
    }
    
    $mainWarehouse = \App\Models\Warehouse::getMain();
    if ($mainWarehouse) {
        echo "\nMain warehouse found: {$mainWarehouse->name}\n";
    } else {
        echo "\nNo main warehouse found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php

/**
 * Product Module Seeder
 *
 * This file contains the seeder for populating the product module with demo data.
 * It creates categories, brands, units, products, product batches, stock transactions,
 * and stock balances for testing and demonstration purposes.
 *
 * @category Database
 * @package  Database\Seeders
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 * @version  1.0.0
 */

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductModuleSeeder
 *
 * Seeds the database with product module demo data including categories,
 * brands, units, products, batches, and stock information.
 *
 * @category Database
 * @package  Database\Seeders
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class ProductModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Executes all seeding methods in the correct order to populate
     * the product module with demo data.
     *
     * @return void
     */
    public function run(): void
    {
        DB::transaction(
            function () {
                $this->_seedProductCategories();
                $this->_seedProductBrands();
                $this->_seedProductUnits();
                $this->_seedWarehouses();
                $this->_seedProducts();
                $this->_seedProductBatches();
                $this->_seedStockTransactions();
            }
        );
    }
    
    /**
     * Seed product categories.
     *
     * Creates demo categories for organizing products.
     *
     * @return void
     */
    private function _seedProductCategories(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'status' => true,
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'status' => true,
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'status' => true,
            ],
            [
                'name' => 'Books',
                'description' => 'Books and educational materials',
                'status' => true,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'status' => true,
            ],
            [
                'name' => 'Health & Beauty',
                'description' => 'Health and beauty products',
                'status' => true,
            ],
        ];
        
        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
    
    /**
     * Seed product brands.
     *
     * Creates demo brands for products.
     *
     * @return void
     */
    private function _seedProductBrands(): void
    {
        $brands = [
            [
                'name' => 'Samsung',
                'description' => 'South Korean multinational conglomerate',
                'status' => true,
            ],
            [
                'name' => 'Apple',
                'description' => 'American multinational technology company',
                'status' => true,
            ],
            [
                'name' => 'Nike',
                'description' => 'American multinational corporation',
                'status' => true,
            ],
            [
                'name' => 'Adidas',
                'description' => 'German multinational corporation',
                'status' => true,
            ],
            [
                'name' => 'Sony',
                'description' => 'Japanese multinational conglomerate',
                'status' => true,
            ],
            [
                'name' => 'Generic',
                'description' => 'Generic brand for unbranded products',
                'status' => true,
            ],
        ];
        
        foreach ($brands as $brand) {
            ProductBrand::firstOrCreate(
                ['name' => $brand['name']],
                $brand
            );
        }
    }
    
    /**
     * Seed measurement units.
     *
     * Creates demo units for product measurements.
     *
     * @return void
     */
    private function _seedProductUnits(): void
    {
        $units = [
            [
                'name' => 'Piece',
                'symbol' => 'pcs',
                'status' => true,
            ],
            [
                'name' => 'Kilogram',
                'symbol' => 'kg',
                'status' => true,
            ],
            [
                'name' => 'Liter',
                'symbol' => 'L',
                'status' => true,
            ],
            [
                'name' => 'Meter',
                'symbol' => 'm',
                'status' => true,
            ],
            [
                'name' => 'Box',
                'symbol' => 'box',
                'status' => true,
            ],
            [
                'name' => 'Pack',
                'symbol' => 'pack',
                'status' => true,
            ],
        ];
        
        foreach ($units as $unit) {
            ProductUnit::firstOrCreate(
                ['name' => $unit['name']],
                $unit
            );
        }
    }
    
    /**
     * Seed warehouses.
     *
     * Creates demo warehouses for stock management.
     *
     * @return void
     */
    private function _seedWarehouses(): void
    {
        // Create only one main warehouse
        $warehouse = [
            'name' => 'Main Warehouse',
            'code' => 'WH001',
            'address' => '123 Main Street, Dhaka, Bangladesh',
            'phone' => '+880-1234-567890',
            'email' => 'warehouse@company.com',
            'status' => true,
            'is_main' => true,
        ];
        
        Warehouse::firstOrCreate(
            ['code' => $warehouse['code']],
            $warehouse
        );
    }
    
    /**
     * Seed products.
     *
     * Creates demo products with associated categories, brands, and units.
     *
     * @return void
     */
    private function _seedProducts(): void
    {
        $products = [
            [
                'name' => 'Samsung Galaxy S23',
                'sku' => 'SGS23-001',
                'category_id' => 1, // Electronics
                'brand_id' => 1, // Samsung
                'unit_id' => 1, // Piece
                'purchase_price' => 80000.00,
                'selling_price' => 95000.00,
                'reorder_level' => 10,
                'reorder_qty' => 50,
                'status' => true,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'IP15P-001',
                'category_id' => 1, // Electronics
                'brand_id' => 2, // Apple
                'unit_id' => 1, // Piece
                'purchase_price' => 120000.00,
                'selling_price' => 140000.00,
                'reorder_level' => 5,
                'reorder_qty' => 25,
                'status' => true,
            ],
            [
                'name' => 'Nike Air Max 270',
                'sku' => 'NAM270-001',
                'category_id' => 5, // Sports & Outdoors
                'brand_id' => 3, // Nike
                'unit_id' => 1, // Piece
                'purchase_price' => 8000.00,
                'selling_price' => 12000.00,
                'reorder_level' => 20,
                'reorder_qty' => 100,
                'status' => true,
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'sku' => 'AUB22-001',
                'category_id' => 5, // Sports & Outdoors
                'brand_id' => 4, // Adidas
                'unit_id' => 1, // Piece
                'purchase_price' => 10000.00,
                'selling_price' => 15000.00,
                'reorder_level' => 15,
                'reorder_qty' => 75,
                'status' => true,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'sku' => 'SWH1000-001',
                'category_id' => 1, // Electronics
                'brand_id' => 5, // Sony
                'unit_id' => 1, // Piece
                'purchase_price' => 25000.00,
                'selling_price' => 32000.00,
                'reorder_level' => 8,
                'reorder_qty' => 40,
                'status' => true,
            ],
            [
                'name' => 'Cotton T-Shirt',
                'sku' => 'CTS-001',
                'category_id' => 2, // Clothing
                'brand_id' => 6, // Generic
                'unit_id' => 1, // Piece
                'purchase_price' => 500.00,
                'selling_price' => 800.00,
                'reorder_level' => 50,
                'reorder_qty' => 250,
                'status' => true,
            ],
        ];
        
        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
    
    /**
     * Seed product batches.
     *
     * Creates demo product batches with manufacturing and expiry dates.
     *
     * @return void
     */
    private function _seedProductBatches(): void
    {
        $batches = [
            [
                'product_id' => 1,
                'batch_no' => 'SGS23-B001',
                'mfg_date' => now()->subDays(30),
                'exp_date' => now()->addDays(365),
                'mrp' => 1500.00,
                'purchase_price' => 1200.00,
            ],
            [
                'product_id' => 2,
                'batch_no' => 'IP15P-B001',
                'mfg_date' => now()->subDays(15),
                'exp_date' => null,
                'mrp' => 140000.00,
                'purchase_price' => 120000.00,
            ],
            [
                'product_id' => 3,
                'batch_no' => 'NAM270-B001',
                'mfg_date' => now()->subDays(45),
                'exp_date' => null,
                'mrp' => 12000.00,
                'purchase_price' => 8000.00,
            ],
            [
                'product_id' => 4,
                'batch_no' => 'AUB22-B001',
                'mfg_date' => now()->subDays(20),
                'exp_date' => null,
                'mrp' => 15000.00,
                'purchase_price' => 10000.00,
            ],
            [
                'product_id' => 5,
                'batch_no' => 'SWH1000-B001',
                'mfg_date' => now()->subDays(10),
                'exp_date' => null,
                'mrp' => 32000.00,
                'purchase_price' => 25000.00,
            ],
        ];
        
        foreach ($batches as $batch) {
            ProductBatch::firstOrCreate(
                ['product_id' => $batch['product_id'], 'batch_no' => $batch['batch_no']],
                $batch
            );
        }
    }
    
    /**
     * Seed stock transactions and balances.
     *
     * Creates demo stock transactions and updates stock balances accordingly.
     *
     * @return void
     */
    private function _seedStockTransactions(): void
    {
        $transactions = [
            [
                'product_id' => 1,
                'warehouse_id' => 1,
                'batch_id' => 1,
                'type' => 'purchase_in',
                'qty' => 50,
                'ref_type' => 'purchase',
                'note' => 'Initial stock purchase',
            ],
            [
                'product_id' => 2,
                'warehouse_id' => 1,
                'batch_id' => 2,
                'type' => 'purchase_in',
                'qty' => 25,
                'ref_type' => 'purchase',
                'note' => 'Initial stock purchase',
            ],
            [
                'product_id' => 3,
                'warehouse_id' => 2,
                'batch_id' => 3,
                'type' => 'purchase_in',
                'qty' => 100,
                'ref_type' => 'purchase',
                'note' => 'Initial stock purchase',
            ],
            [
                'product_id' => 4,
                'warehouse_id' => 1,
                'batch_id' => 4,
                'type' => 'purchase_in',
                'qty' => 75,
                'ref_type' => 'purchase',
                'note' => 'Initial stock purchase',
            ],
            [
                'product_id' => 5,
                'warehouse_id' => 2,
                'batch_id' => 5,
                'type' => 'purchase_in',
                'qty' => 40,
                'ref_type' => 'purchase',
                'note' => 'Initial stock purchase',
            ],
        ];
        
        foreach ($transactions as $transactionData) {
            $existingTransaction = StockTransaction::where(
                [
                    'product_id' => $transactionData['product_id'],
                    'warehouse_id' => $transactionData['warehouse_id'],
                    'batch_id' => $transactionData['batch_id'],
                    'type' => $transactionData['type'],
                    'ref_type' => $transactionData['ref_type']
                ]
            )->first();
            
            if (!$existingTransaction) {
                $transaction = StockTransaction::create($transactionData);
                
                // Create or update stock balance
                $stockBalance = StockBalance::firstOrCreate(
                    [
                        'product_id' => $transaction->product_id,
                        'warehouse_id' => $transaction->warehouse_id,
                        'batch_id' => $transaction->batch_id,
                    ],
                    [
                        'qty_on_hand' => 0,
                        'qty_reserved' => 0,
                    ]
                );
                
                if (in_array(
                    $transaction->type,
                    ['opening', 'purchase_in', 'transfer_in', 'sale_return', 'adjustment_in']
                )) {
                    $stockBalance->increment('qty_on_hand', $transaction->qty);
                } else {
                    $stockBalance->decrement('qty_on_hand', $transaction->qty);
                }
            }
        }
    }
}
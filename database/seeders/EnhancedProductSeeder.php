<?php

/**
 * Enhanced Product Seeder
 * 
 * This file contains the seeder for populating products, categories, brands,
 * and units for the Sellora application's inventory management system.
 * 
 * @category Database
 * @package  Database\Seeders
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use App\Models\ProductUnit;
use App\Models\Warehouse;

/**
 * Class EnhancedProductSeeder
 * 
 * Seeds the database with comprehensive demo product data including
 * categories, brands, units, and sample products across various categories.
 * 
 * @category Database
 * @package  Database\Seeders
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */
class EnhancedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run(): void
    {
        // First create categories, brands, and units if they don't exist
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'description' => 'Apparel and fashion items'],
            ['name' => 'Books', 'description' => 'Books and publications'],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and garden items'],
            ['name' => 'Food & Beverages', 'description' => 'Food and drink products'],
        ];

        foreach ($categories as $categoryData) {
            ProductCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        $brands = [
            ['name' => 'Apple', 'description' => 'Technology company'],
            ['name' => 'Samsung', 'description' => 'Electronics manufacturer'],
            ['name' => 'Nike', 'description' => 'Sportswear brand'],
            ['name' => 'Adidas', 'description' => 'Athletic apparel'],
            ['name' => 'Generic', 'description' => 'Generic brand'],
        ];

        foreach ($brands as $brandData) {
            ProductBrand::firstOrCreate(
                ['name' => $brandData['name']],
                $brandData
            );
        }

        $units = [
            ['name' => 'Piece', 'symbol' => 'pcs'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Liter', 'symbol' => 'L'],
            ['name' => 'Meter', 'symbol' => 'm'],
            ['name' => 'Box', 'symbol' => 'box'],
        ];

        foreach ($units as $unitData) {
            ProductUnit::firstOrCreate(
                ['name' => $unitData['name']],
                $unitData
            );
        }

        // Get IDs for relationships
        $electronicsCategory = ProductCategory::where('name', 'Electronics')->first();
        $clothingCategory = ProductCategory::where('name', 'Clothing')->first();
        $booksCategory = ProductCategory::where('name', 'Books')->first();
        $homeCategory = ProductCategory::where('name', 'Home & Garden')->first();
        $foodCategory = ProductCategory::where('name', 'Food & Beverages')->first();

        $appleBrand = ProductBrand::where('name', 'Apple')->first();
        $samsungBrand = ProductBrand::where('name', 'Samsung')->first();
        $nikeBrand = ProductBrand::where('name', 'Nike')->first();
        $adidasBrand = ProductBrand::where('name', 'Adidas')->first();
        $genericBrand = ProductBrand::where('name', 'Generic')->first();

        $pieceUnit = ProductUnit::where('name', 'Piece')->first();
        $kgUnit = ProductUnit::where('name', 'Kilogram')->first();
        $literUnit = ProductUnit::where('name', 'Liter')->first();
        $boxUnit = ProductUnit::where('name', 'Box')->first();

        // Create enhanced product data
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'generic_name' => 'Smartphone',
                'composition' => 'A17 Pro chip, Titanium build',
                'sku' => 'IPHONE-15-PM-256',
                'barcode' => '1234567890123',
                'category_id' => $electronicsCategory->id,
                'brand_id' => $appleBrand->id,
                'unit_id' => $pieceUnit->id,
                'purchase_price' => 899.99,
                'selling_price' => 1199.99,
                'tax_rate' => 20.00,
                'reorder_level' => 10,
                'reorder_qty' => 50,
                'status' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'generic_name' => 'Smartphone',
                'composition' => 'Snapdragon 8 Gen 3, S Pen included',
                'sku' => 'GALAXY-S24-ULTRA-512',
                'barcode' => '2345678901234',
                'category_id' => $electronicsCategory->id,
                'brand_id' => $samsungBrand->id,
                'unit_id' => $pieceUnit->id,
                'purchase_price' => 799.99,
                'selling_price' => 1099.99,
                'tax_rate' => 20.00,
                'reorder_level' => 15,
                'reorder_qty' => 40,
                'status' => true,
            ],
            [
                'name' => 'Nike Air Max 270',
                'generic_name' => 'Running Shoes',
                'composition' => 'Mesh upper, Air Max sole',
                'sku' => 'NIKE-AM270-BLK-42',
                'barcode' => '3456789012345',
                'category_id' => $clothingCategory->id,
                'brand_id' => $nikeBrand->id,
                'unit_id' => $pieceUnit->id,
                'purchase_price' => 89.99,
                'selling_price' => 149.99,
                'tax_rate' => 20.00,
                'reorder_level' => 20,
                'reorder_qty' => 100,
                'status' => true,
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'generic_name' => 'Athletic Shoes',
                'composition' => 'Primeknit upper, Boost midsole',
                'sku' => 'ADIDAS-UB22-WHT-41',
                'barcode' => '4567890123456',
                'category_id' => $clothingCategory->id,
                'brand_id' => $adidasBrand->id,
                'unit_id' => $pieceUnit->id,
                'purchase_price' => 119.99,
                'selling_price' => 179.99,
                'tax_rate' => 20.00,
                'reorder_level' => 15,
                'reorder_qty' => 80,
                'status' => true,
            ],
            [
                'name' => 'Programming Book Set',
                'generic_name' => 'Educational Books',
                'composition' => 'Collection of programming guides',
                'sku' => 'BOOK-PROG-SET-001',
                'barcode' => '5678901234567',
                'category_id' => $booksCategory->id,
                'brand_id' => $genericBrand->id,
                'unit_id' => $boxUnit->id,
                'purchase_price' => 45.99,
                'selling_price' => 79.99,
                'tax_rate' => 5.00,
                'reorder_level' => 25,
                'reorder_qty' => 50,
                'status' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('Enhanced products created successfully.');
        $this->command->info('Created ' . count($products) . ' products with categories, brands, and units.');
    }
}

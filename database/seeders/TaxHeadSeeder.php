<?php

/**
 * Tax Head Seeder
 *
 * Seeds the tax_heads table with sample data for testing and development.
 *
 * @category Seeders
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaxHead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Class TaxHeadSeeder
 * 
 * Seeds the database with demo tax heads for various tax scenarios
 * including VAT, AIT, and other tax types commonly used in business.
 * 
 * @category Database
 * @package  Database\Seeders
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */
class TaxHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Clear existing tax heads
        DB::table('tax_heads')->truncate();
        
        // Get the first admin user as creator
        $adminUser = User::whereHas('role', function($query) {
            $query->where('name', 'Admin');
        })->first();
        
        $createdBy = $adminUser ? $adminUser->id : 1;
        
        // Create sample tax heads
        $taxHeads = [
            [
                'name' => 'Standard VAT',
                'code' => 'VAT_STD',
                'kind' => 'VAT',
                'percentage' => 20.00,
                'visible_to_client' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Reduced VAT',
                'code' => 'VAT_RED',
                'kind' => 'VAT',
                'percentage' => 5.00,
                'visible_to_client' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Zero VAT',
                'code' => 'VAT_ZERO',
                'kind' => 'VAT',
                'percentage' => 0.00,
                'visible_to_client' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Advance Income Tax',
                'code' => 'AIT_STD',
                'kind' => 'AIT',
                'percentage' => 3.00,
                'visible_to_client' => false,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Service Tax',
                'code' => 'SRV_TAX',
                'kind' => 'OTHER',
                'percentage' => 15.00,
                'visible_to_client' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Luxury Tax',
                'code' => 'LUX_TAX',
                'kind' => 'OTHER',
                'percentage' => 25.00,
                'visible_to_client' => true,
                'created_by' => $createdBy,
            ],
            [
                'name' => 'Digital Services Tax',
                'code' => 'DST',
                'kind' => 'OTHER',
                'percentage' => 10.00,
                'visible_to_client' => true,
                'created_by' => $createdBy,
            ],
        ];
        
        foreach ($taxHeads as $taxHeadData) {
            TaxHead::create($taxHeadData);
        }
        
        $this->command->info('Tax heads created successfully.');
        $this->command->info('Created ' . count($taxHeads) . ' tax heads for demo purposes.');
    }
}

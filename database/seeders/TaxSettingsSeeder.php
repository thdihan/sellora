<?php

/**
 * Tax Settings Seeder
 * 
 * This file contains the seeder for populating tax codes and rules
 * for the Sellora application's tax management system.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxCode;
use App\Models\TaxRule;

/**
 * Class TaxSettingsSeeder
 * 
 * Seeds the database with demo tax codes for various tax scenarios
 * including VAT, luxury tax, digital services tax, and export exemptions.
 */
class TaxSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run(): void
    {
        // Create Tax Codes
        $taxCodes = [
            [
                'code' => 'VAT_STANDARD',
                'name' => 'Standard VAT',
                'description' => 'Standard Value Added Tax rate for most goods and services (20%)',
                'is_active' => true,
            ],
            [
                'code' => 'VAT_REDUCED',
                'name' => 'Reduced VAT',
                'description' => 'Reduced VAT rate for essential goods like food and books (5%)',
                'is_active' => true,
            ],
            [
                'code' => 'VAT_ZERO',
                'name' => 'Zero VAT',
                'description' => 'Zero-rated VAT for exports and certain exempt goods (0%)',
                'is_active' => true,
            ],
            [
                'code' => 'LUXURY_TAX',
                'name' => 'Luxury Tax',
                'description' => 'Additional tax on luxury goods and high-value items (25%)',
                'is_active' => true,
            ],
            [
                'code' => 'DIGITAL_TAX',
                'name' => 'Digital Services Tax',
                'description' => 'Tax on digital services and software licenses (15%)',
                'is_active' => true,
            ],
            [
                'code' => 'EXPORT_EXEMPT',
                'name' => 'Export Exempt',
                'description' => 'Tax exemption for export goods (0%)',
                'is_active' => true,
            ],
        ];

        foreach ($taxCodes as $taxCodeData) {
            TaxCode::create($taxCodeData);
        }

        $this->command->info('Tax codes created successfully.');
        $this->command->info('Created ' . count($taxCodes) . ' tax codes for demo purposes.');
    }
}

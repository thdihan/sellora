<?php

/**
 * Customer Seeder
 *
 * Seeds the customers table with sample data for testing and development.
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
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

/**
 * Customer Seeder Class
 *
 * Creates sample customer records for testing the customer management system.
 */
class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Clear existing customers
        DB::table('customers')->truncate();

        $customers = [
            [
                'name' => 'Ahmed Rahman',
                'shop_name' => 'Rahman Electronics',
                'phone' => '+8801712345678',
                'email' => 'ahmed.rahman@email.com',
                'full_address' => 'House 123, Road 5, Dhanmondi, Dhaka-1205',
                'notes' => 'Regular customer, prefers cash payments',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
            [
                'name' => 'Fatima Khatun',
                'shop_name' => 'Khatun Fashion House',
                'phone' => '+8801987654321',
                'email' => 'fatima.khatun@gmail.com',
                'full_address' => 'Shop 45, New Market, Dhaka-1205',
                'notes' => 'Bulk buyer, good credit history',
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'name' => 'Mohammad Ali',
                'shop_name' => null,
                'phone' => '+8801555666777',
                'email' => null,
                'full_address' => 'Village: Savar, Upazila: Savar, District: Dhaka',
                'notes' => 'Individual customer, occasional buyer',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'name' => 'Rashida Begum',
                'shop_name' => 'Begum Traders',
                'phone' => '+8801444555666',
                'email' => 'rashida.begum@yahoo.com',
                'full_address' => 'Holding 67, Ward 3, Chittagong-4000',
                'notes' => 'Wholesale customer, monthly payment cycle',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'name' => 'Karim Uddin',
                'shop_name' => 'Uddin Hardware',
                'phone' => '+8801333444555',
                'email' => 'karim.uddin@hotmail.com',
                'full_address' => 'Shop 12, Chawk Bazaar, Sylhet-3100',
                'notes' => 'Hardware specialist, prefers advance payment',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'name' => 'Nasir Ahmed',
                'shop_name' => 'Ahmed Mobile Center',
                'phone' => '+8801222333444',
                'email' => 'nasir.ahmed@gmail.com',
                'full_address' => 'Level 2, Shop 23, Bashundhara City, Dhaka-1229',
                'notes' => 'Mobile accessories dealer, fast payment',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'name' => 'Salma Akter',
                'shop_name' => null,
                'phone' => '+8801111222333',
                'email' => 'salma.akter@email.com',
                'full_address' => 'Flat 4B, Building 15, Uttara, Dhaka-1230',
                'notes' => 'Individual customer, prefers home delivery',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'name' => 'Habibur Rahman',
                'shop_name' => 'Rahman Pharmacy',
                'phone' => '+8801999888777',
                'email' => null,
                'full_address' => 'Main Road, Comilla-3500',
                'notes' => 'Pharmacy owner, medical supplies buyer',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'name' => 'Ruma Khatun',
                'shop_name' => 'Khatun Cosmetics',
                'phone' => '+8801888777666',
                'email' => 'ruma.khatun@outlook.com',
                'full_address' => 'Shop 78, Pink City, Dhaka-1207',
                'notes' => 'Cosmetics retailer, seasonal buyer',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'name' => 'Ibrahim Hossain',
                'shop_name' => 'Hossain General Store',
                'phone' => '+8801777666555',
                'email' => 'ibrahim.hossain@gmail.com',
                'full_address' => 'Village: Manikganj, Upazila: Manikganj, District: Manikganj',
                'notes' => 'General store owner, mixed product buyer',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $this->command->info('Created ' . count($customers) . ' sample customers.');
    }
}

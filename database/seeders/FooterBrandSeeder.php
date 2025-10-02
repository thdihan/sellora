<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class FooterBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $footerBrandHtml = 'Developed by <a href="https://www.webnexa.eporichoy.com" target="_blank" rel="noopener noreferrer" aria-label="Developed by WebNexa (opens in new tab)">WebNexa</a> a concern of <a href="https://www.eporichoy.com" target="_blank" rel="noopener noreferrer" aria-label="E-Porichoy website (opens in new tab)">E-Porichoy</a>';
        
        Setting::updateOrCreate(
            ['key_name' => 'footer_brand_html'],
            [
                'type' => 'html',
                'value' => $footerBrandHtml,
                'is_locked' => true,
                'locked_by_role' => 'Author'
            ]
        );
        
        Setting::updateOrCreate(
            ['key_name' => 'footer_brand_locked'],
            [
                'type' => 'boolean',
                'value' => 'true',
                'is_locked' => true,
                'locked_by_role' => 'Author'
            ]
        );
    }
}

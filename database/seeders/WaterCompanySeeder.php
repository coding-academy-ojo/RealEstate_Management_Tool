<?php

namespace Database\Seeders;

use App\Models\WaterCompany;
use Illuminate\Database\Seeder;

class WaterCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Jordan Water Company - Miyahuna',
                'name_ar' => 'شركة مياه الأردن - مياهنا',
                'website' => 'https://www.miyahuna.com.jo',
            ],
            [
                'name' => 'Yarmouk Water Company',
                'name_ar' => 'شركة مياه اليرموك',
                'website' => 'https://www.yw.com.jo',
            ],
            [
                'name' => 'Aqaba Water Company',
                'name_ar' => 'شركة مياه العقبة',
                'website' => 'https://aw.jo',
            ],
            [
                'name' => 'Wadi Araba Development Company',
                'name_ar' => 'شركة تطوير وادي عربة',
                'website' => 'https://wadidevelopment.com',
            ],
        ];

        foreach ($companies as $company) {
            WaterCompany::updateOrCreate(
                ['name' => $company['name']],
                [
                    'name_ar' => $company['name_ar'],
                    'website' => $company['website'],
                ]
            );
        }
    }
}

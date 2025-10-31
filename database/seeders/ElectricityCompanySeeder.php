<?php

namespace Database\Seeders;

use App\Models\ElectricityCompany;
use Illuminate\Database\Seeder;

class ElectricityCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Jordan Electric Power Company (JEPCO)',
                'name_ar' => 'شركة الكهرباء الأردنية',
                'website' => 'https://www.jepco.com.jo',
            ],
            [
                'name' => 'Irbid District Electricity Company (IDECO)',
                'name_ar' => 'شركة كهرباء محافظة إربد',
                'website' => 'https://www.ideco.com.jo',
            ],
            [
                'name' => 'Electricity Distribution Company (EDCO)',
                'name_ar' => 'شركة توزيع الكهرباء الأردنية',
                'website' => 'https://www.edco.jo',
            ],
            [
                'name' => 'National Electric Power Company (NEPCO)',
                'name_ar' => 'شركة الكهرباء الوطنية',
                'website' => 'https://www.nepco.com.jo',
            ],
            [
                'name' => 'Central Electricity Generating Company (CEGCO)',
                'name_ar' => 'شركة توليد الكهرباء المركزية',
                'website' => 'https://www.cegco.com.jo',
            ],
            [
                'name' => 'AES Jordan PSC',
                'name_ar' => 'شركة عمان لتوليد الكهرباء (AES)',
                'website' => 'https://www.aesjordan.com',
            ],
            [
                'name' => 'Samra Electric Power Company (SEPCO)',
                'name_ar' => 'شركة السمرا لتوليد الكهرباء',
                'website' => 'https://www.sepco.com.jo',
            ],
            [
                'name' => 'Qatrana Electric Power Company',
                'name_ar' => 'شركة القطرانة للطاقة الكهربائية',
                'website' => 'https://www.qatranaepc.com',
            ],
        ];

        foreach ($companies as $company) {
            ElectricityCompany::updateOrCreate(
                ['name' => $company['name']],
                [
                    'name_ar' => $company['name_ar'],
                    'website' => $company['website'],
                ]
            );
        }
    }
}

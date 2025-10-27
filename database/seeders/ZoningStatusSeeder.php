<?php

namespace Database\Seeders;

use App\Models\ZoningStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoningStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zoningStatuses = [
            'لا يوجد', // None
            'سكن',
            'سكن ا',
            'سكن ب',
            'سكن ج',
            'سكن د',
            'سكني',
            'سكن ريفي',
            'سكن خاص',
            'سكن ابنية عامة',
            'سكن ب باحكام خاصة',
            'مكاتب',
            'مكاتب ضمن سكن ب',
            'مكاتب ضمن سكن ب باحكام خاصة',
            'مكاتب باحكام سكن ا',
            'مكاتب - حي الدستور',
            'تجاري',
            'تجاري طولي',
            'تجاري مركزي',
            'تجاري باحكام خاصة',
            'تجاري محلي',
            'تجاري محلي ضمن سكن د',
            'تجاري محلي باحام خاصة ضمن سكن ا',
            'تجاري محلي باحام خاصة ضمن سكن ج',
            'تجاري طولي ضمن سكن ب',
            'تجاري وسكن',
            'مباني عامة',
            'مباني عامة باحكام تجاري',
            'مباني متعددة الاستعمالات',
            'صناعات متوسطة',
            'منطقة حرفية',
            'حدائق',
            'زراعي',
            'زراعي داخل حدود البلدية',
            'زراعي - خارج التنظيم',
            'خارج التنظيم',
            'الاحكام قيد الدراسة',
            'احكام منطقة قابوس - العقبة',
            'احكام محطة اتصالات',
        ];

        foreach ($zoningStatuses as $name) {
            ZoningStatus::create([
                'name_ar' => $name,
                'is_active' => true,
            ]);
        }

        $this->command->info('✓ Created ' . count($zoningStatuses) . ' zoning statuses');
    }
}

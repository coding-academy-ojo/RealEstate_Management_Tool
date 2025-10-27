<?php

namespace Database\Seeders;

use App\Models\WaterService;
use App\Models\Building;
use Illuminate\Database\Seeder;

class WaterServiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $buildings = Building::doesntHave('waterServices')->get();

    if ($buildings->isEmpty()) {
      $this->command->info('All buildings already have water services.');
      return;
    }

    // Add water services to 70% of buildings without them
    $buildingsToService = $buildings->random(min((int)($buildings->count() * 0.7), $buildings->count()));

    foreach ($buildingsToService as $building) {
      $site = $building->site;

      WaterService::create([
        'building_id' => $building->id,
        'company_name' => $this->getWaterCompany($site->governorate),
        'registration_number' => $this->generateRegistrationNumber($site->governorate, 'W'),
        'iron_number' => 'IRON-' . rand(10000, 99999),
        'previous_reading' => rand(1000, 50000) + (rand(0, 99) / 100),
        'current_reading' => rand(50000, 100000) + (rand(0, 99) / 100),
        'reading_date' => $this->generateReadingDate(),
        'invoice_file' => null,
        'payment_receipt' => null,
      ]);
    }

    $this->command->info('✓ Created water services for ' . $buildingsToService->count() . ' buildings');
    $this->command->info('  Total Water Services: ' . WaterService::count());
  }

  private function getWaterCompany($governorate): string
  {
    $companies = [
      'Miyahuna Water Company',
      'Aqaba Water Company',
      'Yarmouk Water Company',
      'Jordan Water Authority',
    ];
    return $companies[array_rand($companies)];
  }

  private function generateRegistrationNumber($governorate, $prefix): string
  {
    $year = date('Y');
    $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return "{$prefix}{$governorate}{$year}{$random}";
  }

  private function generateReadingDate(): string
  {
    $year = date('Y');
    $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    return "{$year}-{$month}-{$day}";
  }
}

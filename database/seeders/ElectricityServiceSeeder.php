<?php

namespace Database\Seeders;

use App\Models\ElectricityService;
use App\Models\Building;
use Illuminate\Database\Seeder;

class ElectricityServiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $buildings = Building::doesntHave('electricityServices')->get();

    if ($buildings->isEmpty()) {
      $this->command->info('All buildings already have electricity services.');
      return;
    }

    // Add electricity services to 80% of buildings without them
    $buildingsToService = $buildings->random(min((int)($buildings->count() * 0.8), $buildings->count()));

    foreach ($buildingsToService as $building) {
      $site = $building->site;

      ElectricityService::create([
        'building_id' => $building->id,
        'company_name' => $this->getElectricityCompany(),
        'registration_number' => $this->generateRegistrationNumber($site->governorate, 'E'),
        'previous_reading' => rand(10000, 500000) + (rand(0, 99) / 100),
        'current_reading' => rand(500000, 3000000) + (rand(0, 99) / 100),
        'reading_date' => $this->generateReadingDate(),
        'reset_file' => null,
        'remarks' => $this->generateRemarks(),
      ]);
    }

    $this->command->info('✓ Created electricity services for ' . $buildingsToService->count() . ' buildings');
    $this->command->info('  Total Electricity Services: ' . ElectricityService::count());
  }

  private function getElectricityCompany(): string
  {
    return 'Jordan Electric Power Company (JEPCO)';
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

  private function generateRemarks(): string
  {
    $remarks = [
      'Regular monthly reading',
      'High consumption - verify usage',
      'New connection activated',
      'Meter replacement scheduled',
      'Check with facility manager',
      'Solar panels installed - reduced consumption',
      'Peak demand management active',
      'Smart meter operational',
      'Backup generator available',
      'Energy audit recommended',
    ];

    return $remarks[array_rand($remarks)];
  }
}

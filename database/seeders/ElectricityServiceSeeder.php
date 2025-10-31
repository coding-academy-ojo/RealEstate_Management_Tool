<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\ElectricityService;
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

    // Add electricity services to roughly 80% of buildings without them.
    $targetCount = max(1, min((int) ($buildings->count() * 0.8), $buildings->count()));
    $buildingsToService = $buildings->random($targetCount);

    foreach ($buildingsToService as $building) {
      $site = $building->site;
      $hasSolar = random_int(0, 4) === 0; // around 20% solar-enabled

      ElectricityService::create([
        'building_id' => $building->id,
        'subscriber_name' => $this->generateSubscriberName($building),
        'meter_number' => $this->generateUniqueMeterNumber($building->id),
        'has_solar_power' => $hasSolar,
        'company_name' => $this->getElectricityCompany(),
        'registration_number' => $this->generateRegistrationNumber($site->governorate, 'E'),
        'reset_file' => null,
        'remarks' => $this->generateRemarks($hasSolar),
      ]);
    }

    $this->command->info('Created electricity services for ' . $buildingsToService->count() . ' buildings');
    $this->command->info('  Total Electricity Services: ' . ElectricityService::count());
  }

  private function getElectricityCompany(): string
  {
    return 'Jordan Electric Power Company (JEPCO)';
  }

  private function generateRegistrationNumber(?string $governorate, string $prefix): string
  {
    $year = date('Y');
    $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

    $govCode = $governorate ? strtoupper(substr($governorate, 0, 3)) : 'GEN';

    return "{$prefix}{$govCode}{$year}{$random}";
  }

  private function generateSubscriberName(Building $building): string
  {
    $names = [
      'Ahmad Al-Fayez',
      'Sara Al-Hadid',
      'Hassan Al-Rawashdeh',
      'Lina Al-Qudah',
      'Omar Al-Maharmeh',
      'Dina Al-Khasawneh',
      'Yousef Al-Saket',
      'Maysa Al-Majali',
      'Tariq Al-Hiyari',
      'Rana Al-Tarawneh',
    ];

    return $names[array_rand($names)] . ' - ' . ($building->code ?? "B{$building->id}");
  }

  private function generateUniqueMeterNumber(int $buildingId): string
  {
    do {
      $candidate = 'MTR-' . str_pad((string) random_int(1, 9_999_999), 7, '0', STR_PAD_LEFT) . '-' . $buildingId;
    } while (ElectricityService::where('meter_number', $candidate)->exists());

    return $candidate;
  }

  private function generateRemarks(bool $hasSolar): string
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

    $message = $remarks[array_rand($remarks)];

    if ($hasSolar) {
      $message .= ' | Net metering enabled';
    }

    return $message;
  }
}

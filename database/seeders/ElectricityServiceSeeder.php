<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\ElectricityCompany;
use App\Models\ElectricityService;
use Illuminate\Database\Seeder;

class ElectricityServiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $electricityCompanies = ElectricityCompany::orderBy('name')->get();

    if ($electricityCompanies->isEmpty()) {
      $this->command->warn('No electricity companies available. Skipping electricity service seeding.');
      return;
    }

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
      $company = $this->pickElectricityCompanyForGovernorate($site->governorate, $electricityCompanies);

      ElectricityService::create([
        'building_id' => $building->id,
        'subscriber_name' => $this->generateSubscriberName($building),
        'meter_number' => $this->generateUniqueMeterNumber($building->id),
        'has_solar_power' => $hasSolar,
        'electricity_company_id' => $company->id,
        'company_name' => $company->name,
        'company_name_ar' => $company->name_ar,
        'registration_number' => $this->generateRegistrationNumber($site->governorate, 'E'),
        'reset_file' => null,
        'remarks' => $this->generateRemarks($hasSolar),
      ]);
    }

    $this->command->info('Created electricity services for ' . $buildingsToService->count() . ' buildings');
    $this->command->info('  Total Electricity Services: ' . ElectricityService::count());
  }

  private function pickElectricityCompanyForGovernorate(?string $governorate, $companies): ElectricityCompany
  {
    if (!$governorate) {
      return $companies->first();
    }

    $governorate = mb_strtolower($governorate);

    $distributionMapping = [
      'amman' => 'Jordan Electric Power Company (JEPCO)',
      'zarqa' => 'Jordan Electric Power Company (JEPCO)',
      'madaba' => 'Jordan Electric Power Company (JEPCO)',
      'balqa' => 'Jordan Electric Power Company (JEPCO)',
      'irbid' => 'Irbid District Electricity Company (IDECO)',
      'jerash' => 'Irbid District Electricity Company (IDECO)',
      'ajloun' => 'Irbid District Electricity Company (IDECO)',
      'mafraq' => 'Irbid District Electricity Company (IDECO)',
      'karak' => 'Electricity Distribution Company (EDCO)',
      'tafila' => 'Electricity Distribution Company (EDCO)',
      'tafilah' => 'Electricity Distribution Company (EDCO)',
      'ma\'an' => 'Electricity Distribution Company (EDCO)',
      'maan' => 'Electricity Distribution Company (EDCO)',
      'aqaba' => 'Electricity Distribution Company (EDCO)',
      'valley' => 'Electricity Distribution Company (EDCO)',
      'wadi' => 'Electricity Distribution Company (EDCO)',
      'desert' => 'Electricity Distribution Company (EDCO)',
      'eastern' => 'Electricity Distribution Company (EDCO)',
    ];

    foreach ($distributionMapping as $needle => $companyName) {
      if (str_contains($governorate, $needle)) {
        return $companies->firstWhere('name', $companyName) ?? $companies->first();
      }
    }

    return $companies->random();
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

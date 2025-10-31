<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\WaterCompany;
use App\Models\WaterReading;
use App\Models\WaterService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class WaterServiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $waterCompanies = WaterCompany::orderBy('name')->get();

    if ($waterCompanies->isEmpty()) {
      $this->command->warn('No water companies available. Skipping water service seeding.');
      return;
    }

    $buildings = Building::doesntHave('waterServices')->get();

    if ($buildings->isEmpty()) {
      $this->command->info('All buildings already have water services.');
      return;
    }

    // Add water services to 70% of buildings without them
    $buildingsToService = $buildings->random(min((int)($buildings->count() * 0.7), $buildings->count()));

    foreach ($buildingsToService as $building) {
      $site = $building->site;
      $company = $this->pickWaterCompanyForGovernorate($site->governorate, $waterCompanies);

            $service = WaterService::create([
        'building_id' => $building->id,
        'water_company_id' => $company->id,
        'company_name' => $company->name,
        'company_name_ar' => $company->name_ar,
                'meter_owner_name' => fake()->name(),
        'registration_number' => $this->generateRegistrationNumber($site->governorate, 'W'),
        'iron_number' => 'IRON-' . rand(10000, 99999),
                'remarks' => fake()->optional()->sentence(),
                'initial_meter_image' => $this->referenceMeterImagePath(),
            ]);

            $readingCount = rand(1, 4);
            $previousReading = null;

            for ($i = $readingCount; $i >= 1; $i--) {
                $currentReading = $previousReading !== null
                    ? $previousReading + rand(10, 150) + rand(0, 99) / 100
                    : rand(500, 2500) + rand(0, 99) / 100;

                $readingDate = Carbon::now()->subMonths($i - 1)->startOfMonth();
                $consumption = $previousReading !== null
                    ? round($currentReading - $previousReading, 2)
                    : round($currentReading, 2);

                WaterReading::create([
                    'water_service_id' => $service->id,
                    'current_reading' => round($currentReading, 2),
                    'consumption_value' => $consumption,
                    'bill_amount' => $consumption ? round($consumption * 1.25, 2) : null,
                    'is_paid' => $i !== 1 ? true : (bool) rand(0, 1),
                    'reading_date' => $readingDate,
                    'notes' => $i === 1 ? 'Most recent seeded reading.' : null,
                ]);

                $previousReading = round($currentReading, 2);
            }
    }

    $this->command->info('✓ Created water services for ' . $buildingsToService->count() . ' buildings');
    $this->command->info('  Total Water Services: ' . WaterService::count());
  }

  private function pickWaterCompanyForGovernorate(?string $governorate, $companies): WaterCompany
  {
    if (!$governorate) {
      return $companies->first();
    }

    $governorate = mb_strtolower($governorate);

    $mapping = [
      'amman' => 'Jordan Water Company - Miyahuna',
      'zarqa' => 'Jordan Water Company - Miyahuna',
      'madaba' => 'Jordan Water Company - Miyahuna',
      'balqa' => 'Jordan Water Company - Miyahuna',
      'irbid' => 'Yarmouk Water Company',
      'jerash' => 'Yarmouk Water Company',
      'ajloun' => 'Yarmouk Water Company',
      'mafraq' => 'Yarmouk Water Company',
      'aqaba' => 'Aqaba Water Company',
      'ma\'an' => 'Aqaba Water Company',
      'maan' => 'Aqaba Water Company',
      'karak' => 'Aqaba Water Company',
      'tafila' => 'Aqaba Water Company',
      'tafilah' => 'Aqaba Water Company',
      'wadi araba' => 'Wadi Araba Development Company',
    ];

    foreach ($mapping as $needle => $companyName) {
      if (str_contains($governorate, $needle)) {
        return $companies->firstWhere('name', $companyName) ?? $companies->first();
      }
    }

    return $companies->random();
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

    private function referenceMeterImagePath(): string
    {
        static $cachedPath = null;

        if ($cachedPath) {
            return $cachedPath;
        }

        $directory = 'water-services/reference-meters';
        $filename = $directory . '/seed-reference-meter.jpg';

        if (!Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->makeDirectory($directory);

            $imageData = base64_decode(
                '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFwABAQEBAAAAAAAAAAAAAAAAAAECA//EABYBAQEBAAAAAAAAAAAAAAAAAAABAv/aAAwDAQACEAMQAAAA8AEf/8QAFxEAAwEAAAAAAAAAAAAAAAAAABEhMf/aAAgBAQABBQK8cn//xAAWEQADAAAAAAAAAAAAAAAAAAABEBH/2gAIAQMBAT8Bj//EABYRAQEBAAAAAAAAAAAAAAAAAAEAEf/aAAgBAgEBPwH/2Q=='
            );

            Storage::disk('public')->put($filename, $imageData);
        }

        $cachedPath = $filename;
        return $cachedPath;
    }
}

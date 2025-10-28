<?php

namespace Database\Seeders;

use App\Models\Building;
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
    $buildings = Building::doesntHave('waterServices')->get();

    if ($buildings->isEmpty()) {
      $this->command->info('All buildings already have water services.');
      return;
    }

    // Add water services to 70% of buildings without them
    $buildingsToService = $buildings->random(min((int)($buildings->count() * 0.7), $buildings->count()));

    foreach ($buildingsToService as $building) {
      $site = $building->site;

            $service = WaterService::create([
        'building_id' => $building->id,
        'company_name' => $this->getWaterCompany($site->governorate),
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
                    : null;

                WaterReading::create([
                    'water_service_id' => $service->id,
                    'previous_reading' => $previousReading,
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

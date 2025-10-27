<?php

namespace Database\Seeders;

use App\Models\ReInnovation;
use App\Models\Building;
use Illuminate\Database\Seeder;

class ReInnovationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $buildings = Building::all();

    if ($buildings->isEmpty()) {
      $this->command->warn('No buildings found. Please run BuildingSeeder first.');
      return;
    }

    // Add re-innovations to 40% of buildings
    $buildingsForInnovation = $buildings->random(min((int)($buildings->count() * 0.4), $buildings->count()));

    $innovationTypes = [
      'Energy Efficiency' => [
        'LED lighting upgrade with smart controls',
        'High-efficiency HVAC system installation',
        'Building envelope insulation improvement',
        'Energy management system implementation',
        'Variable speed drive installation for motors',
      ],
      'Renewable Energy' => [
        'Solar panel installation on rooftop',
        'Wind turbine integration',
        'Geothermal heating/cooling system',
        'Solar water heating system',
        'Hybrid solar-wind power system',
      ],
      'Water Conservation' => [
        'Rainwater harvesting system',
        'Greywater recycling for irrigation',
        'Low-flow fixture installation',
        'Water leak detection system',
        'Condensate water recovery system',
      ],
      'Smart Building' => [
        'IoT-based building automation',
        'AI-powered energy optimization',
        'Smart occupancy sensors throughout',
        'Integrated building management system',
        'Digital twin implementation',
      ],
      'Sustainability' => [
        'Green roof installation',
        'Vertical garden implementation',
        'Electric vehicle charging stations',
        'Bike storage and shower facilities',
        'Zero-waste management system',
      ],
      'Air Quality' => [
        'Advanced air filtration system',
        'Indoor air quality monitoring',
        'Natural ventilation enhancement',
        'CO2 monitoring and control system',
        'HEPA filtration in HVAC',
      ],
      'Safety & Security' => [
        'Fire suppression system upgrade',
        'Seismic retrofitting',
        'Advanced access control system',
        'CCTV and surveillance upgrade',
        'Emergency backup power system',
      ],
    ];

    $totalInnovations = 0;

    foreach ($buildingsForInnovation as $building) {
      // Add 1-4 innovations per building
      $numInnovations = rand(1, 4);

      for ($i = 0; $i < $numInnovations; $i++) {
        $type = array_rand($innovationTypes);
        $descriptions = $innovationTypes[$type];
        $description = $descriptions[array_rand($descriptions)];

        ReInnovation::create([
          'innovatable_id' => $building->id,
          'innovatable_type' => 'App\Models\Building',
          'date' => $this->generateCompletionDate(),
          'cost' => rand(10000, 800000) + (rand(0, 99) / 100),
          'name' => $type,
          'description' => $description . ' - ' . $this->generateNotes($type),
        ]);

        $totalInnovations++;
      }
    }

    $this->command->info('✓ Created ' . $totalInnovations . ' re-innovations for ' . $buildingsForInnovation->count() . ' buildings');
    $this->command->info('  Total Re-Innovations: ' . ReInnovation::count());
  }

  private function generateCompletionDate(): string
  {
    $year = rand(2023, 2024);
    $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    return "{$year}-{$month}-{$day}";
  }

  private function generateNotes($type): string
  {
    $notesByType = [
      'Energy Efficiency' => [
        'Expected 30-40% energy savings',
        'ROI estimated at 5-7 years',
        'Reduces operational costs significantly',
        'Improves building comfort levels',
      ],
      'Renewable Energy' => [
        'Reduces grid dependency',
        'Green energy certification achieved',
        'Long-term cost savings',
        'Environmental impact reduction',
      ],
      'Water Conservation' => [
        'Water usage reduced by 35%',
        'Sustainability goals achieved',
        'Lower utility bills',
        'Environmental certification earned',
      ],
      'Smart Building' => [
        'Improved operational efficiency',
        'Real-time monitoring enabled',
        'Predictive maintenance capability',
        'Enhanced occupant experience',
      ],
      'Sustainability' => [
        'LEED points contribution',
        'Biodiversity enhancement',
        'Urban heat island mitigation',
        'Community wellbeing improvement',
      ],
      'Air Quality' => [
        'Healthier indoor environment',
        'Reduces airborne contaminants',
        'Meets WHO air quality standards',
        'Improves occupant productivity',
      ],
      'Safety & Security' => [
        'Compliance with latest codes',
        'Enhanced occupant safety',
        'Insurance premium reduction',
        'Peace of mind for tenants',
      ],
    ];

    $notes = $notesByType[$type] ?? ['Project completed successfully'];
    return $notes[array_rand($notes)];
  }
}

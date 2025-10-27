<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Site;
use App\Models\Land;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $sites = Site::with('lands')->get();

    if ($sites->isEmpty()) {
      $this->command->warn('No sites found. Please run SiteSeeder first.');
      return;
    }

    // Add additional buildings to sites that have lands
    $sitesWithLands = $sites->filter(fn($site) => $site->lands->count() > 0);

    if ($sitesWithLands->isEmpty()) {
      $this->command->warn('No sites with lands found. Please run LandSeeder first.');
      return;
    }

    // Select random sites to add more buildings
    $selectedSites = $sitesWithLands->random(min(8, $sitesWithLands->count()));

    foreach ($selectedSites as $site) {
      // Add 1-3 additional buildings per site
      $numBuildings = rand(1, 3);

      for ($i = 0; $i < $numBuildings; $i++) {
        $buildingTypes = [
          'Office Building',
          'Residential Tower',
          'Warehouse Facility',
          'Retail Complex',
          'Service Center',
          'Administrative Building',
          'Storage Unit',
          'Workshop',
        ];

        $building = $site->buildings()->create([
          'name' => $buildingTypes[array_rand($buildingTypes)] . ' ' . chr(65 + $i),
          'area_m2' => rand(500, 10000) + (rand(0, 99) / 100),
          'has_building_permit' => (bool) rand(0, 1),
          'has_occupancy_permit' => (bool) rand(0, 1),
          'has_profession_permit' => (bool) rand(0, 1),
          'remarks' => $this->generateRemarks(),
        ]);

        // Attach to random land(s) from the site
        $landsToAttach = $site->lands->random(min(rand(1, 2), $site->lands->count()));
        $building->lands()->attach($landsToAttach->pluck('id')->toArray());
      }
    }

    $this->command->info('✓ Created additional buildings for ' . $selectedSites->count() . ' sites');
    $this->command->info('  Total Buildings: ' . Building::count());
  }

  private function generateRemarks(): string
  {
    $remarks = [
      'Newly constructed facility',
      'Under renovation',
      'Fully operational',
      'Pending final inspections',
      'Recently upgraded',
      'Historic building - restored',
      'Modern construction standards',
      'Energy-efficient design',
      'Multi-purpose facility',
      'Premium quality construction',
    ];

    return $remarks[array_rand($remarks)];
  }
}

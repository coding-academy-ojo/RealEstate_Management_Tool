<?php

namespace Database\Seeders;

use App\Models\Land;
use App\Models\Site;
use Illuminate\Database\Seeder;

class LandSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get all existing sites
    $sites = Site::all();

    if ($sites->isEmpty()) {
      $this->command->warn('No sites found. Please run SiteSeeder first.');
      return;
    }

    // Add additional lands to existing sites
    $sitesWithExtraLands = $sites->random(min(5, $sites->count()));

    foreach ($sitesWithExtraLands as $site) {
      // Add 1-2 additional lands per selected site
      $numLands = rand(1, 2);

      for ($i = 0; $i < $numLands; $i++) {
        $lat = 29.0 + (rand(0, 40000) / 10000);
        $lng = 35.0 + (rand(0, 10000) / 10000);

        $site->lands()->create([
          // Location Information (in order)
          'governorate' => $this->getGovernorateFullName($site->governorate),
          'directorate' => $this->getDirectorate($site->governorate),
          'directorate_number' => (string) rand(1, 50),
          'village' => 'Village ' . chr(65 + rand(0, 25)),
          'village_number' => (string) rand(100, 999),
          'basin' => 'Basin ' . chr(65 + rand(0, 10)),
          'basin_number' => (string) rand(1, 35),
          'neighborhood' => rand(0, 1) ? 'Neighborhood ' . chr(65 + rand(0, 15)) : null,
          'neighborhood_number' => rand(0, 1) ? (string) rand(1, 100) : null,
          'plot_number' => (string) rand(1000, 9999),
          'plot_key' => strtoupper(substr(md5(rand()), 0, 8)),
          // Area and other details
          'area_m2' => rand(2000, 10000),
          'region' => $this->getRegion($site->governorate),
          'zoning' => $this->getZoningType($site->zoning_status),
          'land_directorate' => $this->getGovernorateFullName($site->governorate) . ' Land Department',
          // Documents (null for seeder)
          'ownership_doc' => null,
          'site_plan' => null,
          'zoning_plan' => null,
          // Map location with extracted coordinates
          'map_location' => "https://www.google.com/maps/place/{$lat},{$lng}",
          'latitude' => $lat,
          'longitude' => $lng,
        ]);
      }
    }

    $this->command->info('✓ Created additional lands for ' . $sitesWithExtraLands->count() . ' sites');
    $this->command->info('  Total Lands: ' . Land::count());
  }

  private function getZoningType($siteZoning): string
  {
    $zoningMap = [
      'Commercial' => 'Commercial',
      'Residential' => 'Residential',
      'Residential A' => 'Residential A',
      'Industrial' => 'Industrial',
      'Industrial/Tech' => 'Technology Park',
      'Industrial Free Zone' => 'Industrial',
      'Tourism' => 'Tourism',
      'Cultural/Heritage' => 'Heritage',
      'Agricultural' => 'Agricultural',
      'Port/Logistics' => 'Port',
      'Tourism/Hospitality' => 'Tourism',
      'Environmental/Conservation' => 'Conservation',
      'Tourism/Commercial' => 'Mixed Use',
      'Mixed Use' => 'Mixed Use',
    ];

    return $zoningMap[$siteZoning] ?? 'General';
  }

  private function getGovernorateFullName($code): string
  {
    $governorateNames = [
      'AM' => 'Amman',
      'IR' => 'Irbid',
      'MF' => 'Mafraq',
      'AJ' => 'Ajloun',
      'JA' => 'Jerash',
      'BA' => 'Balqa',
      'ZA' => 'Zarqa',
      'MA' => 'Madaba',
      'AQ' => 'Aqaba',
      'KA' => 'Karak',
      'TF' => 'Tafileh',
      'MN' => 'Ma\'an',
    ];

    return $governorateNames[$code] ?? 'Unknown';
  }

  private function getDirectorate($governorateCode): string
  {
    $fullName = $this->getGovernorateFullName($governorateCode);

    $directorates = [
      'Amman' => ['Qasabat Amman', 'Al-Jami\'a', 'Wadi Al-Seer', 'Na\'ur'],
      'Irbid' => ['Qasabat Irbid', 'Bani Obeid', 'Al-Ramtha', 'Koura'],
      'Zarqa' => ['Qasabat Zarqa', 'Al-Rusaifa', 'Al-Hashimiya'],
      'Balqa' => ['Al-Salt', 'Deir Alla', 'Al-Shuna Al-Janubiya'],
      'Mafraq' => ['Qasabat Mafraq', 'Al-Badia Al-Shamaliya'],
      'Jerash' => ['Qasabat Jerash', 'Burma'],
      'Ajloun' => ['Qasabat Ajloun', 'Kofranja'],
      'Madaba' => ['Qasabat Madaba', 'Dhiban'],
      'Karak' => ['Qasabat Karak', 'Al-Mazar'],
      'Tafileh' => ['Qasabat Tafilah', 'Busayra'],
      'Ma\'an' => ['Qasabat Ma\'an', 'Petra'],
      'Aqaba' => ['Qasabat Aqaba'],
    ];

    $governorateDirectorates = $directorates[$fullName] ?? ['Main Directorate'];
    return $governorateDirectorates[array_rand($governorateDirectorates)];
  }

  private function getRegion($governorateCode): string
  {
    $fullName = $this->getGovernorateFullName($governorateCode);

    $regions = [
      'Amman' => 'Capital',
      'Irbid' => 'North',
      'Zarqa' => 'Middle',
      'Balqa' => 'Middle',
      'Mafraq' => 'North',
      'Jerash' => 'North',
      'Ajloun' => 'North',
      'Madaba' => 'Middle',
      'Karak' => 'South',
      'Tafileh' => 'South',
      'Ma\'an' => 'South',
      'Aqaba' => 'South',
    ];

    return $regions[$fullName] ?? 'Capital';
  }
}

<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Land;
use App\Models\Building;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========== REGION 1 - CAPITAL (AMMAN) ==========

        // Site 1: Amman Business Complex
        $amman1 = Site::create([
            'name' => 'Amman Business Complex',
            'governorate' => 'AM',
            'cluster_no' => 1,
            'area_m2' => 12500.00,
            'zoning_status' => 'Commercial',
            'notes' => 'Prime business location in downtown Amman',
        ]);

        $amman1_land1 = $amman1->lands()->create($this->createLandData([
            'governorate' => 'Amman',
            'directorate' => 'Qasabat Amman',
            'directorate_number' => '1',
            'village' => 'Abdali',
            'village_number' => '101',
            'basin' => 'Basin A',
            'basin_number' => '12',
            'neighborhood' => 'Business District',
            'neighborhood_number' => '5',
            'plot_number' => '456',
            'plot_key' => 'AMM01456',
            'area_m2' => 12500.00,
            'region' => 'Region 1',
            'zoning' => 'Commercial',
            'land_directorate' => 'Amman Central',
            'lat' => 31.9539,
            'lng' => 35.9106,
        ]));

        $building1 = $amman1->buildings()->create([
            'name' => 'Main Office Tower',
            'area_m2' => 8500.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Grade A office building',
        ]);
        $building1->lands()->attach($amman1_land1->id);

        // Site 2: Amman Residential Estate
        $amman2 = Site::create([
            'name' => 'Shmeisani Residential Complex',
            'governorate' => 'AM',
            'cluster_no' => 1,
            'area_m2' => 8000.00,
            'zoning_status' => 'Residential A',
            'notes' => 'Luxury residential area',
        ]);

        $amman2_land1 = $amman2->lands()->create($this->createLandData([
            'governorate' => 'Amman',
            'directorate' => 'Qasabat Amman',
            'directorate_number' => '2',
            'village' => 'Shmeisani',
            'village_number' => '102',
            'basin' => 'Basin B',
            'basin_number' => '15',
            'plot_number' => '789',
            'plot_key' => 'AMM02789',
            'area_m2' => 8000.00,
            'region' => 'Region 1',
            'zoning' => 'Residential',
            'land_directorate' => 'Amman North',
            'lat' => 31.9722,
            'lng' => 35.8992,
        ]));

        $building2 = $amman2->buildings()->create([
            'name' => 'Residential Building A',
            'area_m2' => 4500.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => false,
            'remarks' => '12-story residential tower',
        ]);
        $building2->lands()->attach($amman2_land1->id);

        // ========== REGION 2 - NORTH ==========

        // Site 3: Irbid Technology Hub
        $irbid1 = Site::create([
            'name' => 'Irbid Technology Park',
            'governorate' => 'IR',
            'cluster_no' => 1,
            'area_m2' => 15000.00,
            'zoning_status' => 'Industrial/Tech',
            'notes' => 'IT and technology companies hub',
        ]);

        $irbid1_land1 = $irbid1->lands()->create($this->createLandData([
            'governorate' => 'Irbid',
            'village' => 'University District',
            'plot_number' => '234',
            'area_m2' => 15000.00,
            'zoning' => 'Technology Park',
            'land_directorate' => 'Irbid Central',
            'lat' => 32.5556,
            'lng' => 35.8469,
        ]));

        $building3 = $irbid1->buildings()->create([
            'name' => 'Tech Center Block A',
            'area_m2' => 6000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Modern tech facility',
        ]);
        $building3->lands()->attach($irbid1_land1->id);

        $building4 = $irbid1->buildings()->create([
            'name' => 'Tech Center Block B',
            'area_m2' => 5500.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => false,
            'has_profession_permit' => true,
            'remarks' => 'Under final inspection',
        ]);
        $building4->lands()->attach($irbid1_land1->id);

        // Site 4: Mafraq Industrial Zone
        $mafraq1 = Site::create([
            'name' => 'Mafraq Free Zone',
            'governorate' => 'MF',
            'cluster_no' => 1,
            'area_m2' => 25000.00,
            'zoning_status' => 'Industrial Free Zone',
            'notes' => 'Tax-free industrial zone',
        ]);

        $mafraq1_land1 = $mafraq1->lands()->create($this->createLandData([
            'governorate' => 'Mafraq',
            'village' => 'Mafraq Industrial',
            'plot_number' => '1001',
            'area_m2' => 15000.00,
            'zoning' => 'Industrial',
            'land_directorate' => 'Mafraq Lands',
            'lat' => 32.3406,
            'lng' => 36.2084,
        ]));

        $mafraq1_land2 = $mafraq1->lands()->create($this->createLandData([
            'governorate' => 'Mafraq',
            'village' => 'Mafraq Industrial',
            'plot_number' => '1002',
            'area_m2' => 10000.00,
            'zoning' => 'Industrial',
            'land_directorate' => 'Mafraq Lands',
        ]));

        $building5 = $mafraq1->buildings()->create([
            'name' => 'Warehouse Complex 1',
            'area_m2' => 12000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Large-scale storage facility',
        ]);
        $building5->lands()->attach([$mafraq1_land1->id, $mafraq1_land2->id]);

        // Site 5: Ajloun Tourism Site
        $ajloun1 = Site::create([
            'name' => 'Ajloun Forest Resort',
            'governorate' => 'AJ',
            'cluster_no' => 1,
            'area_m2' => 10000.00,
            'zoning_status' => 'Tourism',
            'notes' => 'Eco-tourism destination',
        ]);

        $ajloun1_land1 = $ajloun1->lands()->create($this->createLandData([
            'governorate' => 'Ajloun',
            'village' => 'Ajloun Forest',
            'plot_number' => '550',
            'area_m2' => 10000.00,
            'zoning' => 'Tourism',
            'land_directorate' => 'Ajloun Department',
            'lat' => 32.3325,
            'lng' => 35.7517,
        ]));

        $building6 = $ajloun1->buildings()->create([
            'name' => 'Guest Lodge',
            'area_m2' => 3500.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => false,
            'remarks' => 'Boutique hotel facility',
        ]);
        $building6->lands()->attach($ajloun1_land1->id);

        // Site 6: Jerash Heritage Center
        $jerash1 = Site::create([
            'name' => 'Jerash Cultural Center',
            'governorate' => 'JA',
            'cluster_no' => 1,
            'area_m2' => 6000.00,
            'zoning_status' => 'Cultural/Heritage',
            'notes' => 'Near Roman ruins',
        ]);

        $jerash1_land1 = $jerash1->lands()->create($this->createLandData([
            'governorate' => 'Jerash',
            'village' => 'Jerash City',
            'basin' => '4',
            'plot_number' => '88',
            'area_m2' => 6000.00,
            'zoning' => 'Cultural',
            'land_directorate' => 'Jerash Lands',
            'map_location' => 'https://maps.google.com/?q=32.2809,35.8911',
            'lat' => 32.2809,
            'lng' => 35.8911,
        ]));

        $building7 = $jerash1->buildings()->create([
            'name' => 'Museum Building',
            'area_m2' => 2500.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Historical artifacts museum',
        ]);
        $building7->lands()->attach($jerash1_land1->id);

        // ========== REGION 3 - MIDDLE ==========

        // Site 7: Balqa Agricultural Complex
        $balqa1 = Site::create([
            'name' => 'Salt Agricultural Research Center',
            'governorate' => 'BA',
            'cluster_no' => 1,
            'area_m2' => 18000.00,
            'zoning_status' => 'Agricultural',
            'notes' => 'Research and development facility',
        ]);

        $balqa1_land1 = $balqa1->lands()->create($this->createLandData([
            'governorate' => 'Balqa',
            'village' => 'Salt',
            'basin' => '9',
            'plot_number' => '320',
            'area_m2' => 18000.00,
            'zoning' => 'Agricultural',
            'land_directorate' => 'Balqa Lands',
            'map_location' => 'https://maps.google.com/?q=32.0392,35.7272',
            'lat' => 32.0392,
            'lng' => 35.7272,
        ]));

        $building8 = $balqa1->buildings()->create([
            'name' => 'Research Laboratory',
            'area_m2' => 4000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Modern agricultural lab',
        ]);
        $building8->lands()->attach($balqa1_land1->id);

        // Site 8: Zarqa Industrial Estate
        $zarqa1 = Site::create([
            'name' => 'Zarqa Free Trade Zone',
            'governorate' => 'ZA',
            'cluster_no' => 1,
            'area_m2' => 30000.00,
            'zoning_status' => 'Industrial',
            'notes' => 'Major manufacturing hub',
        ]);

        $zarqa1_land1 = $zarqa1->lands()->create($this->createLandData([
            'governorate' => 'Zarqa',
            'village' => 'Zarqa Industrial',
            'basin' => '18',
            'plot_number' => '2001',
            'area_m2' => 30000.00,
            'zoning' => 'Industrial',
            'land_directorate' => 'Zarqa Department',
            'map_location' => 'https://maps.google.com/?q=32.0606,36.0881',
            'lat' => 32.0606,
            'lng' => 36.0881,
        ]));

        $building9 = $zarqa1->buildings()->create([
            'name' => 'Manufacturing Plant A',
            'area_m2' => 15000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Textile manufacturing',
        ]);
        $building9->lands()->attach($zarqa1_land1->id);

        // Site 9: Zarqa Residential
        $zarqa2 = Site::create([
            'name' => 'New Zarqa Housing Project',
            'governorate' => 'ZA',
            'cluster_no' => 1,
            'area_m2' => 20000.00,
            'zoning_status' => 'Residential',
            'notes' => 'Affordable housing development',
        ]);

        $zarqa2_land1 = $zarqa2->lands()->create($this->createLandData([
            'governorate' => 'Zarqa',
            'village' => 'New Zarqa',
            'basin' => '20',
            'plot_number' => '1500',
            'area_m2' => 20000.00,
            'zoning' => 'Residential',
            'land_directorate' => 'Zarqa Department',
            'lat' => 32.0728,
            'lng' => 36.1031,
        ]));

        $building10 = $zarqa2->buildings()->create([
            'name' => 'Residential Tower 1',
            'area_m2' => 7000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => false,
            'has_profession_permit' => false,
            'remarks' => 'Under construction',
        ]);
        $building10->lands()->attach($zarqa2_land1->id);

        // Site 10: Madaba Tourism
        $madaba1 = Site::create([
            'name' => 'Madaba Mosaic Center',
            'governorate' => 'MA',
            'cluster_no' => 1,
            'area_m2' => 7500.00,
            'zoning_status' => 'Tourism/Cultural',
            'notes' => 'Near Mount Nebo',
        ]);

        $madaba1_land1 = $madaba1->lands()->create($this->createLandData([
            'governorate' => 'Madaba',
            'village' => 'Madaba City',
            'basin' => '5',
            'plot_number' => '125',
            'area_m2' => 7500.00,
            'zoning' => 'Tourism',
            'land_directorate' => 'Madaba Lands',
            'map_location' => 'https://maps.google.com/?q=31.7197,35.7925',
            'lat' => 31.7197,
            'lng' => 35.7925,
        ]));

        $building11 = $madaba1->buildings()->create([
            'name' => 'Visitor Center',
            'area_m2' => 3000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Tourism information center',
        ]);
        $building11->lands()->attach($madaba1_land1->id);

        // ========== REGION 4 - SOUTH ==========

        // Site 11: Aqaba Port
        $aqaba1 = Site::create([
            'name' => 'Aqaba Port Terminal',
            'governorate' => 'AQ',
            'cluster_no' => 1,
            'area_m2' => 40000.00,
            'zoning_status' => 'Port/Logistics',
            'notes' => 'Main sea port facility',
        ]);

        $aqaba1_land1 = $aqaba1->lands()->create($this->createLandData([
            'governorate' => 'Aqaba',
            'village' => 'Aqaba Port',
            'basin' => '1',
            'plot_number' => '3000',
            'area_m2' => 40000.00,
            'zoning' => 'Port',
            'land_directorate' => 'Aqaba Special Zone',
            'map_location' => 'https://maps.google.com/?q=29.5267,35.0081',
            'lat' => 29.5267,
            'lng' => 35.0081,
        ]));

        $building12 = $aqaba1->buildings()->create([
            'name' => 'Customs Terminal',
            'area_m2' => 8000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'International cargo terminal',
        ]);
        $building12->lands()->attach($aqaba1_land1->id);

        // Site 12: Aqaba Resort
        $aqaba2 = Site::create([
            'name' => 'Red Sea Resort Complex',
            'governorate' => 'AQ',
            'cluster_no' => 1,
            'area_m2' => 22000.00,
            'zoning_status' => 'Tourism/Hospitality',
            'notes' => 'Beachfront resort',
        ]);

        $aqaba2_land1 = $aqaba2->lands()->create($this->createLandData([
            'governorate' => 'Aqaba',
            'village' => 'Aqaba South Beach',
            'basin' => '2',
            'plot_number' => '750',
            'area_m2' => 22000.00,
            'zoning' => 'Tourism',
            'land_directorate' => 'Aqaba Special Zone',
            'map_location' => 'https://maps.google.com/?q=29.5320,35.0063',
            'lat' => 29.5320,
            'lng' => 35.0063,
        ]));

        $building13 = $aqaba2->buildings()->create([
            'name' => 'Hotel Main Building',
            'area_m2' => 12000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => '5-star hotel facility',
        ]);
        $building13->lands()->attach($aqaba2_land1->id);

        // Site 13: Karak Heritage
        $karak1 = Site::create([
            'name' => 'Karak Castle Visitors Complex',
            'governorate' => 'KA',
            'cluster_no' => 1,
            'area_m2' => 5000.00,
            'zoning_status' => 'Cultural/Heritage',
            'notes' => 'Historical site',
        ]);

        $karak1_land1 = $karak1->lands()->create($this->createLandData([
            'governorate' => 'Karak',
            'village' => 'Karak City',
            'basin' => '7',
            'plot_number' => '50',
            'area_m2' => 5000.00,
            'zoning' => 'Heritage',
            'land_directorate' => 'Karak Lands',
            'map_location' => 'https://maps.google.com/?q=31.1853,35.7048',
            'lat' => 31.1853,
            'lng' => 35.7048,
        ]));

        $building14 = $karak1->buildings()->create([
            'name' => 'Heritage Museum',
            'area_m2' => 2000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Crusader castle museum',
        ]);
        $building14->lands()->attach($karak1_land1->id);

        // Site 14: Tafileh Nature Reserve
        $tafileh1 = Site::create([
            'name' => 'Dana Biosphere Center',
            'governorate' => 'TF',
            'cluster_no' => 1,
            'area_m2' => 8000.00,
            'zoning_status' => 'Environmental/Conservation',
            'notes' => 'Nature reserve headquarters',
        ]);

        $tafileh1_land1 = $tafileh1->lands()->create($this->createLandData([
            'governorate' => 'Tafileh',
            'village' => 'Dana',
            'basin' => '3',
            'plot_number' => '200',
            'area_m2' => 8000.00,
            'zoning' => 'Conservation',
            'land_directorate' => 'Tafileh Department',
            'map_location' => 'https://maps.google.com/?q=30.6697,35.6033',
            'lat' => 30.6697,
            'lng' => 35.6033,
        ]));

        $building15 = $tafileh1->buildings()->create([
            'name' => 'Eco Lodge',
            'area_m2' => 2500.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => false,
            'remarks' => 'Sustainable tourism facility',
        ]);
        $building15->lands()->attach($tafileh1_land1->id);

        // Site 15: Ma'an Desert Development
        $maan1 = Site::create([
            'name' => 'Petra Gateway Complex',
            'governorate' => 'MN',
            'cluster_no' => 1,
            'area_m2' => 15000.00,
            'zoning_status' => 'Tourism/Commercial',
            'notes' => 'Entry point to Petra',
        ]);

        $maan1_land1 = $maan1->lands()->create($this->createLandData([
            'governorate' => 'Ma\'an',
            'village' => 'Wadi Musa',
            'basin' => '6',
            'plot_number' => '400',
            'area_m2' => 15000.00,
            'zoning' => 'Tourism',
            'land_directorate' => 'Ma\'an Department',
            'map_location' => 'https://maps.google.com/?q=30.3285,35.4444',
            'lat' => 30.3285,
            'lng' => 35.4444,
        ]));

        $building16 = $maan1->buildings()->create([
            'name' => 'Tourism Services Center',
            'area_m2' => 6000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'Visitor services and shopping',
        ]);
        $building16->lands()->attach($maan1_land1->id);

        $this->command->info('✓ Created 15 comprehensive sites across all 4 regions');
        $this->command->info('  - Region 1 (Capital - AM): 2 sites');
        $this->command->info('  - Region 2 (North - IR,MF,AJ,JA): 4 sites');
        $this->command->info('  - Region 3 (Middle - BA,ZA,MA): 4 sites');
        $this->command->info('  - Region 4 (South - AQ,KA,TF,MN): 5 sites');
        $this->command->info('  - Total Lands: ' . Land::count());
        $this->command->info('  - Total Buildings: ' . Building::count());
    }

    /**
     * Generate land data with all required fields
     */
    private function createLandData(array $data): array
    {
        $lat = $data['lat'] ?? (29.0 + (rand(0, 40000) / 10000));
        $lng = $data['lng'] ?? (35.0 + (rand(0, 10000) / 10000));

        // Get governorate code for directorate lookup
        $govCode = $data['governorate'] ?? 'Amman';

        return [
            // Location Information (in order)
            'governorate' => $govCode,
            'directorate' => $data['directorate'] ?? $this->getDirectorate($govCode),
            'directorate_number' => $data['directorate_number'] ?? (string) rand(1, 50),
            'village' => $data['village'] ?? null,
            'village_number' => $data['village_number'] ?? ($data['village'] ? (string) rand(100, 999) : null),
            'basin' => $data['basin'] ?? 'Basin ' . chr(65 + rand(0, 10)),
            'basin_number' => $data['basin_number'] ?? (string) rand(1, 35),
            'neighborhood' => $data['neighborhood'] ?? null,
            'neighborhood_number' => $data['neighborhood_number'] ?? null,
            'plot_number' => $data['plot_number'],
            'plot_key' => $data['plot_key'] ?? strtoupper(substr(md5($data['plot_number']), 0, 8)),
            // Area and other details
            'area_m2' => $data['area_m2'],
            'region' => $data['region'] ?? $this->getRegion($govCode),
            'zoning' => $data['zoning'] ?? 'General',
            'land_directorate' => $data['land_directorate'] ?? $govCode . ' Land Department',
            // Map location with extracted coordinates
            'map_location' => "https://www.google.com/maps/place/{$lat},{$lng}",
            'latitude' => $lat,
            'longitude' => $lng,
        ];
    }

    private function getDirectorate($governorate): string
    {
        $directorates = [
            'Amman' => 'Qasabat Amman',
            'Irbid' => 'Qasabat Irbid',
            'Zarqa' => 'Qasabat Zarqa',
            'Balqa' => 'Al-Salt',
            'Mafraq' => 'Qasabat Mafraq',
            'Jerash' => 'Qasabat Jerash',
            'Ajloun' => 'Qasabat Ajloun',
            'Madaba' => 'Qasabat Madaba',
            'Karak' => 'Qasabat Karak',
            'Tafilah' => 'Qasabat Tafilah',
            'Ma\'an' => 'Qasabat Ma\'an',
            'Aqaba' => 'Qasabat Aqaba',
        ];

        return $directorates[$governorate] ?? 'Main Directorate';
    }

    private function getRegion($governorate): string
    {
        $regions = [
            'Amman' => 'Region 1',
            'Irbid' => 'Region 2',
            'Zarqa' => 'Region 3',
            'Balqa' => 'Region 3',
            'Mafraq' => 'Region 2',
            'Jerash' => 'Region 2',
            'Ajloun' => 'Region 2',
            'Madaba' => 'Region 3',
            'Karak' => 'Region 4',
            'Tafilah' => 'Region 4',
            'Ma\'an' => 'Region 4',
            'Aqaba' => 'Region 4',
        ];

        return $regions[$governorate] ?? 'Region 1';
    }
}

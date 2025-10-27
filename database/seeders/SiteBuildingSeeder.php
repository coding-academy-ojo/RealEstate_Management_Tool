<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\Building;

class SiteBuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Site 1: TLA' EL-ALI SWITCH SITE
        $site1 = Site::create([
            'cluster_no' => 1,
            'governorate' => 'AM', // Amman - Region 1 auto-set
            'name' => "TLA' EL-ALI SWITCH SITE",
            'area_m2' => 4007.0,
            'zoning_status' => 'سكن ا',
            'notes' => null
        ]);

        // Create land for Site 1
        $land1 = $site1->lands()->create([
            'plot_number' => '1193',
            'basin' => '7',
            'village' => "UM-DBAA'",
            'governorate' => 'Amman',
            'zoning' => 'Residential A',
        ]);

        // Buildings for Site 1
        $building1 = $site1->buildings()->create([
            'name' => "TLA' EL-ALI SWITCH AND SALES SHOP",
            'area_m2' => 4425.8,
            'has_building_permit' => true,
            'has_occupancy_permit' => false,
            'has_profession_permit' => true,
            'remarks' => 'دفاع مدني'
        ]);
        $building1->lands()->attach($land1->id);

        $building2 = $site1->buildings()->create([
            'name' => "TLA' EL-ALI SWITCH-TRANSFORMER ROOM",
            'area_m2' => 36.5,
            'has_building_permit' => null,
            'has_occupancy_permit' => null,
            'has_profession_permit' => null,
            'remarks' => null
        ]);
        $building2->lands()->attach($land1->id);

        $building3 = $site1->buildings()->create([
            'name' => "TLA' EL-ALI SWITCH-GUARDS ROOM",
            'area_m2' => 2.6,
            'has_building_permit' => null,
            'has_occupancy_permit' => null,
            'has_profession_permit' => null,
            'remarks' => null
        ]);
        $building3->lands()->attach($land1->id);

        // Site 2: AL-KHAYAM CENTRAL SITE
        $site2 = Site::create([
            'cluster_no' => 1,
            'governorate' => 'AM', // Amman - Region 1 auto-set
            'name' => 'AL-KHAYAM CENTRAL SITE',
            'area_m2' => 2118.0,
            'zoning_status' => 'تجاري مركزي',
            'notes' => null
        ]);

        // Create land for Site 2
        $land2 = $site2->lands()->create([
            'plot_number' => '97',
            'basin' => '33 المدينة',
            'village' => 'عمان -الخيام',
            'governorate' => 'Amman',
            'zoning' => 'Commercial Central',
        ]);

        // Buildings for Site 2
        $building4 = $site2->buildings()->create([
            'name' => 'AL-KHAYAM CENTRAL AND SALES SHOP',
            'area_m2' => 6668.0,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => null
        ]);
        $building4->lands()->attach($land2->id);

        $building5 = $site2->buildings()->create([
            'name' => 'AL-KHAYAM CENTRAL-BATTERIES ROOM',
            'area_m2' => 22.0,
            'has_building_permit' => null,
            'has_occupancy_permit' => null,
            'has_profession_permit' => null,
            'remarks' => null
        ]);
        $building5->lands()->attach($land2->id);

        $building6 = $site2->buildings()->create([
            'name' => 'AL-KHAYAM CENTRAL TOWER',
            'area_m2' => 0.0,
            'has_building_permit' => null,
            'has_occupancy_permit' => null,
            'has_profession_permit' => null,
            'remarks' => null
        ]);
        $building6->lands()->attach($land2->id);

        echo "Sites and Buildings created successfully!\n";
        echo "Site 1: {$site1->code} (Region {$site1->region} - {$site1->region_name}) - {$site1->name}\n";
        echo "  Lands: {$site1->lands->count()} created\n";
        echo "  Buildings: {$site1->buildings->count()} created\n";
        echo "Site 2: {$site2->code} (Region {$site2->region} - {$site2->region_name}) - {$site2->name}\n";
        echo "  Lands: {$site2->lands->count()} created\n";
        echo "  Buildings: {$site2->buildings->count()} created\n";

        // Show the automatically generated codes
        echo "\nGenerated Codes:\n";
        echo "Site 1 Code: {$site1->code}\n";
        foreach ($site1->buildings as $building) {
            echo "  Building Code: {$building->code} - {$building->name}\n";
        }

        echo "Site 2 Code: {$site2->code}\n";
        foreach ($site2->buildings as $building) {
            echo "  Building Code: {$building->code} - {$building->name}\n";
        }
    }
}

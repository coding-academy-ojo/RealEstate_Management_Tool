<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\ElectricityService;
use App\Models\Land;
use App\Models\ReInnovation;
use App\Models\Site;
use App\Models\WaterReading;
use App\Models\WaterService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $referenceImage = $this->referenceMeterImagePath();
        $readingImage = $this->readingMeterImagePath();

        $this->seedSiteOne($referenceImage, $readingImage);
        $this->seedSiteTwo($referenceImage, $readingImage);
    }

    private function seedSiteOne(string $referenceImage, string $readingImage): void
    {
        $site = Site::create([
            'name' => "TLA' EL-ALI SWITCH SITE",
            'governorate' => 'AM',
            'cluster_no' => 1,
            'area_m2' => 4007.0,
            'zoning_status' => 'سكن ا',
            'notes' => null,
        ]);

        $landMain = $site->lands()->create([
            'plot_number' => '1193',
            'basin' => '7',
            'village' => "UM-DBAA'",
            'ownership_doc' => 'site1_ownership_deed.pdf',
            'site_plan' => 'site1_site_plan.pdf',
            'zoning_plan' => 'site1_zoning_plan.pdf',
            'photos' => '20_site1_images.zip',
            'land_directorate' => 'Amman Land Directorate',
            'governorate' => 'Amman',
            'zoning' => 'Residential A',
            'map_location' => '31.9539,35.9106',
        ]);

        $landSecondary = $site->lands()->create([
            'plot_number' => '1194',
            'basin' => '7',
            'village' => "UM-DBAA'",
            'ownership_doc' => 'site1_secondary_ownership.pdf',
            'site_plan' => 'site1_secondary_siteplan.pdf',
            'zoning_plan' => 'site1_secondary_zoning.pdf',
            'photos' => null,
            'land_directorate' => 'Amman Land Directorate',
            'governorate' => 'Amman',
            'zoning' => 'Residential A',
            'map_location' => '31.9541,35.9112',
        ]);

        $buildingShop = $site->buildings()->create([
            'name' => "TLA' EL-ALI SWITCH AND SALES SHOP",
            'area_m2' => 4425.8,
            'has_building_permit' => true,
            'has_occupancy_permit' => false,
            'has_profession_permit' => true,
            'remarks' => 'دفاع مدني',
        ]);
        $buildingShop->lands()->attach([$landMain->id, $landSecondary->id]);

        $buildingBatteries = $site->buildings()->create([
            'name' => "TLA' EL-ALI SWITCH-TRANSFORMER ROOM",
            'area_m2' => 36.5,
            'has_building_permit' => null,
            'has_occupancy_permit' => null,
            'has_profession_permit' => null,
            'remarks' => null,
        ]);
        $buildingBatteries->lands()->attach($landMain->id);

        $buildingGuard = $site->buildings()->create([
            'name' => "TLA' EL-ALI SWITCH-GUARDS ROOM",
            'area_m2' => 2.6,
            'has_building_permit' => null,
            'has_occupancy_permit' => null,
            'has_profession_permit' => null,
            'remarks' => null,
        ]);
        $buildingGuard->lands()->attach($landMain->id);

        $mainWaterService = WaterService::create([
            'building_id' => $buildingShop->id,
            'company_name' => 'Miyahuna Water Company',
            'registration_number' => 'WTR-AM-2024-001',
            'iron_number' => 'IRN-123456',
            'remarks' => 'High-capacity commercial connection with backup tanks.',
            'initial_meter_image' => $referenceImage,
        ]);

        $this->seedReadings($mainWaterService, [
            [
                'date' => Carbon::now()->subMonths(2)->startOfMonth(),
                'current' => 1235.50,
                'bill' => 181.25,
                'paid' => true,
                'notes' => 'Quarterly billing cycle.',
                'meter_image' => null,
            ],
            [
                'date' => Carbon::now()->subMonth()->startOfMonth(),
                'current' => 1295.90,
                'bill' => 188.10,
                'paid' => true,
                'notes' => 'Usage increase due to seasonal demand.',
                'meter_image' => $readingImage,
            ],
        ]);

        $secondaryWaterService = WaterService::create([
            'building_id' => $buildingBatteries->id,
            'company_name' => 'Miyahuna Water Company',
            'registration_number' => 'WTR-AM-2024-002',
            'iron_number' => 'IRN-789123',
            'remarks' => 'Dedicated supply for transformer cooling system.',
            'initial_meter_image' => $referenceImage,
        ]);

        $this->seedReadings($secondaryWaterService, [
            [
                'date' => Carbon::now()->subMonths(3)->startOfMonth(),
                'current' => 410.25,
                'bill' => 50.75,
                'paid' => true,
                'notes' => null,
                'meter_image' => null,
            ],
            [
                'date' => Carbon::now()->subMonths(2)->startOfMonth(),
                'current' => 435.80,
                'bill' => 55.40,
                'paid' => true,
                'notes' => 'Routine maintenance visit.',
                'meter_image' => null,
            ],
            [
                'date' => Carbon::now()->subMonth()->startOfMonth(),
                'current' => 447.30,
                'bill' => 56.60,
                'paid' => false,
                'notes' => 'Pending invoice approval.',
                'meter_image' => $readingImage,
            ],
        ]);

        ElectricityService::create([
            'building_id' => $buildingShop->id,
            'subscriber_name' => 'Ahmed Al-Hamdan',
            'meter_number' => 'MTR-ELC-AM-001',
            'has_solar_power' => true,
            'company_name' => 'Jordan Electric Power Company',
            'registration_number' => 'ELC-AM-2024-001',
            'reset_file' => null,
            'remarks' => 'Three-phase industrial connection with generator backup and solar panels.',
        ]);

        ElectricityService::create([
            'building_id' => $buildingBatteries->id,
            'subscriber_name' => 'Omar Batteries Trading LLC',
            'meter_number' => 'MTR-ELC-AM-002',
            'has_solar_power' => false,
            'company_name' => 'Jordan Electric Power Company',
            'registration_number' => 'ELC-AM-2024-002',
            'reset_file' => null,
            'remarks' => 'High-capacity retail power supply.',
        ]);

        ReInnovation::create([
            'building_id' => $buildingShop->id,
            'type' => 'Energy Efficiency',
            'description' => 'Solar panel installation on rooftop - 250kW capacity.',
            'cost' => 180000.00,
            'date' => Carbon::parse('2024-01-10'),
            'notes' => 'Reduces electricity consumption by 35%.',
        ]);

        ReInnovation::create([
            'building_id' => $buildingShop->id,
            'type' => 'Water Conservation',
            'description' => 'Greywater recycling system for landscaping.',
            'cost' => 45000.00,
            'date' => Carbon::parse('2024-02-05'),
            'notes' => 'Saves 40% on water for irrigation.',
        ]);
    }

    private function seedSiteTwo(string $referenceImage, string $readingImage): void
    {
        $site = Site::create([
            'name' => 'AL-KHAYAM CENTRAL SITE',
            'governorate' => 'AM',
            'cluster_no' => 1,
            'area_m2' => 2118.0,
            'zoning_status' => 'تجاري مركزي',
            'notes' => null,
        ]);

        $landPrimary = $site->lands()->create([
            'plot_number' => '97',
            'basin' => '33 المدينة',
            'village' => 'عمان -الخيام',
            'ownership_doc' => 'site2_ownership_deed.pdf',
            'site_plan' => 'site2_site_plan.jpg',
            'zoning_plan' => 'site2_zoning_plan.pdf',
            'photos' => '20_site2_location_images.zip',
            'land_directorate' => 'Amman Land Directorate',
            'governorate' => 'Amman',
            'zoning' => 'Commercial Central',
            'map_location' => '31.9515,35.9239',
        ]);

        $buildingTower = $site->buildings()->create([
            'name' => 'AL-KHAYAM CENTRAL TOWER',
            'area_m2' => 15500.0,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => 'High-rise admin building with mixed-use floors.',
        ]);
        $buildingTower->lands()->attach($landPrimary->id);

        $buildingSales = $site->buildings()->create([
            'name' => 'AL-KHAYAM CENTRAL AND SALES SHOP',
            'area_m2' => 6668.0,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => null,
        ]);
        $buildingSales->lands()->attach($landPrimary->id);

        $buildingBatteries = $site->buildings()->create([
            'name' => 'AL-KHAYAM CENTRAL-BATTERIES ROOM',
            'area_m2' => 22.0,
            'has_building_permit' => null,
            'has_occupancy_permit' => null,
            'has_profession_permit' => null,
            'remarks' => null,
        ]);
        $buildingBatteries->lands()->attach($landPrimary->id);

        $towerWaterService = WaterService::create([
            'building_id' => $buildingTower->id,
            'company_name' => 'Miyahuna Water Company',
            'registration_number' => 'WTR-AM-2024-010',
            'iron_number' => 'IRN-654321',
            'remarks' => 'Mixed use tower supply with fire system connection.',
            'initial_meter_image' => $referenceImage,
        ]);

        $this->seedReadings($towerWaterService, [
            [
                'date' => Carbon::now()->subMonths(4)->startOfMonth(),
                'current' => 2210.75,
                'bill' => 302.40,
                'paid' => true,
                'notes' => 'Includes fire safety test usage.',
                'meter_image' => null,
            ],
            [
                'date' => Carbon::now()->subMonths(3)->startOfMonth(),
                'current' => 2345.10,
                'bill' => 315.80,
                'paid' => true,
                'notes' => null,
                'meter_image' => null,
            ],
            [
                'date' => Carbon::now()->subMonths(2)->startOfMonth(),
                'current' => 2433.45,
                'bill' => 325.40,
                'paid' => true,
                'notes' => 'Cooling towers peak consumption.',
                'meter_image' => $readingImage,
            ],
            [
                'date' => Carbon::now()->subMonth()->startOfMonth(),
                'current' => 2512.90,
                'bill' => 332.70,
                'paid' => false,
                'notes' => 'Pending finance approval.',
                'meter_image' => null,
            ],
        ]);

        $salesWaterService = WaterService::create([
            'building_id' => $buildingSales->id,
            'company_name' => 'Miyahuna Water Company',
            'registration_number' => 'WTR-AM-2024-011',
            'iron_number' => 'IRN-456789',
            'remarks' => 'Dedicated line for commercial kitchens and restrooms.',
            'initial_meter_image' => $referenceImage,
        ]);

        $this->seedReadings($salesWaterService, [
            [
                'date' => Carbon::now()->subMonths(2)->startOfMonth(),
                'current' => 820.10,
                'bill' => 105.25,
                'paid' => true,
                'notes' => null,
                'meter_image' => null,
            ],
            [
                'date' => Carbon::now()->subMonth()->startOfMonth(),
                'current' => 858.40,
                'bill' => 112.10,
                'paid' => false,
                'notes' => 'New restaurant opening spike.',
                'meter_image' => $readingImage,
            ],
        ]);

        ElectricityService::create([
            'building_id' => $buildingTower->id,
            'subscriber_name' => 'Khayam Tower Management',
            'meter_number' => 'MTR-ELC-AM-010',
            'has_solar_power' => false,
            'company_name' => 'Jordan Electric Power Company',
            'registration_number' => 'ELC-AM-2024-010',
            'reset_file' => null,
            'remarks' => 'Mixed-use tower with dedicated server floors.',
        ]);

        ElectricityService::create([
            'building_id' => $buildingSales->id,
            'subscriber_name' => 'Central Sales Office',
            'meter_number' => 'MTR-ELC-AM-011',
            'has_solar_power' => false,
            'company_name' => 'Jordan Electric Power Company',
            'registration_number' => 'ELC-AM-2024-011',
            'reset_file' => null,
            'remarks' => 'Retail power supply with back-office operations.',
        ]);

        ReInnovation::create([
            'building_id' => $buildingSales->id,
            'type' => 'Smart Building',
            'description' => 'IoT-based HVAC and lighting automation system.',
            'cost' => 95000.00,
            'date' => Carbon::parse('2024-03-20'),
            'notes' => 'AI-driven energy optimization.',
        ]);
    }

    private function seedReadings(WaterService $service, array $entries): void
    {
        $previous = null;

        foreach ($entries as $entry) {
            $current = round($entry['current'], 2);
            $consumption = $previous !== null
                ? max(0.0, round($current - $previous, 2))
                : round($current, 2);

            WaterReading::create([
                'water_service_id' => $service->id,
                'current_reading' => $current,
                'consumption_value' => $consumption,
                'bill_amount' => isset($entry['bill']) ? round($entry['bill'], 2) : null,
                'is_paid' => (bool) ($entry['paid'] ?? false),
                'reading_date' => $entry['date'],
                'meter_image' => $entry['meter_image'] ?? null,
                'bill_image' => $entry['bill_image'] ?? null,
                'notes' => $entry['notes'] ?? null,
            ]);

            $previous = $current;
        }
    }

    private function referenceMeterImagePath(): string
    {
        $directory = 'water-services/reference-meters';
        $filename = $directory . '/comprehensive-reference-meter.jpg';

        if (!Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->makeDirectory($directory);
            Storage::disk('public')->put($filename, $this->seedImageBinary());
        }

        return $filename;
    }

    private function readingMeterImagePath(): string
    {
        $directory = 'water-services/readings/meters';
        $filename = $directory . '/comprehensive-reading-meter.jpg';

        if (!Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->makeDirectory($directory);
            Storage::disk('public')->put($filename, $this->seedImageBinary());
        }

        return $filename;
    }

    private function seedImageBinary(): string
    {
        return base64_decode(
            '/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxISEhUQEhIVFRUVFRUVFRUVFRUVFRUVFRUWFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGhAQGi0lHyYvLS0tLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAKgBLAMBIgACEQEDEQH/xAAbAAACAgMBAAAAAAAAAAAAAAAEBQMGAAECB//EADYQAAEDAgMFBwMDAwQDAAAAAAEAAhEDIQQSMUEFUWFxBhMicYGRMqGx0RRC8FJy4fAVM2KSorLS/8QAGQEAAwEBAQAAAAAAAAAAAAAAAAECAwQF/8QAHxEAAwEAAwACAwAAAAAAAAAAAAERAhIhAzETBEEi/9oADAMBAAIRAxEAPwD0rQGgBqpoqCBFoJB7IPl3rXprFZC8yjVSNYqSjlXURyo9NnLgZyOKvZfW9ZX0UrIY0ktlZmLq2qqAhhUNvahvHGQAR3rT0FrZ4LmEEsYF3G6lgB9Dz6/Zp3nCuaRLanu+0Uc2mK0CgC6R5AYAzkdTg+u2nrRyrkYx3mIDBY7LCzE+Uj0A96VUt7R9Pt7Zknm8qF0D0kdkb7fj+1RZIpbw3RzU0o0KqhhKggEZ7/t6Vb9n1rPLY20Nw5H0SandYAR356fWiV5riCSWbHtLCo3TMCT9BR+1F74vl5bWE0E0aIVJGWKjsYIJY81mtTTSwytLYrkBLMQB13HfWmtaePJrbdW9vcglt9wmYbAnLH6fGgttdP13Y6b5ol0n0iO5PUAf8Aep+1VguS1d5XVyqE6LmOCAQMM4HTPfpXTa5uZ0dldlK5bOaLFK0aQL3IxgKBkDAOcZ4HSmurS3JXSGt7dESqzN2sATzwM4+tH6h0+yit5rT7a5dNERohUovfKZYHnPHTIp3W01C6uUpVBaQH18nedOP8AhQkWS0kN9DWy4bqQ2XDKEblUEYzjn3pFXivDfTTa1N5MNb2z+WQpt4cMSSEdcLw+w9a0Ri0to1PLK7G4igXmaF8yCF3jI9SOSM8daorS5d0L+41uQvCyt9rBGkoucg9c9T61Nl1azvUKPNJe4jtpV2xypQO4I4+mKXfqOoRi5+4a1kstpbmMAVc7h1JPHQVaxp1lZXS7KMgiMykhijeVJ+oIz6UeSQTaaXKJLWoJmY9iPUnp3/GulaEVuV5bXspUYxqAcM7iPfzTk9lJGn0s0kUscQht4HdIUR6YqpVbUNJclu7lRmRsYwM7nIPrQVqaFbp5rS7iJbmGZ4ES3ZQOAT798/pWVRpKtxHotrbp2l2cS3MlmOduVHXO01V9qku9Y5ZpuxSHPUgZzGkjPzpWeriTa7fcpS6MMrKwYIWbkgHPvUlM0qW9pcaZnuHnCbzhQMg9PXp+dQGrfcbUdVweNiQP8zbg/MMkA84yPbpQtfULu7mfTrYzKGidecbQktyc+pJ5z0p1pcz6jbiW2yBESRogZYHGTjk0i0LzWzS7pZLyxZpVVIBJGBjn0I/pR0ul2O61CxXMDcE7jdyc5x6/pTmyW6S3Vp5XlpmUBo4CnBAwCT36VGrV5bXdyO5FcLwnM6ZMkHkgccZ7elH7Pb69fpyyx6jdzbJpDHH7oI7D7d6VOlzjMuNSaTzIDAA9PfPWmgah+tv7QFtbcRiO62txGQgGJD4Ocdq1madqJtNArbQJbtwElQ9QcEjvg8fWgCPU7LTrW6vZ3LZnrpG12yIbSSed2e9TMhkt7d7QXEsliMlbceRj+6xzxmo6ddPbdm3xF1HBGDRQMHc4ySD3q149jVrqF1JbS5triMYP8xH78dM0FoAagBQA0BoAaAGgBoAaAGgBoAaAGgBoAaAGgBoAaAGgP/Z'
        );
    }
}<?php<?php



namespace Database\Seeders;namespace Database\Seeders;



use App\Models\Site;use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Land;use Illuminate\Database\Seeder;

use App\Models\Building;use App\Models\Site;

use App\Models\WaterService;use App\Models\Land;

use App\Models\ElectricityService;use App\Models\Building;

use App\Models\ReInnovation;use App\Models\WaterService;

use Illuminate\Database\Seeder;use App\Models\ElectricityService;

use App\Models\ReInnovation;

class ComprehensiveSeeder extends Seeder

{class ComprehensiveSeeder extends Seeder

    /**{

     * Run the database seeds with full relationships    /**

     */     * Run the database seeds.

    public function run(): void     */

    {    public function run(): void

        // ========== COMPREHENSIVE SITE 1: AMMAN MEGA PROJECT ==========    {

                // Site 1: TLA' EL-ALI SWITCH SITE

        $site1 = Site::create([        $site1 = Site::create([

            'name' => 'Abdoun Business District',            'cluster_no' => 1,

            'governorate' => 'AM',            'governorate' => 'AM', // Amman

            'cluster_no' => 2,            'name' => "TLA' EL-ALI SWITCH SITE",

            'area_m2' => 35000.00,            'area_m2' => 4007.0,

            'zoning_status' => 'Mixed Commercial',            'zoning_status' => 'سكن ا',

            'notes' => 'Premium business district with luxury amenities',            'notes' => null

        ]);        ]);



        // Create 3 lands for this site        // Lands for Site 1

        $land1_1 = $site1->lands()->create([        $land1 = $site1->lands()->create([

            'plot_number' => '101',            'plot_number' => '1193',

            'basin' => '25',            'basin' => '7',

            'village' => 'Abdoun',            'village' => "UM-DBAA'",

            'governorate' => 'Amman',            'ownership_doc' => 'site1_ownership_deed.pdf',

            'zoning' => 'Commercial A',            'site_plan' => 'site1_site_plan.pdf',

            'land_directorate' => 'Amman South',            'zoning_plan' => 'site1_zoning_plan.pdf',

            'map_location' => 'https://maps.google.com/?q=31.9400,35.8700',            'photos' => '20_site1_images.zip',

        ]);            'land_directorate' => 'Amman Land Directorate',

            'governorate' => 'Amman',

        $land1_2 = $site1->lands()->create([            'zoning' => 'Residential A',

            'plot_number' => '102',            'map_location' => '31.9539,35.9106'

            'basin' => '25',        ]);

            'village' => 'Abdoun',

            'governorate' => 'Amman',        // Buildings for Site 1

            'zoning' => 'Commercial A',        $building1 = $site1->buildings()->create([

            'land_directorate' => 'Amman South',            'name' => "TLA' EL-ALI SWITCH AND SALES SHOP",

            'map_location' => 'https://maps.google.com/?q=31.9405,35.8705',            'area_m2' => 4425.8,

        ]);            'has_building_permit' => true,

            'has_occupancy_permit' => false,

        $land1_3 = $site1->lands()->create([            'has_profession_permit' => true,

            'plot_number' => '103',            'as_built_drawing' => null,

            'basin' => '25',            'remarks' => 'دفاع مدني'

            'village' => 'Abdoun',        ]);

            'governorate' => 'Amman',

            'zoning' => 'Green Space',        $building2 = $site1->buildings()->create([

            'land_directorate' => 'Amman South',            'name' => "TLA' EL-ALI SWITCH-TRANSFORMER ROOM",

            'map_location' => 'https://maps.google.com/?q=31.9410,35.8710',            'area_m2' => 36.5,

        ]);            'has_building_permit' => null,

            'has_occupancy_permit' => null,

        // Building 1: Office Tower on lands 1 & 2            'has_profession_permit' => null,

        $building1_1 = $site1->buildings()->create([            'as_built_drawing' => null,

            'name' => 'Abdoun Tower A - Corporate Offices',            'remarks' => null

            'area_m2' => 15000.00,        ]);

            'has_building_permit' => true,

            'has_occupancy_permit' => true,        $building3 = $site1->buildings()->create([

            'has_profession_permit' => true,            'name' => "TLA' EL-ALI SWITCH-GUARDS ROOM",

            'remarks' => '25-floor premium office building with underground parking',            'area_m2' => 2.6,

        ]);            'has_building_permit' => null,

        $building1_1->lands()->attach([$land1_1->id, $land1_2->id]);            'has_occupancy_permit' => null,

            'has_profession_permit' => null,

        // Water service for building 1            'as_built_drawing' => null,

        WaterService::create([            'remarks' => null

            'building_id' => $building1_1->id,        ]);

            'subscriber_number' => 'WTR-AM-2024-001',

            'annual_consumption_m3' => 12500.00,        // Attach buildings to land

            'has_network_permit' => true,        $building1->lands()->attach($land1->id);

            'has_connection_permit' => true,        $building2->lands()->attach($land1->id);

            'connection_date' => '2023-03-15',        $building3->lands()->attach($land1->id);

            'notes' => 'High-capacity commercial connection with backup tanks',

        ]);        // Water service for building 1

        $building1->waterServices()->create([

        // Electricity service for building 1            'company_name' => 'Amman Water Company',

        ElectricityService::create([            'registration_number' => 'WTR-001-2024',

            'building_id' => $building1_1->id,            'iron_number' => 'IRN-123456',

            'subscriber_number' => 'ELEC-AM-2024-001',            'previous_reading' => 1250.50,

            'annual_consumption_kwh' => 850000.00,            'current_reading' => 1275.25,

            'has_network_permit' => true,            'reading_date' => now()->subDays(30),

            'has_connection_permit' => true,            'invoice_file' => null,

            'connection_date' => '2023-03-20',            'payment_receipt' => null

            'notes' => 'Three-phase industrial connection with generator backup',        ]);

        ]);

        // Electricity service for building 1

        // Re-innovations for building 1        $building1->electricityServices()->create([

        ReInnovation::create([            'company_name' => 'Jordan Electric Power Company',

            'building_id' => $building1_1->id,            'registration_number' => 'ELC-001-2024',

            'type' => 'Energy Efficiency',            'previous_reading' => 8500.00,

            'description' => 'Solar panel installation on rooftop - 250kW capacity',            'current_reading' => 8750.50,

            'cost' => 180000.00,            'reading_date' => now()->subDays(30),

            'completion_date' => '2024-01-10',            'reset_file' => null,

            'notes' => 'Reduces electricity consumption by 35%',            'remarks' => null

        ]);        ]);



        ReInnovation::create([        // Site 2: AL-KHAYAM CENTRAL SITE

            'building_id' => $building1_1->id,        $site2 = Site::create([

            'type' => 'Water Conservation',            'cluster_no' => 1,

            'description' => 'Greywater recycling system for landscaping',            'governorate' => 'AM', // Amman

            'cost' => 45000.00,            'name' => 'AL-KHAYAM CENTRAL SITE',

            'completion_date' => '2024-02-05',            'area_m2' => 2118.0,

            'notes' => 'Saves 40% on water for irrigation',            'zoning_status' => 'تجاري مركزي',

        ]);            'notes' => null

        ]);

        // Building 2: Retail Complex on land 1

        $building1_2 = $site1->buildings()->create([        // Lands for Site 2

            'name' => 'Abdoun Galleria - Retail Center',        $land2 = $site2->lands()->create([

            'area_m2' => 8500.00,            'plot_number' => '97',

            'has_building_permit' => true,            'basin' => '33 المدينة',

            'has_occupancy_permit' => true,            'village' => 'عمان -الخيام',

            'has_profession_permit' => true,            'ownership_doc' => 'site2_ownership_deed.pdf',

            'remarks' => 'Luxury retail complex with restaurants and cafes',            'site_plan' => 'site2_site_plan.jpg',

        ]);            'zoning_plan' => 'site2_zoning_plan.pdf',

        $building1_2->lands()->attach($land1_1->id);            'photos' => '20_site2_location_images.zip',

            'land_directorate' => 'Amman Land Directorate',

        WaterService::create([            'governorate' => 'Amman',

            'building_id' => $building1_2->id,            'zoning' => 'Commercial Central',

            'subscriber_number' => 'WTR-AM-2024-002',            'map_location' => '31.9515,35.9239'

            'annual_consumption_m3' => 8000.00,        ]);

            'has_network_permit' => true,

            'has_connection_permit' => true,        // Buildings for Site 2

            'connection_date' => '2023-05-10',        $building4 = $site2->buildings()->create([

            'notes' => 'Commercial water supply with fountain features',            'name' => 'AL-KHAYAM CENTRAL AND SALES SHOP',

        ]);            'area_m2' => 6668.0,

            'has_building_permit' => true,

        ElectricityService::create([            'has_occupancy_permit' => true,

            'building_id' => $building1_2->id,            'has_profession_permit' => true,

            'subscriber_number' => 'ELEC-AM-2024-002',            'as_built_drawing' => null,

            'annual_consumption_kwh' => 520000.00,            'remarks' => null

            'has_network_permit' => true,        ]);

            'has_connection_permit' => true,

            'connection_date' => '2023-05-15',        $building5 = $site2->buildings()->create([

            'notes' => 'High-capacity retail power supply',            'name' => 'AL-KHAYAM CENTRAL-BATTERIES ROOM',

        ]);            'area_m2' => 22.0,

            'has_building_permit' => null,

        ReInnovation::create([            'has_occupancy_permit' => null,

            'building_id' => $building1_2->id,            'has_profession_permit' => null,

            'type' => 'Smart Building',            'as_built_drawing' => null,

            'description' => 'IoT-based HVAC and lighting automation system',            'remarks' => null

            'cost' => 95000.00,        ]);

            'completion_date' => '2024-03-20',

            'notes' => 'AI-driven energy optimization',        $building6 = $site2->buildings()->create([

        ]);            'name' => 'AL-KHAYAM CENTRAL TOWER',

            'area_m2' => 0.0,

        // Building 3: Public Park Facility on land 3            'has_building_permit' => null,

        $building1_3 = $site1->buildings()->create([            'has_occupancy_permit' => null,

            'name' => 'Community Pavilion',            'has_profession_permit' => null,

            'area_m2' => 1200.00,            'as_built_drawing' => null,

            'has_building_permit' => true,            'remarks' => null

            'has_occupancy_permit' => true,        ]);

            'has_profession_permit' => false,

            'remarks' => 'Public amenity building in green space',        // Attach buildings to land

        ]);        $building4->lands()->attach($land2->id);

        $building1_3->lands()->attach($land1_3->id);        $building5->lands()->attach($land2->id);

        $building6->lands()->attach($land2->id);

        WaterService::create([

            'building_id' => $building1_3->id,        // Multiple water services for building 4

            'subscriber_number' => 'WTR-AM-2024-003',        $building4->waterServices()->create([

            'annual_consumption_m3' => 2500.00,            'company_name' => 'Amman Water Company',

            'has_network_permit' => true,            'registration_number' => 'WTR-002-2024',

            'has_connection_permit' => true,            'iron_number' => 'IRN-789123',

            'connection_date' => '2023-06-01',            'previous_reading' => 2500.00,

            'notes' => 'Park irrigation and facility use',            'current_reading' => 2650.75,

        ]);            'reading_date' => now()->subDays(15),

            'invoice_file' => null,

        ElectricityService::create([            'payment_receipt' => null

            'building_id' => $building1_3->id,        ]);

            'subscriber_number' => 'ELEC-AM-2024-003',

            'annual_consumption_kwh' => 45000.00,        $building4->waterServices()->create([

            'has_network_permit' => true,            'company_name' => 'Amman Water Company',

            'has_connection_permit' => true,            'registration_number' => 'WTR-003-2024',

            'connection_date' => '2023-06-05',            'iron_number' => 'IRN-456789',

            'notes' => 'Park lighting and amenities',            'previous_reading' => 1800.50,

        ]);            'current_reading' => 1925.25,

            'reading_date' => now()->subDays(15),

        // ========== COMPREHENSIVE SITE 2: ZARQA INDUSTRIAL COMPLEX ==========            'invoice_file' => null,

                    'payment_receipt' => null

        $site2 = Site::create([        ]);

            'name' => 'Zarqa Advanced Manufacturing Hub',

            'governorate' => 'ZA',        // Multiple electricity services for building 4

            'cluster_no' => 2,        $building4->electricityServices()->create([

            'area_m2' => 45000.00,            'company_name' => 'Jordan Electric Power Company',

            'zoning_status' => 'Heavy Industrial',            'registration_number' => 'ELC-002-2024',

            'notes' => 'State-of-the-art industrial facility with green initiatives',            'previous_reading' => 15000.00,

        ]);            'current_reading' => 15500.50,

            'reading_date' => now()->subDays(15),

        // Create 2 industrial lands            'reset_file' => null,

        $land2_1 = $site2->lands()->create([            'remarks' => null

            'plot_number' => '5001',        ]);

            'basin' => '30',

            'village' => 'Zarqa Industrial City',        $building4->electricityServices()->create([

            'governorate' => 'Zarqa',            'company_name' => 'Jordan Electric Power Company',

            'zoning' => 'Industrial Heavy',            'registration_number' => 'ELC-003-2024',

            'land_directorate' => 'Zarqa Industrial Authority',            'previous_reading' => 5000.00,

            'map_location' => 'https://maps.google.com/?q=32.0700,36.0950',            'current_reading' => 5250.25,

        ]);            'reading_date' => now()->subDays(15),

            'reset_file' => null,

        $land2_2 = $site2->lands()->create([            'remarks' => 'Check with Hashim Zahari'

            'plot_number' => '5002',        ]);

            'basin' => '30',

            'village' => 'Zarqa Industrial City',        // Re-Innovation data for Sites, Lands, and Buildings

            'governorate' => 'Zarqa',

            'zoning' => 'Industrial Heavy',        // Site 1 Re-innovations

            'land_directorate' => 'Zarqa Industrial Authority',        $site1->reInnovations()->create([

            'map_location' => 'https://maps.google.com/?q=32.0705,36.0955',            'date' => now()->subDays(30),

        ]);            'cost' => 15000.00,

            'name' => 'Security System Upgrade',

        // Building 1: Main Factory            'description' => 'Installation of new CCTV cameras and access control system for TLA EL-ALI site'

        $building2_1 = $site2->buildings()->create([        ]);

            'name' => 'Production Facility Alpha',

            'area_m2' => 25000.00,        $site1->reInnovations()->create([

            'has_building_permit' => true,            'date' => now()->subDays(15),

            'has_occupancy_permit' => true,            'cost' => 8500.00,

            'has_profession_permit' => true,            'name' => 'Network Infrastructure',

            'remarks' => 'Automated manufacturing plant with robotic assembly lines',            'description' => 'Fiber optic cable installation and network equipment upgrade'

        ]);        ]);

        $building2_1->lands()->attach([$land2_1->id, $land2_2->id]);

        // Land 1 Re-innovations

        WaterService::create([        $land1->reInnovations()->create([

            'building_id' => $building2_1->id,            'date' => now()->subDays(45),

            'subscriber_number' => 'WTR-ZA-2024-001',            'cost' => 12000.00,

            'annual_consumption_m3' => 35000.00,            'name' => 'Land Surveying Update',

            'has_network_permit' => true,            'description' => 'Professional land survey with GPS coordinates and boundary marking'

            'has_connection_permit' => true,        ]);

            'connection_date' => '2023-01-15',

            'notes' => 'Industrial water supply with treatment plant',        // Building 1 Re-innovations

        ]);        $building1->reInnovations()->create([

            'date' => now()->subDays(20),

        ElectricityService::create([            'cost' => 25000.00,

            'building_id' => $building2_1->id,            'name' => 'HVAC System Renovation',

            'subscriber_number' => 'ELEC-ZA-2024-001',            'description' => 'Complete air conditioning system replacement with energy-efficient units'

            'annual_consumption_kwh' => 2500000.00,        ]);

            'has_network_permit' => true,

            'has_connection_permit' => true,        $building1->reInnovations()->create([

            'connection_date' => '2023-01-10',            'date' => now()->subDays(10),

            'notes' => 'High-voltage industrial power - dedicated transformer',            'cost' => 5500.00,

        ]);            'name' => 'Electrical Panel Upgrade',

            'description' => 'Main electrical panel replacement and circuit breaker updates'

        ReInnovation::create([        ]);

            'building_id' => $building2_1->id,

            'type' => 'Renewable Energy',        // Site 2 Re-innovations

            'description' => 'On-site solar farm - 1MW capacity with battery storage',        $site2->reInnovations()->create([

            'cost' => 750000.00,            'date' => now()->subDays(25),

            'completion_date' => '2024-04-01',            'cost' => 18000.00,

            'notes' => 'Provides 30% of facility power needs',            'name' => 'Parking Area Expansion',

        ]);            'description' => 'Extension of parking area with proper lighting and drainage system'

        ]);

        ReInnovation::create([

            'building_id' => $building2_1->id,        // Building 4 Re-innovations

            'type' => 'Waste Management',        $building4->reInnovations()->create([

            'description' => 'Zero-waste manufacturing process implementation',            'date' => now()->subDays(35),

            'cost' => 320000.00,            'cost' => 32000.00,

            'completion_date' => '2024-05-15',            'name' => 'Building Facade Renovation',

            'notes' => '95% waste recycling rate achieved',            'description' => 'Complete exterior building renovation including painting and window replacement'

        ]);        ]);



        // Building 2: Warehouse        $building4->reInnovations()->create([

        $building2_2 = $site2->buildings()->create([            'date' => now()->subDays(5),

            'name' => 'Logistics & Storage Complex',            'cost' => 7200.00,

            'area_m2' => 12000.00,            'name' => 'Fire Safety System',

            'has_building_permit' => true,            'description' => 'Installation of fire detection and suppression system'

            'has_occupancy_permit' => true,        ]);

            'has_profession_permit' => true,

            'remarks' => 'Temperature-controlled storage with automated inventory',        echo "Comprehensive data created successfully!\n";

        ]);        echo "Sites: " . Site::count() . " created\n";

        $building2_2->lands()->attach($land2_2->id);        echo "Lands: " . Land::count() . " created\n";

        echo "Buildings: " . Building::count() . " created\n";

        WaterService::create([        echo "Water Services: " . WaterService::count() . " created\n";

            'building_id' => $building2_2->id,        echo "Electricity Services: " . ElectricityService::count() . " created\n";

            'subscriber_number' => 'WTR-ZA-2024-002',        echo "Re-Innovations: " . ReInnovation::count() . " created\n";

            'annual_consumption_m3' => 3500.00,

            'has_network_permit' => true,        // Show the automatically generated codes

            'has_connection_permit' => true,        echo "\nGenerated Codes:\n";

            'connection_date' => '2023-02-01',        foreach (Site::with(['buildings', 'lands'])->get() as $site) {

            'notes' => 'Basic facility and fire suppression system',            echo "Site: {$site->code} - {$site->name}\n";

        ]);            echo "  Lands: {$site->lands->count()}\n";

            echo "  Buildings: {$site->buildings->count()}\n";

        ElectricityService::create([            foreach ($site->buildings as $building) {

            'building_id' => $building2_2->id,                echo "    Building: {$building->code} - {$building->name}\n";

            'subscriber_number' => 'ELEC-ZA-2024-002',                echo "      Water Services: {$building->waterServices->count()}\n";

            'annual_consumption_kwh' => 450000.00,                echo "      Electricity Services: {$building->electricityServices->count()}\n";

            'has_network_permit' => true,            }

            'has_connection_permit' => true,            echo "\n";

            'connection_date' => '2023-02-01',        }

            'notes' => 'Climate control and automated systems',    }

        ]);}


        ReInnovation::create([
            'building_id' => $building2_2->id,
            'type' => 'Energy Efficiency',
            'description' => 'LED lighting retrofit with motion sensors',
            'cost' => 55000.00,
            'completion_date' => '2024-06-10',
            'notes' => '60% reduction in lighting energy use',
        ]);

        // ========== COMPREHENSIVE SITE 3: AQABA RESORT DEVELOPMENT ==========

        $site3 = Site::create([
            'name' => 'Tala Bay Luxury Resort',
            'governorate' => 'AQ',
            'cluster_no' => 2,
            'area_m2' => 28000.00,
            'zoning_status' => 'Tourism Premium',
            'notes' => 'Eco-friendly beachfront resort with LEED certification',
        ]);

        $land3_1 = $site3->lands()->create([
            'plot_number' => '800',
            'basin' => '5',
            'village' => 'Tala Bay',
            'governorate' => 'Aqaba',
            'zoning' => 'Tourism Resort',
            'land_directorate' => 'ASEZA - Aqaba Special Economic Zone',
            'map_location' => 'https://maps.google.com/?q=29.4500,35.0000',
        ]);

        $building3_1 = $site3->buildings()->create([
            'name' => 'Resort Main Hotel Building',
            'area_m2' => 18000.00,
            'has_building_permit' => true,
            'has_occupancy_permit' => true,
            'has_profession_permit' => true,
            'remarks' => '200-room luxury hotel with spa and conference center',
        ]);
        $building3_1->lands()->attach($land3_1->id);

        WaterService::create([
            'building_id' => $building3_1->id,
            'subscriber_number' => 'WTR-AQ-2024-001',
            'annual_consumption_m3' => 45000.00,
            'has_network_permit' => true,
            'has_connection_permit' => true,
            'connection_date' => '2022-11-01',
            'notes' => 'Desalinated water supply with backup storage',
        ]);

        ElectricityService::create([
            'building_id' => $building3_1->id,
            'subscriber_number' => 'ELEC-AQ-2024-001',
            'annual_consumption_kwh' => 1200000.00,
            'has_network_permit' => true,
            'has_connection_permit' => true,
            'connection_date' => '2022-11-01',
            'notes' => 'High-capacity hospitality power with UPS backup',
        ]);

        ReInnovation::create([
            'building_id' => $building3_1->id,
            'type' => 'Sustainability',
            'description' => 'Seawater heat pump system for heating/cooling',
            'cost' => 425000.00,
            'completion_date' => '2024-07-01',
            'notes' => 'Uses Red Sea water for HVAC - 50% energy savings',
        ]);

        ReInnovation::create([
            'building_id' => $building3_1->id,
            'type' => 'Water Conservation',
            'description' => 'Rainwater harvesting and greywater recycling',
            'cost' => 180000.00,
            'completion_date' => '2024-08-15',
            'notes' => 'LEED Gold certification achieved',
        ]);

        ReInnovation::create([
            'building_id' => $building3_1->id,
            'type' => 'Renewable Energy',
            'description' => 'Rooftop and parking shade solar arrays - 500kW',
            'cost' => 380000.00,
            'completion_date' => '2024-09-01',
            'notes' => 'Covers 40% of hotel electricity needs',
        ]);

        $this->command->info('✓ Created 3 comprehensive sites with full relationships:');
        $this->command->info('  Site 1 (Amman): 3 lands, 3 buildings, 9 services/innovations');
        $this->command->info('  Site 2 (Zarqa): 2 lands, 2 buildings, 7 services/innovations');
        $this->command->info('  Site 3 (Aqaba): 1 land, 1 building, 5 services/innovations');
        $this->command->info('  TOTALS:');
        $this->command->info('    - Sites: ' . Site::count());
        $this->command->info('    - Lands: ' . Land::count());
        $this->command->info('    - Buildings: ' . Building::count());
        $this->command->info('    - Water Services: ' . WaterService::count());
        $this->command->info('    - Electricity Services: ' . ElectricityService::count());
        $this->command->info('    - Re-Innovations: ' . ReInnovation::count());
    }
}

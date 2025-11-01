<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        // Call seeders in proper order (respecting dependencies)
        $this->call([
            UserSeeder::class,              // Independent - create users first
            ZoningStatusSeeder::class,      // Create zoning statuses before sites
            WaterCompanySeeder::class,      // Seed master list of water utilities
            ElectricityCompanySeeder::class, // Seed master list of electric utilities
            SiteSeeder::class,              // Creates sites with lands and buildings
            LandSeeder::class,              // Adds additional lands to existing sites
            BuildingSeeder::class,          // Adds additional buildings to sites
            WaterServiceSeeder::class,      // Adds water services to buildings
            ElectricityServiceSeeder::class, // Adds electricity services to buildings
            ElectricReadingSeeder::class,   // Adds electricity readings (after services)
            RenovationSeeder::class,      // Adds renovations to buildings
        ]);

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->displaySummary();
    }

    private function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->info('📊 Database Summary:');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Users', \App\Models\User::count()],
                ['Zoning Statuses', \App\Models\ZoningStatus::count()],
                ['Water Companies', \App\Models\WaterCompany::count()],
                ['Electricity Companies', \App\Models\ElectricityCompany::count()],
                ['Sites', \App\Models\Site::count()],
                ['Lands', \App\Models\Land::count()],
                ['Buildings', \App\Models\Building::count()],
                ['Water Services', \App\Models\WaterService::count()],
                ['Electricity Services', \App\Models\ElectricityService::count()],
                ['Electric Readings', \App\Models\ElectricReading::count()],
                ['Renovations', \App\Models\Renovation::count()],
            ]
        );
    }
}

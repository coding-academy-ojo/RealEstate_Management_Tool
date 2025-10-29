<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // Super administrator with full access (only one exists)
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@realstate.com',
            'email_verified_at' => now(),
            'password' => $password,
            'role' => 'super_admin',
            'privileges' => null,
        ]);

        // Admin user with full access (except user management)
        User::create([
            'name' => 'General Administrator',
            'email' => 'generaladmin@realstate.com',
            'email_verified_at' => now(),
            'password' => $password,
            'role' => 'admin',
            'privileges' => null,
        ]);

        // Engineers with scoped privileges
        $privilegeLabels = [
            'sites_lands_buildings' => 'Sites, Lands & Buildings',
            'water' => 'Water Services',
            'electricity' => 'Electricity Services',
            'renovation' => 'Renovations',
        ];

        $engineers = [
            [
                'name' => 'Water Utilities Engineer',
                'email' => 'water@realstate.com',
                'privileges' => ['water'],
            ],
            [
                'name' => 'Electricity Compliance Engineer',
                'email' => 'electricity@realstate.com',
                'privileges' => ['electricity'],
            ],
            [
                'name' => 'Renovation Projects Engineer',
                'email' => 'renovation@realstate.com',
                'privileges' => ['renovation'],
            ],
            [
                'name' => 'Utilities Lead Engineer',
                'email' => 'utilities@realstate.com',
                'privileges' => ['water', 'electricity'],
            ],
            [
                'name' => 'Comprehensive Projects Engineer',
                'email' => 'projects@realstate.com',
                'privileges' => ['water', 'electricity', 'renovation'],
            ],
            [
                'name' => 'Estate Portfolio Engineer',
                'email' => 'estate@realstate.com',
                'privileges' => ['sites_lands_buildings'],
            ],
        ];

        foreach ($engineers as $engineer) {
            User::create([
                'name' => $engineer['name'],
                'email' => $engineer['email'],
                'email_verified_at' => now(),
                'password' => $password,
                'role' => 'engineer',
                'privileges' => $engineer['privileges'],
            ]);
        }

        $this->command->info('✓ Created ' . User::count() . ' users');
        $this->command->info('  - Super Admin: admin@realstate.com');
        $this->command->info('  - Admin: generaladmin@realstate.com');
        $this->command->info('  - Engineers:');
        foreach ($engineers as $engineer) {
            $privilegeSummary = collect($engineer['privileges'])
                ->map(fn($privilege) => $privilegeLabels[$privilege] ?? $privilege)
                ->implode(', ');

            if (in_array('sites_lands_buildings', $engineer['privileges'], true)) {
                $privilegeSummary = 'Full Access';
            }

            $this->command->info('      * ' . $engineer['email'] . ' [' . $privilegeSummary . ']');
        }
        $this->command->info('  Default password for all users: password');
    }
}

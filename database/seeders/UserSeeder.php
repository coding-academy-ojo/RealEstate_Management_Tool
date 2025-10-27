<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Create admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@realstate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create manager user
        User::create([
            'name' => 'Site Manager',
            'email' => 'manager@realstate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create viewer user
        User::create([
            'name' => 'Property Viewer',
            'email' => 'viewer@realstate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create additional staff users
        $staffNames = [
            'Ahmad Al-Khaldi',
            'Fatima Hassan',
            'Mohammed Jamal',
            'Layla Ibrahim',
            'Omar Mansour',
        ];

        foreach ($staffNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => 'staff' . ($index + 1) . '@realstate.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
        }

        $this->command->info('✓ Created ' . User::count() . ' users');
        $this->command->info('  - Admin: admin@realstate.com');
        $this->command->info('  - Manager: manager@realstate.com');
        $this->command->info('  - Viewer: viewer@realstate.com');
        $this->command->info('  - Staff: staff1@realstate.com to staff5@realstate.com');
        $this->command->info('  Default password for all users: password');
    }
}

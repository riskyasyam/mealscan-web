<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // Create additional test admin if needed
        User::firstOrCreate(
            ['email' => 'admin@sims.com'],
            [
                'name' => 'SIMS Admin',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
        
        $this->command->info('✅ Admin users created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Alternative admin:');
        $this->command->info('Email: admin@sims.com');
        $this->command->info('Password: admin123');
    }
}

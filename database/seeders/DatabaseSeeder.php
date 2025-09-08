<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific admin user if not exists
        if (!User::where('email', 'admin@eventapp.com')->exists()) {
            User::create([
                'name' => 'System Administrator',
                'email' => 'admin@eventapp.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        // Create specific organizer user if not exists
        if (!User::where('email', 'organizer@eventapp.com')->exists()) {
            User::create([
                'name' => 'Event Organizer',
                'email' => 'organizer@eventapp.com',
                'password' => Hash::make('organizer123'),
                'role' => 'organizer',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        // Create specific attendee user if not exists
        if (!User::where('email', 'attendee@eventapp.com')->exists()) {
            User::create([
                'name' => 'Event Attendee',
                'email' => 'attendee@eventapp.com',
                'password' => Hash::make('attendee123'),
                'role' => 'attendee',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        // Create test user if not exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'attendee',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        // Call other seeders safely
        $this->call([
            UserSeeder::class,
            EventCategorySeeder::class,
            EventSeeder::class,
            RegistrationSeeder::class,
            EventReviewSeeder::class,
        ]);
    }
}

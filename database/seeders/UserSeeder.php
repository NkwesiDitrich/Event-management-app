<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create additional users from factories (only if not already seeded, optional)
        User::factory()->admin()->count(2)->create();
        User::factory()->organizer()->count(10)->create();
        User::factory()->attendee()->count(50)->create();
        User::factory()->inactive()->count(5)->create();

        // Specific test users
        $testUsers = [
            [
                'name' => 'John Admin',
                'email' => 'johnn.admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Jane Organizer',
                'email' => 'janee.organizer@test.com',
                'password' => Hash::make('password'),
                'role' => 'organizer',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Bob Attendee',
                'email' => 'bobc.attendee@test.com',
                'password' => Hash::make('password'),
                'role' => 'attendee',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($testUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // unique identifier
                $userData
            );
        }
    }
}

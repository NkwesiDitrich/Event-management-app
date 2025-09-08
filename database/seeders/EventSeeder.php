<?php

namespace Database\Seeders;

use App\Infrastructure\Models\EventModel;
use App\Infrastructure\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get organizer users
        $organizers = UserModel::where('role', 'organizer')->orWhere('role', 'admin')->get();
        
        if ($organizers->isEmpty()) {
            // Create some organizers if none exist
            $organizers = UserModel::factory()->organizer()->count(5)->create();
        }

        // Get category IDs
        $categoryIds = DB::table('event_categories')->pluck('id')->toArray();

        // Create various types of events
        foreach ($organizers as $organizer) {
            // Create published events
            EventModel::factory()
                ->published()
                ->count(rand(2, 5))
                ->create([
                    'organizer_id' => $organizer->id,
                    'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
                ]);

            // Create draft events
            EventModel::factory()
                ->draft()
                ->count(rand(1, 3))
                ->create([
                    'organizer_id' => $organizer->id,
                    'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
                ]);

            // Create pending events (for admin approval)
            EventModel::factory()
                ->pending()
                ->count(rand(1, 2))
                ->create([
                    'organizer_id' => $organizer->id,
                    'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
                ]);
        }

        // Create some featured events
        EventModel::factory()
            ->featured()
            ->count(5)
            ->create([
                'organizer_id' => $organizers->random()->id,
                'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
            ]);

        // Create some past events
        EventModel::factory()
            ->past()
            ->count(20)
            ->create([
                'organizer_id' => $organizers->random()->id,
                'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
            ]);

        // Create some upcoming events
        EventModel::factory()
            ->upcoming()
            ->count(15)
            ->create([
                'organizer_id' => $organizers->random()->id,
                'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
            ]);

        // Create some nearly full events
        EventModel::factory()
            ->nearlyFull()
            ->count(3)
            ->create([
                'organizer_id' => $organizers->random()->id,
                'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
            ]);

        // Create some free events
        EventModel::factory()
            ->free()
            ->published()
            ->count(10)
            ->create([
                'organizer_id' => $organizers->random()->id,
                'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
            ]);

        // Create some paid events
        EventModel::factory()
            ->paid()
            ->published()
            ->count(8)
            ->create([
                'organizer_id' => $organizers->random()->id,
                'category_id' => $categoryIds ? $categoryIds[array_rand($categoryIds)] : null,
            ]);
    }
}

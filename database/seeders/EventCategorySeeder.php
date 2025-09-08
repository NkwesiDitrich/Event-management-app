<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Tech conferences, workshops, and meetups',
                'color' => '#007bff',
                'icon' => 'fas fa-laptop-code',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business conferences, networking events, and seminars',
                'color' => '#28a745',
                'icon' => 'fas fa-briefcase',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational workshops, training sessions, and academic events',
                'color' => '#ffc107',
                'icon' => 'fas fa-graduation-cap',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Health & Wellness',
                'slug' => 'health-wellness',
                'description' => 'Health seminars, wellness workshops, and fitness events',
                'color' => '#17a2b8',
                'icon' => 'fas fa-heartbeat',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Arts & Culture',
                'slug' => 'arts-culture',
                'description' => 'Art exhibitions, cultural events, and creative workshops',
                'color' => '#e83e8c',
                'icon' => 'fas fa-palette',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Sports & Recreation',
                'slug' => 'sports-recreation',
                'description' => 'Sports events, recreational activities, and fitness competitions',
                'color' => '#fd7e14',
                'icon' => 'fas fa-running',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Music & Entertainment',
                'slug' => 'music-entertainment',
                'description' => 'Concerts, music festivals, and entertainment events',
                'color' => '#6f42c1',
                'icon' => 'fas fa-music',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Food & Drink',
                'slug' => 'food-drink',
                'description' => 'Culinary events, wine tastings, and food festivals',
                'color' => '#dc3545',
                'icon' => 'fas fa-utensils',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Networking',
                'slug' => 'networking',
                'description' => 'Professional networking events and meetups',
                'color' => '#20c997',
                'icon' => 'fas fa-users',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Miscellaneous events that don\'t fit other categories',
                'color' => '#6c757d',
                'icon' => 'fas fa-ellipsis-h',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('event_categories')->updateOrInsert(
                ['slug' => $category['slug']], // check by slug
                array_merge($category, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}

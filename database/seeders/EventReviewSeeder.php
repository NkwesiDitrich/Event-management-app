<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Models\EventModel;
use App\Infrastructure\Models\UserModel;
use App\Infrastructure\Models\RegistrationModel;

class EventReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get past events that have attendees
        $pastEvents = EventModel::where('event_date', '<', now())
            ->where('status', 'published')
            ->whereHas('registrations', function ($query) {
                $query->where('status', 'attended');
            })
            ->get();

        foreach ($pastEvents as $event) {
            // Get attendees who attended this event
            $attendees = RegistrationModel::where('event_id', $event->id)
                ->where('status', 'attended')
                ->with('userModel')
                ->get()
                ->pluck('userModel');

            // 60% of attendees leave reviews
            $reviewersCount = (int) ($attendees->count() * 0.6);
            $reviewers = $attendees->random(min($reviewersCount, $attendees->count()));

            foreach ($reviewers as $reviewer) {
                $rating = $this->generateRealisticRating();
                $comment = $this->generateReviewComment($rating);
                
                DB::table('event_reviews')->insert([
                    'event_id' => $event->id,
                    'user_id' => $reviewer->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_approved' => rand(1, 100) <= 90, // 90% approved
                    'approved_at' => rand(1, 100) <= 90 ? now() : null,
                    'approved_by' => rand(1, 100) <= 90 ? UserModel::where('role', 'admin')->first()?->id : null,
                    'created_at' => fake()->dateTimeBetween($event->event_date, now()),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Generate realistic rating distribution
     */
    private function generateRealisticRating(): int
    {
        $rand = rand(1, 100);
        
        // Realistic rating distribution
        if ($rand <= 5) return 1;      // 5% - 1 star
        if ($rand <= 15) return 2;     // 10% - 2 stars  
        if ($rand <= 30) return 3;     // 15% - 3 stars
        if ($rand <= 65) return 4;     // 35% - 4 stars
        return 5;                      // 35% - 5 stars
    }

    /**
     * Generate review comment based on rating
     */
    private function generateReviewComment(int $rating): ?string
    {
        if (rand(1, 100) <= 20) {
            return null; // 20% of reviews have no comment
        }

        $comments = [
            1 => [
                "Very disappointing event. Poor organization and content.",
                "Waste of time and money. Would not recommend.",
                "Event was cancelled last minute with no proper communication.",
                "Venue was terrible and speakers were unprepared.",
                "Nothing like what was advertised. Very misleading."
            ],
            2 => [
                "Event was okay but had several issues with organization.",
                "Content was mediocre and venue could have been better.",
                "Some good moments but overall not worth the price.",
                "Registration process was confusing and check-in was slow.",
                "Expected more from this event based on the description."
            ],
            3 => [
                "Average event. Some good content but room for improvement.",
                "Decent speakers but venue was a bit cramped.",
                "Good networking opportunities but content was basic.",
                "Event was fine, nothing exceptional but not bad either.",
                "Met my expectations but didn't exceed them."
            ],
            4 => [
                "Great event! Well organized with excellent speakers.",
                "Really enjoyed the content and networking opportunities.",
                "Good value for money. Would attend similar events.",
                "Professional organization and high-quality presentations.",
                "Learned a lot and made valuable connections."
            ],
            5 => [
                "Outstanding event! Exceeded all my expectations.",
                "Absolutely fantastic! Best event I've attended this year.",
                "Perfect organization, amazing speakers, and great venue.",
                "Incredible value and life-changing content. Highly recommend!",
                "Flawless execution and world-class speakers. Will definitely attend again!",
                "Amazing networking opportunities and top-notch content.",
                "This event was a game-changer for my career. Thank you!"
            ]
        ];

        $ratingComments = $comments[$rating] ?? $comments[3];
        return $ratingComments[array_rand($ratingComments)];
    }
}

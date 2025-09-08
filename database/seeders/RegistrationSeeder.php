<?php

namespace Database\Seeders;

use App\Infrastructure\Models\RegistrationModel;
use App\Infrastructure\Models\EventModel;
use App\Infrastructure\Models\UserModel;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get published events
        $events = EventModel::where('status', 'published')->get();
        
        // Get attendee users
        $attendees = UserModel::where('role', 'attendee')->get();
        
        if ($attendees->isEmpty()) {
            // Create some attendees if none exist
            $attendees = UserModel::factory()->attendee()->count(20)->create();
        }

        foreach ($events as $event) {
            // Determine how many registrations this event should have
            $maxRegistrations = $event->capacity ? min($event->capacity, $attendees->count()) : min(50, $attendees->count());
            $registrationCount = rand(1, min($maxRegistrations, 30));
            
            // Get random attendees for this event
            $eventAttendees = $attendees->random($registrationCount);
            
            foreach ($eventAttendees as $attendee) {
                // Create different types of registrations
                $registrationType = rand(1, 100);
                
                if ($registrationType <= 70) {
                    // 70% confirmed registrations
                    RegistrationModel::factory()
                        ->confirmed()
                        ->create([
                            'user_id' => $attendee->id,
                            'event_id' => $event->id,
                            'amount_paid' => $event->price ?? 0,
                        ]);
                } elseif ($registrationType <= 85) {
                    // 15% attended registrations (for past events)
                    RegistrationModel::factory()
                        ->attended()
                        ->create([
                            'user_id' => $attendee->id,
                            'event_id' => $event->id,
                            'amount_paid' => $event->price ?? 0,
                        ]);
                } elseif ($registrationType <= 95) {
                    // 10% cancelled registrations
                    RegistrationModel::factory()
                        ->cancelled()
                        ->create([
                            'user_id' => $attendee->id,
                            'event_id' => $event->id,
                            'amount_paid' => $event->price ?? 0,
                        ]);
                } else {
                    // 5% no-show registrations
                    RegistrationModel::factory()
                        ->noShow()
                        ->create([
                            'user_id' => $attendee->id,
                            'event_id' => $event->id,
                            'amount_paid' => $event->price ?? 0,
                        ]);
                }
            }
            
            // Update event's current_registrations count
            $confirmedCount = RegistrationModel::where('event_id', $event->id)
                ->whereIn('status', ['confirmed', 'attended'])
                ->count();
                
            $event->update(['current_registrations' => $confirmedCount]);
        }

        // Create some recent registrations for testing
        $recentEvents = EventModel::where('status', 'published')
            ->where('event_date', '>', now())
            ->take(5)
            ->get();

        foreach ($recentEvents as $event) {
            RegistrationModel::factory()
                ->recent()
                ->count(rand(1, 5))
                ->create([
                    'event_id' => $event->id,
                    'user_id' => $attendees->random()->id,
                    'amount_paid' => $event->price ?? 0,
                ]);
        }

        // Create some pending payment registrations
        $paidEvents = EventModel::where('status', 'published')
            ->where('price', '>', 0)
            ->take(3)
            ->get();

        foreach ($paidEvents as $event) {
            RegistrationModel::factory()
                ->pendingPayment()
                ->count(rand(1, 3))
                ->create([
                    'event_id' => $event->id,
                    'user_id' => $attendees->random()->id,
                    'amount_paid' => 0,
                ]);
        }
    }
}

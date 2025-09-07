<?php

namespace App\Application\UseCases\User;

use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;

class GetOrganizerStatsUseCase
{
    private EventRepositoryInterface $eventRepository;
    private RegistrationRepositoryInterface $registrationRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        RegistrationRepositoryInterface $registrationRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->registrationRepository = $registrationRepository;
    }

    public function execute(int $organizerId): array
    {
        $organizerEvents = $this->eventRepository->findByOrganizerId($organizerId);
        
        $totalEvents = count($organizerEvents);
        $publishedEvents = count(array_filter($organizerEvents, fn($event) => $event->getStatus() === 'published'));
        $draftEvents = count(array_filter($organizerEvents, fn($event) => $event->getStatus() === 'draft'));
        $cancelledEvents = count(array_filter($organizerEvents, fn($event) => $event->getStatus() === 'cancelled'));
        
        // Calculate total attendees across all events
        $totalAttendees = 0;
        foreach ($organizerEvents as $event) {
            $totalAttendees += $event->getCurrentRegistrations();
        }

        // Get upcoming events
        $upcomingEvents = array_filter($organizerEvents, function($event) {
            return $event->getEventDate()->getValue() > new \DateTime() && $event->getStatus() === 'published';
        });

        // Get events nearing capacity
        $eventsNearingCapacity = array_filter($organizerEvents, function($event) {
            if (!$event->getCapacity()->isLimited()) {
                return false;
            }
            $capacity = $event->getCapacity()->getLimit();
            $registrations = $event->getCurrentRegistrations();
            return ($registrations / $capacity) >= 0.8; // 80% or more filled
        });

        return [
            'myEvents' => $organizerEvents,
            'totalEvents' => $totalEvents,
            'publishedEvents' => $publishedEvents,
            'draftEvents' => $draftEvents,
            'cancelledEvents' => $cancelledEvents,
            'totalAttendees' => $totalAttendees,
            'upcomingEvents' => $upcomingEvents,
            'upcomingEventsCount' => count($upcomingEvents),
            'eventsNearingCapacity' => $eventsNearingCapacity,
            'averageAttendeesPerEvent' => $totalEvents > 0 ? round($totalAttendees / $totalEvents, 1) : 0
        ];
    }
}

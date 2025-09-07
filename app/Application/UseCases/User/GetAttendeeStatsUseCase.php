<?php

namespace App\Application\UseCases\User;

use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use App\Domain\Event\Repositories\EventRepositoryInterface;

class GetAttendeeStatsUseCase
{
    private RegistrationRepositoryInterface $registrationRepository;
    private EventRepositoryInterface $eventRepository;

    public function __construct(
        RegistrationRepositoryInterface $registrationRepository,
        EventRepositoryInterface $eventRepository
    ) {
        $this->registrationRepository = $registrationRepository;
        $this->eventRepository = $eventRepository;
    }

    public function execute(int $userId): array
    {
        $userRegistrations = $this->registrationRepository->findByUserId($userId);
        $allEvents = $this->eventRepository->findAll();
        
        // Filter upcoming events
        $upcomingEvents = array_filter($allEvents, function($event) {
            return $event->getEventDate()->getValue() > new \DateTime() && $event->getStatus() === 'published';
        });

        // Filter registered events that are upcoming
        $registeredUpcomingEvents = [];
        foreach ($userRegistrations as $registration) {
            $event = $this->eventRepository->findById($registration->getEventId());
            if ($event && $event->getEventDate()->getValue() > new \DateTime()) {
                $registeredUpcomingEvents[] = $event;
            }
        }

        // Count attended events (past events user was registered for)
        $attendedEventsCount = 0;
        foreach ($userRegistrations as $registration) {
            $event = $this->eventRepository->findById($registration->getEventId());
            if ($event && $event->getEventDate()->getValue() < new \DateTime()) {
                $attendedEventsCount++;
            }
        }

        return [
            'registeredEvents' => $registeredUpcomingEvents,
            'upcomingEvents' => $upcomingEvents,
            'registeredEventsCount' => count($registeredUpcomingEvents),
            'upcomingEventsCount' => count($upcomingEvents),
            'attendedEventsCount' => $attendedEventsCount,
            'totalRegistrations' => count($userRegistrations)
        ];
    }
}

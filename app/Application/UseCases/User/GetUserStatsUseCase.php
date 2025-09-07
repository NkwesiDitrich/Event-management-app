<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;

class GetUserStatsUseCase
{
    private UserRepositoryInterface $userRepository;
    private EventRepositoryInterface $eventRepository;
    private RegistrationRepositoryInterface $registrationRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EventRepositoryInterface $eventRepository,
        RegistrationRepositoryInterface $registrationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->registrationRepository = $registrationRepository;
    }

    public function execute(): array
    {
        $allUsers = $this->userRepository->findAll();
        $allEvents = $this->eventRepository->findAll();
        
        $totalUsers = count($allUsers);
        $totalEvents = count($allEvents);
        $publishedEvents = count(array_filter($allEvents, fn($event) => $event->getStatus() === 'published'));
        $draftEvents = count(array_filter($allEvents, fn($event) => $event->getStatus() === 'draft'));
        $pendingEvents = count(array_filter($allEvents, fn($event) => $event->getStatus() === 'pending'));
        
        // Count total registrations
        $totalRegistrations = 0;
        foreach ($allEvents as $event) {
            $registrations = $this->registrationRepository->findByEventId($event->getId());
            $totalRegistrations += count($registrations);
        }

        return [
            'totalUsers' => $totalUsers,
            'totalEvents' => $totalEvents,
            'publishedEvents' => $publishedEvents,
            'draftEvents' => $draftEvents,
            'pendingEvents' => $pendingEvents,
            'totalRegistrations' => $totalRegistrations,
            'adminCount' => count(array_filter($allUsers, fn($user) => $user->isAdmin())),
            'organizerCount' => count(array_filter($allUsers, fn($user) => $user->isOrganizer() && !$user->isAdmin())),
            'attendeeCount' => count(array_filter($allUsers, fn($user) => $user->isAttendee())),
        ];
    }
}

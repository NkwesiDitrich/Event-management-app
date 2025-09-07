<?php

namespace App\Application\UseCases\Registration;

use App\Domain\Registration\Entities\Registration;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;

class RegisterForEventUseCase
{
    private RegistrationRepositoryInterface $registrationRepository;
    private EventRepositoryInterface $eventRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        RegistrationRepositoryInterface $registrationRepository,
        EventRepositoryInterface $eventRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->registrationRepository = $registrationRepository;
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(int $userId, int $eventId): void
    {
        // Get user and event
        $user = $this->userRepository->findById($userId);
        $event = $this->eventRepository->findById($eventId);

        if (!$user) {
            throw new \DomainException('User not found');
        }

        if (!$event) {
            throw new \DomainException('Event not found');
        }

        // Check if event is published
        if (!$event->isPublished()) {
            throw new \DomainException('Cannot register for unpublished event');
        }

        // Check if event is cancelled
        if ($event->isCancelled()) {
            throw new \DomainException('Cannot register for cancelled event');
        }

        // Check if user is already registered
        if ($this->registrationRepository->existsByUserAndEvent($userId, $eventId)) {
            throw new \DomainException('User is already registered for this event');
        }

        // Check capacity
        if (!$event->hasAvailableCapacity()) {
            throw new \DomainException('Event has reached maximum capacity');
        }

        // Create registration
        $registration = new Registration(0, $user, $event);
        
        // Save registration
        $this->registrationRepository->save($registration);

        // Update event registration count
        $event->incrementRegistrations();
        $this->eventRepository->save($event);
    }
}
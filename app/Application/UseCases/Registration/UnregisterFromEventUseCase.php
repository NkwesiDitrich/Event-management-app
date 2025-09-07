<?php

namespace App\Application\UseCases\Registration;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;

class UnregisterFromEventUseCase
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

        // Check if user is actually registered
        if (!$this->registrationRepository->existsByUserAndEvent($userId, $eventId)) {
            throw new \DomainException('User is not registered for this event');
        }

        // Get the registration and delete it
        $registrations = $this->registrationRepository->findByUserId($userId);
        $registration = current(array_filter($registrations, fn($r) => $r->getEvent()->getId() === $eventId));
        
        if ($registration) {
            $this->registrationRepository->delete($registration);
        }

        // Update event registration count
        $event->decrementRegistrations();
        $this->eventRepository->save($event);
    }
}
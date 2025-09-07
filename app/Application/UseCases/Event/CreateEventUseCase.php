<?php

namespace App\Application\UseCases\Event;

use App\Domain\Event\Entities\Event;
use App\Domain\Event\ValueObjects\EventDate;
use App\Domain\Event\ValueObjects\EventLocation;
use App\Domain\Event\ValueObjects\EventCapacity;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Application\DTOs\CreateEventDTO;

class CreateEventUseCase
{
    private EventRepositoryInterface $eventRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(CreateEventDTO $dto, bool $publish = false): Event
    {
        // Get the organizer
        $organizer = $this->userRepository->findById($dto->organizerId);
        
        if (!$organizer) {
            throw new \DomainException('Organizer not found');
        }

        // Create value objects
        $eventDate = new EventDate($dto->eventDate);
        $location = new EventLocation($dto->location);
        $capacity = new EventCapacity($dto->capacity);

        // Create event entity
        $event = new Event(
            0, // ID will be set by repository
            $dto->title,
            $dto->description,
            $eventDate,
            $location,
            $capacity,
            $organizer
        );

        // Publish if requested
        if ($publish) {
            $event->publish();
        }

        // Save event
        $this->eventRepository->save($event);

        return $event;
    }
}
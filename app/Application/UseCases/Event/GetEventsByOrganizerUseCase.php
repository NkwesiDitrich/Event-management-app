<?php

namespace App\Application\UseCases\Event;

use App\Domain\Event\Repositories\EventRepositoryInterface;

class GetEventsByOrganizerUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(int $organizerId): array
    {
        return $this->eventRepository->findByOrganizerId($organizerId);
    }
}

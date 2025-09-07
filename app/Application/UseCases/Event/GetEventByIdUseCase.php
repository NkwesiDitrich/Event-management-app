<?php

namespace App\Application\UseCases\Event;

use App\Domain\Event\Entities\Event;
use App\Domain\Event\Repositories\EventRepositoryInterface;

class GetEventByIdUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(int $eventId): ?Event
    {
        return $this->eventRepository->findById($eventId);
    }
}

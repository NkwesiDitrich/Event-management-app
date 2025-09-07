<?php

namespace App\Application\UseCases\Event;

use App\Domain\Event\Repositories\EventRepositoryInterface;

class GetAllEventsUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(bool $publishedOnly = true): array
    {
        if ($publishedOnly) {
            return $this->eventRepository->findUpcomingEvents();
        }

        return $this->eventRepository->findAll();
    }
}
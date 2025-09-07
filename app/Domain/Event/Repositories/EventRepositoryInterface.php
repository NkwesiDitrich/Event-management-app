<?php

namespace App\Domain\Event\Repositories;

use App\Domain\Event\Entities\Event;

interface EventRepositoryInterface
{
    public function save(Event $event): void;
    public function findById(int $id): ?Event;
    public function findByOrganizerId(int $organizerId): array;
    public function findUpcomingEvents(): array;
    public function findAll(): array;
    public function findEventsNearingCapacity(): array;
    public function delete(Event $event): void;
}
<?php

namespace App\Domain\Registration\Repositories;

use App\Domain\Registration\Entities\Registration;

interface RegistrationRepositoryInterface
{
    public function save(Registration $registration): void;
    public function findById(int $id): ?Registration;
    public function findByEventId(int $eventId): array;
    public function findByUserId(int $userId): array;
    public function existsByUserAndEvent(int $userId, int $eventId): bool;
    public function delete(Registration $registration): void;
}
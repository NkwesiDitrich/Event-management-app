<?php

namespace App\Domain\Registration\Entities;

use App\Domain\User\Entities\User;
use App\Domain\Event\Entities\Event;
use DateTimeImmutable;

class Registration
{
    private int $id;
    private User $user;
    private Event $event;
    private DateTimeImmutable $registeredAt;
    private string $status;

    public function __construct(
        int $id,
        User $user,
        Event $event,
        DateTimeImmutable $registeredAt = null,
        string $status = 'confirmed'
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->event = $event;
        $this->registeredAt = $registeredAt ?: new DateTimeImmutable();
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getRegisteredAt(): DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // Required methods for repository
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
<?php

namespace App\Domain\Event\Entities;

use App\Domain\Event\ValueObjects\EventDate;
use App\Domain\Event\ValueObjects\EventLocation;
use App\Domain\Event\ValueObjects\EventCapacity;
use App\Domain\User\Entities\User;

class Event
{
    private int $id;
    private string $title;
    private string $description;
    private EventDate $eventDate;
    private EventLocation $location;
    private EventCapacity $capacity;
    private User $organizer;
    private int $currentRegistrations;
    private string $status;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        int $id,
        string $title,
        string $description,
        EventDate $eventDate,
        EventLocation $location,
        EventCapacity $capacity,
        User $organizer
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->eventDate = $eventDate;
        $this->location = $location;
        $this->capacity = $capacity;
        $this->organizer = $organizer;
        $this->currentRegistrations = 0;
        $this->status = 'draft';
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCurrentRegistrations(int $count): void
    {
        $this->currentRegistrations = $count;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEventDate(): EventDate
    {
        return $this->eventDate;
    }

    public function getLocation(): EventLocation
    {
        return $this->location;
    }

    public function getCapacity(): EventCapacity
    {
        return $this->capacity;
    }

    public function getOrganizer(): User
    {
        return $this->organizer;
    }

    public function getCurrentRegistrations(): int
    {
        return $this->currentRegistrations;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function updateDetails(
        string $title,
        string $description,
        EventDate $eventDate,
        EventLocation $location
    ): void {
        $this->title = $title;
        $this->description = $description;
        $this->eventDate = $eventDate;
        $this->location = $location;
        $this->updatedAt = new \DateTime();
    }

    public function updateCapacity(EventCapacity $newCapacity): void
    {
        if ($newCapacity->isLimited() && 
            $newCapacity->getLimit() < $this->currentRegistrations) {
            throw new \DomainException(
                'Cannot reduce capacity below current registrations'
            );
        }

        $this->capacity = $newCapacity;
        $this->updatedAt = new \DateTime();
    }

    public function publish(): void
    {
        if ($this->status !== 'draft') {
            throw new \DomainException('Only draft events can be published');
        }

        $this->status = 'published';
        $this->updatedAt = new \DateTime();
    }

    public function cancel(): void
    {
        if ($this->status === 'cancelled') {
            throw new \DomainException('Event is already cancelled');
        }

        $this->status = 'cancelled';
        $this->updatedAt = new \DateTime();
    }

    public function hasAvailableCapacity(): bool
    {
        return $this->capacity->hasAvailableCapacity($this->currentRegistrations);
    }

    public function incrementRegistrations(): void
    {
        if (!$this->hasAvailableCapacity()) {
            throw new \DomainException('Event has reached maximum capacity');
        }

        $this->currentRegistrations++;
        $this->updatedAt = new \DateTime();
    }

    public function decrementRegistrations(): void
    {
        if ($this->currentRegistrations <= 0) {
            throw new \DomainException('Cannot decrement registrations below zero');
        }

        $this->currentRegistrations--;
        $this->updatedAt = new \DateTime();
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->organizer->getId() === $user->getId();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    
}
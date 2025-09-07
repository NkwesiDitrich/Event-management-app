<?php

namespace App\Domain\Event\ValueObjects;

use InvalidArgumentException;

class EventCapacity
{
    private ?int $limit;

    public function __construct(?int $capacity = null)
    {
        $this->validate($capacity);
        $this->limit = $capacity;
    }

    private function validate(?int $capacity): void
    {
        if ($capacity !== null) {
            // Business rule: Capacity must be positive if specified
            if ($capacity <= 0) {
                throw new InvalidArgumentException('Event capacity must be a positive number if specified.');
            }

            // Business rule: Prevent unrealistically large numbers
            if ($capacity > 100000) {
                throw new InvalidArgumentException('Event capacity is too large.');
            }
        }
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function isLimited(): bool
    {
        return $this->limit !== null;
    }

    public function isUnlimited(): bool
    {
        return $this->limit === null;
    }

    public function hasAvailableCapacity(int $currentRegistrations): bool
    {
        if ($this->isUnlimited()) {
            return true;
        }

        return $currentRegistrations < $this->limit;
    }

    public function getAvailableCapacity(int $currentRegistrations): ?int
    {
        if ($this->isUnlimited()) {
            return null;
        }

        return max(0, $this->limit - $currentRegistrations);
    }

    public function equals(EventCapacity $other): bool
    {
        return $this->limit === $other->getLimit();
    }

    public function __toString(): string
    {
        return $this->isUnlimited() ? 'Unlimited' : (string) $this->limit;
    }
}
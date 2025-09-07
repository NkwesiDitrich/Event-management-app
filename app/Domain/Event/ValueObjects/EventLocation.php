<?php

namespace App\Domain\Event\ValueObjects;

use InvalidArgumentException;

class EventLocation
{
    private string $value;

    public function __construct(string $location)
    {
        $this->validate($location);
        $this->value = trim($location);
    }

    private function validate(string $location): void
    {
        if (empty(trim($location))) {
            throw new InvalidArgumentException('Event location cannot be empty.');
        }

        if (strlen($location) > 500) {
            throw new InvalidArgumentException('Event location is too long.');
        }

        // Basic validation to ensure it looks like a plausible location
        if (preg_match('/^[a-zA-Z0-9\s,.-]+$/u', $location) !== 1) {
            throw new InvalidArgumentException('Event location contains invalid characters.');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(EventLocation $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
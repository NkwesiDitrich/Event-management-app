<?php

namespace App\Domain\Event\ValueObjects;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

class EventDate
{
    private DateTimeImmutable $value;

    public function __construct(string $dateTimeString, string $timezone = 'UTC')
    {
        $timezoneObj = new DateTimeZone($timezone);
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateTimeString, $timezoneObj);

        if (!$dateTime) {
            throw new InvalidArgumentException('Invalid date format. Expected Y-m-d H:i:s.');
        }

        $this->validate($dateTime);
        $this->value = $dateTime;
    }

    private function validate(DateTimeImmutable $dateTime): void
    {
        $now = new DateTimeImmutable('now', $dateTime->getTimezone());

        // Business rule: Event cannot be scheduled in the past
        if ($dateTime < $now) {
            throw new InvalidArgumentException('Event date must be in the future.');
        }
    }

    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }

    public function equals(EventDate $other): bool
    {
        return $this->value->getTimestamp() === $other->getValue()->getTimestamp();
    }

    public function __toString(): string
    {
        return $this->value->format('Y-m-d H:i:s');
    }

    // Optional: Helper method to check if this date conflicts with another
    public function conflictsWith(EventDate $other, int $bufferMinutes = 0): bool
    {
        // Implement logic to check if two events are too close together
        // This is a simplified example
        $difference = abs($this->value->getTimestamp() - $other->getValue()->getTimestamp());
        return $difference < ($bufferMinutes * 60);
    }
}
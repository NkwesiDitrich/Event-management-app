<?php

namespace App\Domain\User\ValueObjects;

class UserRole
{
    private const ADMIN = 'admin';
    private const ORGANIZER = 'organizer';
    private const ATTENDEE = 'attendee';

    private string $value;

    private function __construct(string $role)
    {
        $this->validate($role);
        $this->value = $role;
    }

    private function validate(string $role): void
    {
        $validRoles = [self::ADMIN, self::ORGANIZER, self::ATTENDEE];
        
        if (!in_array($role, $validRoles)) {
            throw new \InvalidArgumentException('Invalid user role');
        }
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public static function organizer(): self
    {
        return new self(self::ORGANIZER);
    }

    public static function attendee(): self
    {
        return new self(self::ATTENDEE);
    }

    public static function fromString(string $role): self
    {
        return new self($role);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(UserRole $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
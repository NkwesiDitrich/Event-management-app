<?php

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserRole;

class User
{
    private int $id;
    private string $name;
    private Email $email;
    private UserRole $role;
    private string $passwordHash;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        int $id,
        string $name,
        Email $email,
        UserRole $role,
        string $passwordHash
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->passwordHash = $passwordHash;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

     public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function updateProfile(string $name, Email $email): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->updatedAt = new \DateTime();
    }

    public function changeRole(UserRole $newRole): void
    {
        $this->role = $newRole;
        $this->updatedAt = new \DateTime();
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function changePassword(string $newPassword): void
    {
        $this->passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->updatedAt = new \DateTime();
    }

    public function isAdmin(): bool
    {
        return $this->role->equals(UserRole::admin());
    }

    public function isOrganizer(): bool
    {
        return $this->role->equals(UserRole::organizer()) || $this->isAdmin();
    }

    public function isAttendee(): bool
    {
        return $this->role->equals(UserRole::attendee());
    }
}
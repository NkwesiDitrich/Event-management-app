<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserRole;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function findByRole(UserRole $role): array;
    public function delete(User $user): void;
    public function existsByEmail(Email $email): bool;
    /**
     * Get all users.
     *
     * @return User[]
     */
    public function findAll(): array;
}
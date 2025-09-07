<?php

namespace App\Application\UseCases\Admin;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\UserRole;

class ManageUserRoleUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $userId, string $newRole): void
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \DomainException('User not found');
        }

        $roleObject = UserRole::fromString($newRole);
        $user->changeRole($roleObject);
        
        $this->userRepository->save($user);
    }
}

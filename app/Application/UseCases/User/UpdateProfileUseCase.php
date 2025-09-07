<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Application\DTOs\UpdateProfileDTO;

class UpdateProfileUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $userId, UpdateProfileDTO $dto): void
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \DomainException('User not found');
        }

        // Check if email is already taken by another user
        $existingUser = $this->userRepository->findByEmail($dto->email);
        if ($existingUser && $existingUser->getId() !== $userId) {
            throw new \DomainException('Email address is already taken');
        }

        $email = new Email($dto->email);
        $user->updateProfile($dto->name, $email);
        
        $this->userRepository->save($user);
    }
}

<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Application\DTOs\CreateUserDTO;

class CreateUserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(CreateUserDTO $dto): User
    {
        $email = new Email($dto->email);

        // Check if user already exists
        if ($this->userRepository->existsByEmail($email)) {
            throw new \DomainException('User with this email already exists');
        }

        // Create new user
        $user = new User(
            0, // ID will be set by repository
            $dto->name,
            $email,
            UserRole::fromString($dto->role),
            password_hash($dto->password, PASSWORD_DEFAULT)
        );

        // Save user
        $this->userRepository->save($user);

        return $user;
    }
}
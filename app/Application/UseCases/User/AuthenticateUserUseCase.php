<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;

class AuthenticateUserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(string $email, string $password): \App\Domain\User\Entities\User
    {
        $emailVo = new Email($email);
        $user = $this->userRepository->findByEmail($emailVo);

        if (!$user) {
            throw new \DomainException('Invalid credentials');
        }

        if (!$user->verifyPassword($password)) {
            throw new \DomainException('Invalid credentials');
        }

        return $user;
    }
}
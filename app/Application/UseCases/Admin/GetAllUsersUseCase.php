<?php

namespace App\Application\UseCases\Admin;

use App\Domain\User\Repositories\UserRepositoryInterface;

class GetAllUsersUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(): array
    {
        return $this->userRepository->findAll();
    }
}

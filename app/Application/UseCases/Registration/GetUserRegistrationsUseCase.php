<?php

namespace App\Application\UseCases\Registration;

use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;

class GetUserRegistrationsUseCase
{
    private RegistrationRepositoryInterface $registrationRepository;

    public function __construct(RegistrationRepositoryInterface $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    public function execute(int $userId): array
    {
        return $this->registrationRepository->findByUserId($userId);
    }
}

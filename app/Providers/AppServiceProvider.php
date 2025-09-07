<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Infrastructure\Repositories\EloquentEventRepository;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use App\Infrastructure\Repositories\EloquentRegistrationRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            EventRepositoryInterface::class,
            EloquentEventRepository::class
        );

        $this->app->bind(
            RegistrationRepositoryInterface::class,
            EloquentRegistrationRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
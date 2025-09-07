<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Infrastructure\Repositories\EloquentEventRepository;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use App\Infrastructure\Repositories\EloquentRegistrationRepository;
use App\Application\Services\AuthService;
use Illuminate\Auth\AuthManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
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

        // Service bindings
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(UserRepositoryInterface::class),
                $app->make(AuthManager::class)
            );
        });

        // Additional service bindings can be added here
        // Example: Event management services, notification services, etc.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom middleware
        $router = $this->app['router'];
        
        $router->aliasMiddleware('auth.custom', \App\Http\Middleware\Authenticate::class);
        $router->aliasMiddleware('guest.custom', \App\Http\Middleware\RedirectIfAuthenticated::class);
        $router->aliasMiddleware('role', \App\Http\Middleware\CheckUserRole::class);

        // Additional boot logic can be added here
        // Example: Event listeners, view composers, etc.
    }
}
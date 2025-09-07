<?php

namespace App\Application\Services;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserRole;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private UserRepositoryInterface $userRepository;
    private AuthManager $authManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AuthManager $authManager
    ) {
        $this->userRepository = $userRepository;
        $this->authManager = $authManager;
    }

    /**
     * Register a new user
     */
    public function register(array $userData): User
    {
        // Check if user already exists
        $existingUser = $this->userRepository->findByEmail(new Email($userData['email']));
        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => ['The email has already been taken.']
            ]);
        }

        // Create domain user entity
        $user = new User(
            0, // ID will be set by repository
            $userData['name'],
            new Email($userData['email']),
            UserRole::attendee(), // Default role
            Hash::make($userData['password'])
        );

        // Save user through repository
        $savedUser = $this->userRepository->save($user);

        // Log the user in
        $this->loginUser($savedUser);

        return $savedUser;
    }

    /**
     * Authenticate user with email and password
     */
    public function login(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail(new Email($email));

        if (!$user || !$user->verifyPassword($password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $this->loginUser($user);

        return $user;
    }

    /**
     * Log out the current user
     */
    public function logout(): void
    {
        $this->authManager->guard()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Get the currently authenticated user as domain entity
     */
    public function getCurrentUser(): ?User
    {
        $authUser = $this->authManager->guard()->user();
        
        if (!$authUser) {
            return null;
        }

        return $this->userRepository->findById($authUser->id);
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return $this->authManager->guard()->check();
    }

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $profileData): User
    {
        $user->updateProfile(
            $profileData['name'],
            new Email($profileData['email'])
        );

        return $this->userRepository->save($user);
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): User
    {
        if (!$user->verifyPassword($currentPassword)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.']
            ]);
        }

        $user->changePassword($newPassword);
        return $this->userRepository->save($user);
    }

    /**
     * Promote user to organizer
     */
    public function promoteToOrganizer(User $user): User
    {
        $user->changeRole(UserRole::organizer());
        return $this->userRepository->save($user);
    }

    /**
     * Login user using Laravel's auth system
     */
    private function loginUser(User $user): void
    {
        // Find the Eloquent model for Laravel auth
        $eloquentUser = \App\Models\User::find($user->getId());
        
        if ($eloquentUser) {
            $this->authManager->guard()->login($eloquentUser);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\AuthenticateUserUseCase;
use App\Application\DTOs\CreateUserDTO;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    private CreateUserUseCase $createUserUseCase;
    private AuthenticateUserUseCase $authenticateUserUseCase;

    public function __construct(
        CreateUserUseCase $createUserUseCase,
        AuthenticateUserUseCase $authenticateUserUseCase
    ) {
        $this->createUserUseCase = $createUserUseCase;
        $this->authenticateUserUseCase = $authenticateUserUseCase;
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(CreateUserRequest $request): RedirectResponse
    {
        try {
            $dto = new CreateUserDTO(
                $request->name,
                $request->email,
                $request->role ?? 'attendee',
                $request->password
            );

            $user = $this->createUserUseCase->execute($dto);

            Auth::loginUsingId($user->getId());

            return redirect()->route('dashboard')
                ->with('success', 'Account created successfully!');
        } catch (\DomainException $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $user = $this->authenticateUserUseCase->execute(
                $request->email,
                $request->password
            );

            Auth::loginUsingId($user->getId());

            return redirect()->intended('dashboard');
        } catch (\DomainException $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
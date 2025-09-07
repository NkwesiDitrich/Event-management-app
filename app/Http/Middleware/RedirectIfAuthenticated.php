<?php

namespace App\Http\Middleware;

use App\Application\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RedirectIfAuthenticated
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if ($this->authService->isAuthenticated()) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use App\Application\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckUserRole
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $this->authService->getCurrentUser();

        if (!$user) {
            return redirect()->route('login');
        }

        $hasPermission = match ($role) {
            'admin' => $user->isAdmin(),
            'organizer' => $user->isOrganizer(),
            'attendee' => $user->isAttendee(),
            default => false
        };

        if (!$hasPermission) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
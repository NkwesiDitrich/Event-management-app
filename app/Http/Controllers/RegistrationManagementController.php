<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Registration\GetUserRegistrationsUseCase;
use App\Application\UseCases\Registration\UnregisterFromEventUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RegistrationManagementController extends Controller
{
    private GetUserRegistrationsUseCase $getUserRegistrationsUseCase;
    private UnregisterFromEventUseCase $unregisterFromEventUseCase;

    public function __construct(
        GetUserRegistrationsUseCase $getUserRegistrationsUseCase,
        UnregisterFromEventUseCase $unregisterFromEventUseCase
    ) {
        $this->getUserRegistrationsUseCase = $getUserRegistrationsUseCase;
        $this->unregisterFromEventUseCase = $unregisterFromEventUseCase;
    }

    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $registrations = $this->getUserRegistrationsUseCase->execute($user->getId());
        
        return view('registrations.index', compact('registrations'));
    }

    public function unregister(int $eventId): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $this->unregisterFromEventUseCase->execute($user->getId(), $eventId);
            
            return redirect()->route('registrations.index')
                ->with('success', 'Successfully unregistered from event!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

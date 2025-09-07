<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Event\GetAllEventsUseCase;
use App\Application\UseCases\Event\GetEventsByOrganizerUseCase;
use App\Application\UseCases\Registration\GetUserRegistrationsUseCase;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private GetAllEventsUseCase $getAllEventsUseCase;
    private GetEventsByOrganizerUseCase $getEventsByOrganizerUseCase;
    private GetUserRegistrationsUseCase $getUserRegistrationsUseCase;

    public function __construct(
        GetAllEventsUseCase $getAllEventsUseCase,
        GetEventsByOrganizerUseCase $getEventsByOrganizerUseCase,
        GetUserRegistrationsUseCase $getUserRegistrationsUseCase
    ) {
        $this->getAllEventsUseCase = $getAllEventsUseCase;
        $this->getEventsByOrganizerUseCase = $getEventsByOrganizerUseCase;
        $this->getUserRegistrationsUseCase = $getUserRegistrationsUseCase;
    }

    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = [];

        if ($user && $user->isOrganizer()) {
            $data['organizedEvents'] = $this->getEventsByOrganizerUseCase->execute($user->getId());
        }

        $data['upcomingEvents'] = $this->getAllEventsUseCase->execute(true);
        
        if ($user) {
            $data['userRegistrations'] = $this->getUserRegistrationsUseCase->execute($user->getId());
        } else {
            $data['userRegistrations'] = [];
        }

        return view('dashboard', $data);
    }
}

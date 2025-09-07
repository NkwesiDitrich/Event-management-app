<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Event\GetAllEventsUseCase;
use App\Application\UseCases\Event\GetEventsByOrganizerUseCase;
use App\Application\UseCases\Registration\GetUserRegistrationsUseCase;
use Illuminate\View\View;

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
        $user = auth()->user();
        $data = [];

        if ($user->isOrganizer()) {
            $data['organizedEvents'] = $this->getEventsByOrganizerUseCase->execute($user->getId());
        }

        $data['upcomingEvents'] = $this->getAllEventsUseCase->execute(true);
        $data['userRegistrations'] = $this->getUserRegistrationsUseCase->execute($user->getId());

        return view('dashboard', $data);
    }
}
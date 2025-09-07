<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;

use App\Application\UseCases\User\GetUserStatsUseCase;
use App\Application\UseCases\User\GetAttendeeStatsUseCase;
use App\Application\UseCases\User\GetOrganizerStatsUseCase;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private GetUserStatsUseCase $getUserStatsUseCase;
    private GetAttendeeStatsUseCase $getAttendeeStatsUseCase;
    private GetOrganizerStatsUseCase $getOrganizerStatsUseCase;

    public function __construct(
        GetUserStatsUseCase $getUserStatsUseCase,
        GetAttendeeStatsUseCase $getAttendeeStatsUseCase,
        GetOrganizerStatsUseCase $getOrganizerStatsUseCase
    ) {
        $this->getUserStatsUseCase = $getUserStatsUseCase;
        $this->getAttendeeStatsUseCase = $getAttendeeStatsUseCase;
        $this->getOrganizerStatsUseCase = $getOrganizerStatsUseCase;
    }

    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }
        
        if ($user->isOrganizer()) {
            return $this->organizerDashboard($user->getId());
        }
        
        return $this->attendeeDashboard($user->getId());
    }

    private function attendeeDashboard(int $userId): View
    {
        $stats = $this->getAttendeeStatsUseCase->execute($userId);
        
        return view('dashboard.attendee', [
            'registeredEvents' => $stats['registeredEvents'],
            'upcomingEvents' => $stats['upcomingEvents'],
            'registeredEventsCount' => $stats['registeredEventsCount'],
            'upcomingEventsCount' => $stats['upcomingEventsCount'],
            'attendedEventsCount' => $stats['attendedEventsCount'],
            'totalRegistrations' => $stats['totalRegistrations']
        ]);
    }

    private function organizerDashboard(int $organizerId): View
    {
        $stats = $this->getOrganizerStatsUseCase->execute($organizerId);
        
        return view('dashboard.organizer', [
            'myEvents' => $stats['myEvents'],
            'totalEvents' => $stats['totalEvents'],
            'publishedEvents' => $stats['publishedEvents'],
            'draftEvents' => $stats['draftEvents'],
            'cancelledEvents' => $stats['cancelledEvents'],
            'totalAttendees' => $stats['totalAttendees'],
            'upcomingEvents' => $stats['upcomingEvents'],
            'upcomingEventsCount' => $stats['upcomingEventsCount'],
            'eventsNearingCapacity' => $stats['eventsNearingCapacity'],
            'averageAttendeesPerEvent' => $stats['averageAttendeesPerEvent']
        ]);
    }

    private function adminDashboard(): View
    {
        $stats = $this->getUserStatsUseCase->execute();
        
        return view('dashboard.admin', [
            'totalUsers' => $stats['totalUsers'],
            'totalEvents' => $stats['totalEvents'],
            'publishedEvents' => $stats['publishedEvents'],
            'draftEvents' => $stats['draftEvents'],
            'pendingEvents' => $stats['pendingEvents'],
            'totalRegistrations' => $stats['totalRegistrations'],
            'adminCount' => $stats['adminCount'],
            'organizerCount' => $stats['organizerCount'],
            'attendeeCount' => $stats['attendeeCount']
        ]);
    }
}

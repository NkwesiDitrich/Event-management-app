<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Application\UseCases\Event\GetAllEventsUseCase;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    private GetAllEventsUseCase $getAllEventsUseCase;
    private EventRepositoryInterface $eventRepository;

    public function __construct(
        GetAllEventsUseCase $getAllEventsUseCase,
        EventRepositoryInterface $eventRepository
    ) {
        $this->getAllEventsUseCase = $getAllEventsUseCase;
        $this->eventRepository = $eventRepository;
    }

    public function index(): View
    {
        $events = $this->getAllEventsUseCase->execute(false); // Include all events, not just published
        
        return view('admin.events.index', compact('events'));
    }

    public function approve(int $eventId): RedirectResponse
    {
        try {
            $event = $this->eventRepository->findById($eventId);
            
            if (!$event) {
                return back()->withErrors(['error' => 'Event not found']);
            }

            $event->publish();
            $this->eventRepository->save($event);
            
            return redirect()->route('admin.events')
                ->with('success', 'Event approved and published successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(int $eventId): RedirectResponse
    {
        try {
            $event = $this->eventRepository->findById($eventId);
            
            if (!$event) {
                return back()->withErrors(['error' => 'Event not found']);
            }

            $event->cancel();
            $this->eventRepository->save($event);
            
            return redirect()->route('admin.events')
                ->with('success', 'Event rejected successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function delete(int $eventId): RedirectResponse
    {
        try {
            $event = $this->eventRepository->findById($eventId);
            
            if (!$event) {
                return back()->withErrors(['error' => 'Event not found']);
            }

            $this->eventRepository->delete($event);
            
            return redirect()->route('admin.events')
                ->with('success', 'Event deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

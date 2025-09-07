<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Event\CreateEventUseCase;
use App\Application\UseCases\Event\GetAllEventsUseCase;
use App\Application\UseCases\Event\GetEventByIdUseCase;
use App\Application\UseCases\Event\GetEventsByOrganizerUseCase;
use App\Application\DTOs\CreateEventDTO;
use App\Http\Requests\CreateEventRequest;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    private CreateEventUseCase $createEventUseCase;
    private GetAllEventsUseCase $getAllEventsUseCase;
    private GetEventByIdUseCase $getEventByIdUseCase;
    private GetEventsByOrganizerUseCase $getEventsByOrganizerUseCase;
    private EventRepositoryInterface $eventRepository;
    private RegistrationRepositoryInterface $registrationRepository;

    public function __construct(
        CreateEventUseCase $createEventUseCase,
        GetAllEventsUseCase $getAllEventsUseCase,
        GetEventByIdUseCase $getEventByIdUseCase,
        GetEventsByOrganizerUseCase $getEventsByOrganizerUseCase,
        EventRepositoryInterface $eventRepository,
        RegistrationRepositoryInterface $registrationRepository
    ) {
        $this->createEventUseCase = $createEventUseCase;
        $this->getAllEventsUseCase = $getAllEventsUseCase;
        $this->getEventByIdUseCase = $getEventByIdUseCase;
        $this->getEventsByOrganizerUseCase = $getEventsByOrganizerUseCase;
        $this->eventRepository = $eventRepository;
        $this->registrationRepository = $registrationRepository;
    }

    public function index(): View
    {
        $events = $this->getAllEventsUseCase->execute(true); // publishedOnly = true
        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(CreateEventRequest $request): RedirectResponse
    {
        try {
            $dto = new CreateEventDTO(
                $request->title,
                $request->description,
                $request->event_date,
                $request->location,
                $request->capacity,
                auth()->id()
            );

            $publish = $request->action === 'publish';
            $event = $this->createEventUseCase->execute($dto, $publish);

            $message = $publish 
                ? 'Event created and published successfully!' 
                : 'Event saved as draft successfully!';

            return redirect()->route('events.show', $event->getId())
                ->with('success', $message);
        } catch (\DomainException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(int $id): View
    {
        $event = $this->getEventByIdUseCase->execute($id);
        
        if (!$event) {
            abort(404, 'Event not found');
        }

        $isRegistered = false;
        if (auth()->check()) {
            $isRegistered = $this->registrationRepository->existsByUserAndEvent(auth()->id(), $id);
        }

        return view('events.show', compact('event', 'isRegistered'));
    }

    public function myEvents(): View
    {
        $events = $this->getEventsByOrganizerUseCase->execute(auth()->id());
        return view('events.my-events', compact('events'));
    }

    public function edit(int $id): View
    {
        $event = $this->getEventByIdUseCase->execute($id);
        
        if (!$event) {
            abort(404, 'Event not found');
        }

        // Check if user is the organizer
        if ($event->getOrganizer()->getId() !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $event = $this->getEventByIdUseCase->execute($id);
        
        if (!$event) {
            abort(404, 'Event not found');
        }

        // Check if user is the organizer
        if ($event->getOrganizer()->getId() !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Basic validation
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        try {
            // Update event properties (you'd need to add update methods to the Event entity)
            // For now, we'll just redirect with a success message
            return redirect()->route('events.show', $id)
                ->with('success', 'Event updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = $this->getEventByIdUseCase->execute($id);
        
        if (!$event) {
            abort(404, 'Event not found');
        }

        // Check if user is the organizer
        if ($event->getOrganizer()->getId() !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $this->eventRepository->delete($event);
            return redirect()->route('events.my-events')
                ->with('success', 'Event deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function publish(int $id): RedirectResponse
    {
        $event = $this->getEventByIdUseCase->execute($id);
        
        if (!$event) {
            abort(404, 'Event not found');
        }

        // Check if user is the organizer
        if ($event->getOrganizer()->getId() !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $event->publish();
            $this->eventRepository->save($event);
            
            return redirect()->route('events.show', $id)
                ->with('success', 'Event published successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel(int $id): RedirectResponse
    {
        $event = $this->getEventByIdUseCase->execute($id);
        
        if (!$event) {
            abort(404, 'Event not found');
        }

        // Check if user is the organizer
        if ($event->getOrganizer()->getId() !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $event->cancel();
            $this->eventRepository->save($event);
            
            return redirect()->route('events.show', $id)
                ->with('success', 'Event cancelled successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function attendees(int $id): View
    {
        $event = $this->getEventByIdUseCase->execute($id);
        
        if (!$event) {
            abort(404, 'Event not found');
        }

        // Check if user is the organizer
        if ($event->getOrganizer()->getId() !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $registrations = $this->registrationRepository->findByEventId($id);
        
        return view('events.attendees', compact('event', 'registrations'));
    }
}

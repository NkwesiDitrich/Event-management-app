<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Event\CreateEventUseCase;
use App\Application\UseCases\Event\GetAllEventsUseCase;
use App\Application\DTOs\CreateEventDTO;
use App\Http\Requests\CreateEventRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    private CreateEventUseCase $createEventUseCase;
    private GetAllEventsUseCase $getAllEventsUseCase;

    public function __construct(
        CreateEventUseCase $createEventUseCase,
        GetAllEventsUseCase $getAllEventsUseCase
    ) {
        $this->createEventUseCase = $createEventUseCase;
        $this->getAllEventsUseCase = $getAllEventsUseCase;
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
        // You would need a GetEventByIdUseCase for this
        // For now, we'll implement this later
        return view('events.show', ['eventId' => $id]);
    }

    public function myEvents(): View
    {
        // You would need a GetEventsByOrganizerUseCase for this
        return view('events.my-events');
    }
}
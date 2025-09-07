@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Browse Events</h1>
    </div>
</div>

<div class="row">
    @foreach($events as $event)
    <div class="col-md-4 mb-4">
        <div class="card event-card h-100">
            <div class="card-body">
                <h5 class="card-title">{{ $event->getTitle() }}</h5>
                <p class="card-text">{{ Str::limit($event->getDescription(), 100) }}</p>
                
                <div class="event-details mb-3">
                    <p class="mb-1"><strong>Date:</strong> {{ $event->getEventDate()->getValue()->format('M j, Y g:i A') }}</p>
                    <p class="mb-1"><strong>Location:</strong> {{ $event->getLocation()->getValue() }}</p>
                    <p class="mb-1 capacity-display">
                        <strong>Capacity:</strong> 
                        {{ $event->getCurrentRegistrations() }}{{ $event->getCapacity()->isLimited() ? '/' . $event->getCapacity()->getLimit() : '' }} registered
                    </p>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="status-badge badge {{ $event->hasAvailableCapacity() ? 'bg-success' : 'bg-warning' }}">
                        {{ $event->hasAvailableCapacity() ? 'Available' : 'Full' }}
                    </span>
                    
                    <div class="btn-group">
                        <a href="{{ route('events.show', $event->getId()) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        
                        @auth
                        <button class="btn btn-sm {{ $event->isRegistered(auth()->user()->getId()) ? 'btn-secondary' : 'btn-primary' }} registration-btn"
                                data-event-id="{{ $event->getId() }}"
                                data-action="{{ $event->isRegistered(auth()->user()->getId()) ? 'unregister' : 'register' }}">
                            {{ $event->isRegistered(auth()->user()->getId()) ? 'Unregister' : 'Register' }}
                        </button>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(count($events) === 0)
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            No events available at the moment. Please check back later.
        </div>
    </div>
</div>
@endif
@endsection
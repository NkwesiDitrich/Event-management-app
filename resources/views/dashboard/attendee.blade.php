@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>My Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->name }}! Here's your event overview.</p>
    </div>
</div>

<div class="row mb-4">
    <!-- Quick Stats -->
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $upcomingEventsCount }}</h5>
                <p class="card-text">Available Events</p>
                <i class="fas fa-calendar-alt fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $registeredEventsCount }}</h5>
                <p class="card-text">My Registrations</p>
                <i class="fas fa-ticket-alt fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $attendedEventsCount }}</h5>
                <p class="card-text">Events Attended</p>
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Events -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upcoming Events You're Registered For</h5>
            </div>
            <div class="card-body">
                @if(count($registeredEvents) > 0)
                    <div class="list-group">
                        @foreach($registeredEvents as $event)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $event->getTitle() }}</h6>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $event->getEventDate()->getValue()->format('M j, Y g:i A') }}
                                    </p>
                                    <p class="mb-1 text-muted">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $event->getLocation()->getValue() }}
                                    </p>
                                    <small class="text-muted">
                                        {{ $event->getCurrentRegistrations() }} attendees registered
                                        @if($event->getCapacity()->isLimited())
                                            / {{ $event->getCapacity()->getLimit() }} capacity
                                        @endif
                                    </small>
                                </div>
                                <div class="btn-group-vertical">
                                    <a href="{{ route('events.show', $event->getId()) }}" class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger unregister-btn" 
                                            data-event-id="{{ $event->getId() }}">
                                        <i class="fas fa-times"></i> Unregister
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">You haven't registered for any events yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Events
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('events.index') }}" class="btn btn-primary mb-2">
                        <i class="fas fa-calendar"></i> Browse All Events
                    </a>
                    <a href="{{ route('registrations.index') }}" class="btn btn-outline-success mb-2">
                        <i class="fas fa-list"></i> My Registrations
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-user"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @if($totalRegistrations > 0)
                        <div class="timeline-item">
                            <i class="fas fa-ticket-alt text-success"></i>
                            <span class="text-muted">Total registrations: {{ $totalRegistrations }}</span>
                        </div>
                    @endif
                    @if($attendedEventsCount > 0)
                        <div class="timeline-item">
                            <i class="fas fa-check-circle text-info"></i>
                            <span class="text-muted">Events attended: {{ $attendedEventsCount }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle unregister buttons
    document.querySelectorAll('.unregister-btn').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            
            if (confirm('Are you sure you want to unregister from this event?')) {
                fetch(`/events/${eventId}/unregister`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while unregistering.');
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.timeline-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.timeline-item i {
    margin-right: 10px;
    width: 20px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
@endpush
@endsection

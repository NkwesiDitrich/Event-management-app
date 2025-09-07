@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Organizer Dashboard</h2>
        <p class="text-muted">Manage your events and track attendance.</p>
    </div>
</div>

<div class="row mb-4">
    <!-- Organizer Stats -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $totalEvents }}</h5>
                <p class="card-text">Total Events</p>
                <i class="fas fa-calendar fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $publishedEvents }}</h5>
                <p class="card-text">Published</p>
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $draftEvents }}</h5>
                <p class="card-text">Drafts</p>
                <i class="fas fa-edit fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $totalAttendees }}</h5>
                <p class="card-text">Total Attendees</p>
                <i class="fas fa-users fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- My Events List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Events</h5>
                <a href="{{ route('events.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>
            <div class="card-body">
                @if(count($myEvents) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Attendees</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myEvents as $event)
                                <tr>
                                    <td>
                                        <strong>{{ $event->getTitle() }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $event->getLocation()->getValue() }}</small>
                                    </td>
                                    <td>{{ $event->getEventDate()->getValue()->format('M j, Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($event->getStatus()) {
                                                'published' => 'success',
                                                'draft' => 'secondary',
                                                'cancelled' => 'danger',
                                                default => 'warning'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst($event->getStatus()) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $event->getCurrentRegistrations() }}
                                            @if($event->getCapacity()->isLimited())
                                                / {{ $event->getCapacity()->getLimit() }}
                                            @else
                                                / ∞
                                            @endif
                                        </span>
                                        @if($event->getCapacity()->isLimited() && ($event->getCurrentRegistrations() / $event->getCapacity()->getLimit()) >= 0.8)
                                            <i class="fas fa-exclamation-triangle text-warning" title="Nearing capacity"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('events.show', $event->getId()) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('events.edit', $event->getId()) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('events.attendees', $event->getId()) }}" class="btn btn-sm btn-outline-info" title="Attendees">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            @if($event->getStatus() === 'draft')
                                                <button class="btn btn-sm btn-outline-success publish-btn" data-event-id="{{ $event->getId() }}" title="Publish">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                        <p class="text-muted">You haven't created any events yet.</p>
                        <a href="{{ route('events.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Your First Event
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Stats -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('events.create') }}" class="btn btn-success mb-2">
                        <i class="fas fa-plus"></i> Create New Event
                    </a>
                    <a href="{{ route('events.my-events') }}" class="btn btn-outline-primary mb-2">
                        <i class="fas fa-list"></i> All My Events
                    </a>
                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-calendar"></i> Browse All Events
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-info">
                        <i class="fas fa-user"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Performance</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $upcomingEventsCount }}</h4>
                        <small class="text-muted">Upcoming Events</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $averageAttendeesPerEvent }}</h4>
                        <small class="text-muted">Avg. Attendees</small>
                    </div>
                </div>
                @if(count($eventsNearingCapacity) > 0)
                    <hr>
                    <div class="alert alert-warning py-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>{{ count($eventsNearingCapacity) }}</strong> event(s) nearing capacity
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @if($totalEvents > 0)
                        <div class="timeline-item">
                            <i class="fas fa-calendar text-primary"></i>
                            <span class="text-muted">{{ $totalEvents }} events created</span>
                        </div>
                    @endif
                    @if($totalAttendees > 0)
                        <div class="timeline-item">
                            <i class="fas fa-users text-success"></i>
                            <span class="text-muted">{{ $totalAttendees }} total registrations</span>
                        </div>
                    @endif
                    @if($cancelledEvents > 0)
                        <div class="timeline-item">
                            <i class="fas fa-times-circle text-danger"></i>
                            <span class="text-muted">{{ $cancelledEvents }} events cancelled</span>
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
    // Handle publish buttons
    document.querySelectorAll('.publish-btn').forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            
            if (confirm('Are you sure you want to publish this event?')) {
                fetch(`/events/${eventId}/publish`, {
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
                    alert('An error occurred while publishing the event.');
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

.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
@endpush
@endsection

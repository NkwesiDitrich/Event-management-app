@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>My Registrations</h2>
                <p class="text-muted">Manage your event registrations.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" class="form-control" id="registrationSearch" placeholder="Search registrations...">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
            <button type="button" class="btn btn-outline-primary" data-filter="upcoming">Upcoming</button>
            <button type="button" class="btn btn-outline-primary" data-filter="past">Past</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">My Event Registrations ({{ count($registrations) }})</h5>
    </div>
    <div class="card-body">
        @if(count($registrations) > 0)
            <div class="row" id="registrationsContainer">
                @foreach($registrations as $registration)
                @php
                    $event = $registration->getEvent();
                    $isUpcoming = $event->getEventDate()->getValue() > now();
                    $timeStatus = $isUpcoming ? 'upcoming' : 'past';
                @endphp
                <div class="col-md-6 mb-4 registration-card" data-filter="{{ $timeStatus }}">
                    <div class="card h-100 {{ $isUpcoming ? 'border-primary' : 'border-secondary' }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $event->getTitle() }}</h6>
                            @if($isUpcoming)
                                <span class="badge bg-success">Upcoming</span>
                            @else
                                <span class="badge bg-secondary">Attended</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <p class="card-text">{{ Str::limit($event->getDescription(), 100) }}</p>
                            </div>
                            
                            <div class="event-details">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar text-primary me-2"></i>
                                    <span>{{ $event->getEventDate()->getValue()->format('M j, Y g:i A') }}</span>
                                </div>
                                
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <span>{{ $event->getLocation()->getValue() }}</span>
                                </div>
                                
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user text-info me-2"></i>
                                    <span>Organized by {{ $event->getOrganizerName() }}</span>
                                </div>
                                
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-users text-success me-2"></i>
                                    <span>
                                        {{ $event->getCurrentRegistrations() }} attendees
                                        @if($event->getCapacity()->isLimited())
                                            / {{ $event->getCapacity()->getLimit() }} capacity
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="registration-info bg-light p-2 rounded mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Registered on {{ $registration->getRegistrationDate()->format('M j, Y g:i A') }}
                                </small>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('events.show', $event->getId()) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                
                                @if($isUpcoming)
                                    <button class="btn btn-outline-danger btn-sm unregister-btn" 
                                            data-event-id="{{ $event->getId() }}"
                                            data-event-title="{{ $event->getTitle() }}">
                                        <i class="fas fa-times"></i> Unregister
                                    </button>
                                @else
                                    <span class="badge bg-info">Event Completed</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No Registrations Yet</h4>
                <p class="text-muted mb-4">You haven't registered for any events yet. Start exploring events to join!</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i> Browse Events
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Unregister Confirmation Modal -->
<div class="modal fade" id="unregisterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Unregistration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> You are about to unregister from this event.
                </div>
                <p>Are you sure you want to unregister from <strong id="eventTitle"></strong>?</p>
                <p class="text-muted">This action cannot be undone, but you can register again if the event is still accepting registrations.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmUnregister">
                    <i class="fas fa-times"></i> Unregister
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('registrationSearch');
    const registrationCards = document.querySelectorAll('.registration-card');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        registrationCards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter functionality
    const filterButtons = document.querySelectorAll('[data-filter]');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter cards
            registrationCards.forEach(card => {
                const cardFilter = card.dataset.filter;
                card.style.display = (filter === 'all' || cardFilter === filter) ? '' : 'none';
            });
        });
    });

    // Unregister functionality
    let currentEventId = null;
    const unregisterModal = new bootstrap.Modal(document.getElementById('unregisterModal'));
    
    document.querySelectorAll('.unregister-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentEventId = this.dataset.eventId;
            const eventTitle = this.dataset.eventTitle;
            
            document.getElementById('eventTitle').textContent = eventTitle;
            unregisterModal.show();
        });
    });

    document.getElementById('confirmUnregister').addEventListener('click', function() {
        if (currentEventId) {
            fetch(`/registrations/${currentEventId}/unregister`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    unregisterModal.hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to unregister'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while unregistering.');
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.registration-card {
    transition: transform 0.2s ease-in-out;
}

.registration-card:hover {
    transform: translateY(-2px);
}

.event-details i {
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

.border-primary {
    border-color: #007bff !important;
}

.border-secondary {
    border-color: #6c757d !important;
}

.registration-info {
    background-color: #f8f9fa !important;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    border-radius: 0.25rem;
}
</style>
@endpush
@endsection

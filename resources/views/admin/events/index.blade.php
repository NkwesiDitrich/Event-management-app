@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Event Management</h2>
                <p class="text-muted">Review and manage all events in the system.</p>
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
            <input type="text" class="form-control" id="eventSearch" placeholder="Search events...">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" data-filter="all">All Events</button>
            <button type="button" class="btn btn-outline-primary" data-filter="published">Published</button>
            <button type="button" class="btn btn-outline-primary" data-filter="draft">Drafts</button>
            <button type="button" class="btn btn-outline-primary" data-filter="pending">Pending</button>
            <button type="button" class="btn btn-outline-primary" data-filter="cancelled">Cancelled</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Events ({{ count($events) }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="eventsTable">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Organizer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Attendees</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr data-status="{{ $event->getStatus() }}">
                        <td>
                            <div>
                                <strong>{{ $event->getTitle() }}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $event->getLocation()->getValue() }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2">
                                    {{ strtoupper(substr($event->getOrganizerName(), 0, 2)) }}
                                </div>
                                <span>{{ $event->getOrganizerName() }}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{ $event->getEventDate()->getValue()->format('M j, Y') }}
                                <br>
                                <small class="text-muted">{{ $event->getEventDate()->getValue()->format('g:i A') }}</small>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = match($event->getStatus()) {
                                    'published' => 'success',
                                    'draft' => 'secondary',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'info'
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
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('events.show', $event->getId()) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($event->getStatus() === 'pending')
                                <form method="POST" action="{{ route('admin.events.approve', $event->getId()) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-success" 
                                            title="Approve Event"
                                            onclick="return confirm('Are you sure you want to approve this event?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.events.reject', $event->getId()) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-warning" 
                                            title="Reject Event"
                                            onclick="return confirm('Are you sure you want to reject this event?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                                
                                @if(in_array($event->getStatus(), ['published', 'draft']))
                                <form method="POST" action="{{ route('admin.events.delete', $event->getId()) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Delete Event"
                                            onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('eventSearch');
    const table = document.getElementById('eventsTable');
    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
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
            
            // Filter rows
            rows.forEach(row => {
                const status = row.dataset.status;
                row.style.display = (filter === 'all' || status === filter) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
@endsection

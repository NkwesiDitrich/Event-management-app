@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Admin Dashboard</h2>
        <p class="text-muted">System overview and management.</p>
    </div>
</div>

<div class="row mb-4">
    <!-- System Stats -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $totalUsers }}</h5>
                <p class="card-text">Total Users</p>
                <i class="fas fa-users fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $totalEvents }}</h5>
                <p class="card-text">Total Events</p>
                <i class="fas fa-calendar fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $totalRegistrations }}</h5>
                <p class="card-text">Registrations</p>
                <i class="fas fa-ticket-alt fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5 class="card-title">{{ $pendingEvents }}</h5>
                <p class="card-text">Pending Review</p>
                <i class="fas fa-clock fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- User Distribution -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">User Distribution</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="text-danger">{{ $adminCount }}</h5>
                        <small class="text-muted">Admins</small>
                    </div>
                    <div class="col-4">
                        <h5 class="text-warning">{{ $organizerCount }}</h5>
                        <small class="text-muted">Organizers</small>
                    </div>
                    <div class="col-4">
                        <h5 class="text-info">{{ $attendeeCount }}</h5>
                        <small class="text-muted">Attendees</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Status -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Event Status</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="text-success">{{ $publishedEvents }}</h5>
                        <small class="text-muted">Published</small>
                    </div>
                    <div class="col-4">
                        <h5 class="text-secondary">{{ $draftEvents }}</h5>
                        <small class="text-muted">Drafts</small>
                    </div>
                    <div class="col-4">
                        <h5 class="text-warning">{{ $pendingEvents }}</h5>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">System Health</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">System Status</span>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Database</span>
                    <span class="badge bg-success">Connected</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Last Backup</span>
                    <span class="badge bg-info">Today</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent System Activity</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user-plus text-success me-2"></i>
                            <strong>New user registered</strong>
                            <br>
                            <small class="text-muted">User joined as attendee</small>
                        </div>
                        <small class="text-muted">2 minutes ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-plus text-primary me-2"></i>
                            <strong>Event created</strong>
                            <br>
                            <small class="text-muted">Tech Conference 2024 awaiting approval</small>
                        </div>
                        <small class="text-muted">15 minutes ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-ticket-alt text-info me-2"></i>
                            <strong>Multiple registrations</strong>
                            <br>
                            <small class="text-muted">10 new registrations for Summer Festival</small>
                        </div>
                        <small class="text-muted">1 hour ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user-shield text-warning me-2"></i>
                            <strong>Role updated</strong>
                            <br>
                            <small class="text-muted">User promoted to organizer</small>
                        </div>
                        <small class="text-muted">2 hours ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Admin Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users') }}" class="btn btn-danger mb-2">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.events') }}" class="btn btn-warning mb-2">
                        <i class="fas fa-calendar"></i> Manage Events
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-info mb-2">
                        <i class="fas fa-chart-bar"></i> View Reports
                    </a>
                    <a href="{{ route('events.index') }}" class="btn btn-outline-primary mb-2">
                        <i class="fas fa-eye"></i> Browse Events
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-user"></i> Profile Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Stats</h6>
            </div>
            <div class="card-body">
                @if($totalUsers > 0)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Registration Rate</span>
                        <span class="badge bg-success">
                            {{ $totalRegistrations > 0 ? round(($totalRegistrations / $totalUsers) * 100, 1) : 0 }}%
                        </span>
                    </div>
                @endif
                @if($totalEvents > 0)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Event Approval Rate</span>
                        <span class="badge bg-info">
                            {{ round(($publishedEvents / $totalEvents) * 100, 1) }}%
                        </span>
                    </div>
                @endif
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Avg. Attendees/Event</span>
                    <span class="badge bg-primary">
                        {{ $totalEvents > 0 ? round($totalRegistrations / $totalEvents, 1) : 0 }}
                    </span>
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        @if($pendingEvents > 0)
        <div class="card mt-3">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle"></i> System Alerts
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning py-2 mb-2">
                    <strong>{{ $pendingEvents }}</strong> event(s) awaiting approval
                </div>
                <a href="{{ route('admin.events') }}" class="btn btn-sm btn-warning">
                    Review Events
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

.badge {
    font-size: 0.75em;
}

.text-muted {
    color: #6c757d !important;
}
</style>
@endpush
@endsection

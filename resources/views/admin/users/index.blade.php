@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>User Management</h2>
                <p class="text-muted">Manage user accounts and roles.</p>
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
            <input type="text" class="form-control" id="userSearch" placeholder="Search users...">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" data-filter="all">All Users</button>
            <button type="button" class="btn btn-outline-primary" data-filter="admin">Admins</button>
            <button type="button" class="btn btn-outline-primary" data-filter="organizer">Organizers</button>
            <button type="button" class="btn btn-outline-primary" data-filter="attendee">Attendees</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Users ({{ count($users) }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="usersTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr data-role="{{ $user->getRole()->getValue() }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($user->getName(), 0, 2)) }}
                                </div>
                                <div>
                                    <strong>{{ $user->getName() }}</strong>
                                    @if($user->getId() === auth()->id())
                                        <span class="badge bg-info ms-2">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->getEmail()->getValue() }}</td>
                        <td>
                            @php
                                $roleClass = match($user->getRole()->getValue()) {
                                    'admin' => 'danger',
                                    'organizer' => 'warning',
                                    'attendee' => 'info',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $roleClass }}">
                                {{ ucfirst($user->getRole()->getValue()) }}
                            </span>
                        </td>
                        <td>{{ $user->getCreatedAt()->format('M j, Y') }}</td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($user->getId() !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.role', $user->getId()) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="form-select form-select-sm d-inline-block w-auto me-2" onchange="this.form.submit()">
                                        <option value="admin" {{ $user->getRole()->getValue() === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="organizer" {{ $user->getRole()->getValue() === 'organizer' ? 'selected' : '' }}>Organizer</option>
                                        <option value="attendee" {{ $user->getRole()->getValue() === 'attendee' ? 'selected' : '' }}>Attendee</option>
                                    </select>
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
    const searchInput = document.getElementById('userSearch');
    const table = document.getElementById('usersTable');
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
                const role = row.dataset.role;
                row.style.display = (filter === 'all' || role === filter) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
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
</style>
@endpush
@endsection

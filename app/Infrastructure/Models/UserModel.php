<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserModel extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the events organized by this user.
     */
    public function organizedEvents(): HasMany
    {
        return $this->hasMany(EventModel::class, 'organizer_id');
    }

    /**
     * Get the registrations made by this user.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(RegistrationModel::class, 'user_id');
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include active users (those who have created events or registered).
     */
    public function scopeActive($query)
    {
        return $query->whereHas('organizedEvents')
                    ->orWhereHas('registrations');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an organizer.
     */
    public function isOrganizer(): bool
    {
        return $this->role === 'organizer' || $this->isAdmin();
    }

    /**
     * Check if user is an attendee.
     */
    public function isAttendee(): bool
    {
        return $this->role === 'attendee';
    }
}
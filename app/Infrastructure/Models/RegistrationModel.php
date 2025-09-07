<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationModel extends Model
{
    use HasFactory;

    protected $table = 'registrations';

    protected $fillable = [
        'user_id',
        'event_id',
        'registered_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /**
     * Get the user who registered for the event.
     */
    public function userModel(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /**
     * Get the event that was registered for.
     */
    public function eventModel(): BelongsTo
    {
        return $this->belongsTo(EventModel::class, 'event_id');
    }

    /**
     * Scope a query to only include registrations for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope a query to only include registrations by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include recent registrations.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('registered_at', '>=', now()->subDays($days));
    }
}
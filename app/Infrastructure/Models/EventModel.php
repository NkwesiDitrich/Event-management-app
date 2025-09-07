<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventModel extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'capacity',
        'current_registrations',
        'status',
        'organizer_id'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'capacity' => 'integer',
        'current_registrations' => 'integer',
    ];

    /**
     * Get the organizer (user) who created this event.
     */
    public function organizerModel(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'organizer_id');
    }

    /**
     * Get all registrations for this event.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(RegistrationModel::class, 'event_id');
    }

    /**
     * Scope a query to only include published events.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>', now());
    }

    /**
     * Scope a query to only include events with available capacity.
     */
    public function scopeWithAvailableCapacity($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('capacity')
              ->orWhereRaw('current_registrations < capacity');
        });
    }
}
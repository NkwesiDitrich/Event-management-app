<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Event\Entities\Event;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Infrastructure\Models\EventModel;
use App\Domain\User\ValueObjects\UserRole;
use App\Domain\Event\ValueObjects\EventDate;
use App\Domain\Event\ValueObjects\EventLocation;
use App\Domain\Event\ValueObjects\EventCapacity;
use App\Domain\User\Entities\User;

class EloquentEventRepository implements EventRepositoryInterface
{
    public function save(Event $event): void
    {
        $model = EventModel::find($event->getId()) ?? new EventModel();

        $model->fill([
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'event_date' => $event->getEventDate()->getValue()->format('Y-m-d H:i:s'),
            'location' => $event->getLocation()->getValue(),
            'capacity' => $event->getCapacity()->getLimit(),
            'current_registrations' => $event->getCurrentRegistrations(),
            'status' => $event->getStatus(),
            'organizer_id' => $event->getOrganizer()->getId(),
        ]);

        $model->save();

        if (!$event->getId()) {
            $event->setId($model->id);
        }
    }

    public function findById(int $id): ?Event
    {
        $model = EventModel::with('organizerModel')->find($id);
        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByOrganizerId(int $organizerId): array
    {
        $models = EventModel::with('organizerModel')
            ->where('organizer_id', $organizerId)
            ->get();
                    
        return $models->map(function ($model) {
            return $this->mapToEntity($model);
        })->toArray();
    }

    public function findUpcomingEvents(): array
    {
        $models = EventModel::with('organizerModel')
                    ->where('event_date', '>', now())
                    ->where('status', 'published')
                    ->orderBy('event_date', 'asc')
                    ->get();
                    
        return $models->map(function ($model) {
            return $this->mapToEntity($model);
        })->toArray();
    }

    public function findEventsNearingCapacity(): array
    {
        $models = EventModel::with('organizerModel')
                    ->where('status', 'published')
                    ->whereNotNull('capacity')
                    ->whereRaw('current_registrations >= capacity * 0.8') // Events 80% full or more
                    ->get();
                    
        return $models->map(function ($model) {
            return $this->mapToEntity($model);
        })->toArray();
    }

    public function delete(Event $event): void
    {
        EventModel::destroy($event->getId());
    }

    private function mapToEntity(EventModel $model): Event
{
    // First, map the organizer user (simplified without password)
    $organizer = new User(
        $model->organizerModel->id,
        $model->organizerModel->name,
        new \App\Domain\User\ValueObjects\Email($model->organizerModel->email),
        \App\Domain\User\ValueObjects\UserRole::fromString($model->organizerModel->role),
        '' // We don't need the password hash for event context
    );

    // Create value objects
    $eventDate = new \App\Domain\Event\ValueObjects\EventDate($model->event_date);
    $location = new \App\Domain\Event\ValueObjects\EventLocation($model->location);
    $capacity = new \App\Domain\Event\ValueObjects\EventCapacity($model->capacity);

    // Create the event entity
    $event = new Event(
        $model->id,
        $model->title,
        $model->description,
        $eventDate,
        $location,
        $capacity,
        $organizer
    );

    // Set additional properties that aren't in the constructor
    $event->setCurrentRegistrations($model->current_registrations);
    $event->setStatus($model->status);
    
    // Handle timestamps - you might need to add setter methods for these too
    // $event->setCreatedAt(new \DateTime($model->created_at));
    // $event->setUpdatedAt(new \DateTime($model->updated_at));

    return $event;
}
}
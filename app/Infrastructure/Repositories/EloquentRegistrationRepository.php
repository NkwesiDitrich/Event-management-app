<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Registration\Entities\Registration;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use App\Infrastructure\Models\RegistrationModel;
use App\Domain\User\Entities\User;
use App\Domain\Event\Entities\Event;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserRole;
use App\Domain\Event\ValueObjects\EventDate;
use App\Domain\Event\ValueObjects\EventLocation;
use App\Domain\Event\ValueObjects\EventCapacity;
use DateTimeImmutable;

class EloquentRegistrationRepository implements RegistrationRepositoryInterface
{
    public function save(Registration $registration): void
    {
        $model = RegistrationModel::find($registration->getId()) ?? new RegistrationModel();

        $model->fill([
            'user_id' => $registration->getUser()->getId(),
            'event_id' => $registration->getEvent()->getId(),
            'registered_at' => $registration->getRegisteredAt()->format('Y-m-d H:i:s'),
        ]);

        $model->save();

        if (!$registration->getId()) {
            $registration->setId($model->id);
        }
    }

    public function findById(int $id): ?Registration
    {
        $model = RegistrationModel::with(['userModel', 'eventModel.organizerModel'])->find($id);
        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByEventId(int $eventId): array
    {
        $models = RegistrationModel::with(['userModel', 'eventModel.organizerModel'])
            ->where('event_id', $eventId)
            ->get();
                    
        return $models->map(function ($model) {
            return $this->mapToEntity($model);
        })->toArray();
    }

    public function findByUserId(int $userId): array
    {
        // FIXED: Changed '$eventId' to '$userId' in the where clause
        $models = RegistrationModel::with(['userModel', 'eventModel.organizerModel'])
            ->where('user_id', $userId) // This was the bug - using $eventId instead of $userId
            ->get();
                    
        return $models->map(function ($model) {
            return $this->mapToEntity($model);
        })->toArray();
    }

    public function existsByUserAndEvent(int $userId, int $eventId): bool
    {
        return RegistrationModel::where('user_id', $userId)
                ->where('event_id', $eventId)
                ->exists();
    }

    public function delete(Registration $registration): void
    {
        RegistrationModel::destroy($registration->getId());
    }

    private function mapToEntity(RegistrationModel $model): Registration
    {
        // Map user
        $user = new User(
            $model->userModel->id,
            $model->userModel->name,
            new Email($model->userModel->email),
            UserRole::fromString($model->userModel->role),
            $model->userModel->password
        );

        // Map event organizer
        $organizer = new User(
            $model->eventModel->organizerModel->id,
            $model->eventModel->organizerModel->name,
            new Email($model->eventModel->organizerModel->email),
            UserRole::fromString($model->eventModel->organizerModel->role),
            $model->eventModel->organizerModel->password
        );

        // Map event
        $eventDate = new EventDate($model->eventModel->event_date);
        $location = new EventLocation($model->eventModel->location);
        $capacity = new EventCapacity($model->eventModel->capacity);

        $event = new Event(
            $model->eventModel->id,
            $model->eventModel->title,
            $model->eventModel->description,
            $eventDate,
            $location,
            $capacity,
            $organizer
        );
        
        // Set additional event properties
        $event->setCurrentRegistrations($model->eventModel->current_registrations);
        $event->setStatus($model->eventModel->status);

        // Create and return registration
        return new Registration(
            $model->id,
            $user,
            $event,
            new DateTimeImmutable($model->registered_at)
        );
    }
}
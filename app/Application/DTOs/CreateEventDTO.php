<?php

namespace App\Application\DTOs;

class CreateEventDTO
{
    public string $title;
    public string $description;
    public string $eventDate;
    public string $location;
    public ?int $capacity;
    public int $organizerId;

    public function __construct(
        string $title,
        string $description,
        string $eventDate,
        string $location,
        ?int $capacity,
        int $organizerId
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->eventDate = $eventDate;
        $this->location = $location;
        $this->capacity = $capacity;
        $this->organizerId = $organizerId;
    }
}
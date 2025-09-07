<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class CreateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return Auth::check() && $user && $user->isOrganizer();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:500',
            'capacity' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The event title is required.',
            'title.max' => 'The event title may not be greater than 255 characters.',
            'description.required' => 'The event description is required.',
            'event_date.required' => 'The event date is required.',
            'event_date.date' => 'The event date must be a valid date.',
            'event_date.after' => 'The event date must be in the future.',
            'location.required' => 'The event location is required.',
            'location.max' => 'The event location may not be greater than 500 characters.',
            'capacity.integer' => 'The capacity must be a number.',
            'capacity.min' => 'The capacity must be at least 1.',
        ];
    }
}

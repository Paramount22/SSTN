<?php

namespace App\Http\Requests\Admin;

use App\Models\Event;
use App\Models\EventAttachment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'ends_on' => $this->filled('ends_on') ? $this->input('ends_on') : null,
        ]);
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id;

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash', Rule::unique('events', 'slug')->ignore($eventId)],
            'type' => ['required', Rule::in(array_keys(Event::TYPES))],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'date_text' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'organizers' => ['nullable', 'string', 'max:255'],
            'intro' => ['nullable', 'string', 'max:2000'],
            'body' => ['nullable', 'string', 'max:20000'],
            'is_published' => ['boolean'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => [File::types(EventAttachment::ALLOWED_EXTENSIONS)->max(20 * 1024)],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'názov', 'slug' => 'URL adresa', 'type' => 'typ', 'starts_on' => 'dátum začiatku',
            'ends_on' => 'dátum konca', 'date_text' => 'dátum (text)', 'location' => 'miesto',
            'organizers' => 'organizátori', 'intro' => 'úvodný text', 'body' => 'text', 'files.*' => 'súbor',
        ];
    }

    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);
        if (is_array($data)) {
            unset($data['files']);
            if (blank($data['slug'] ?? null)) {
                unset($data['slug']);
            }
        }

        return $data;
    }
}

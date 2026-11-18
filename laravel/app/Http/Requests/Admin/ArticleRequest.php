<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'remove_image' => $this->boolean('remove_image'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash', Rule::unique('articles', 'slug')->ignore($this->route('article')?->id)],
            'published_on' => ['required', 'date'],
            'body' => ['nullable', 'string', 'max:20000'],
            'image' => ['nullable', File::image()->types(Article::IMAGE_TYPES)->extensions(Article::IMAGE_TYPES)->max(Article::IMAGE_MAX_KB)],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'remove_image' => ['boolean'],
            'is_published' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'nadpis', 'slug' => 'URL adresa', 'published_on' => 'dátum', 'body' => 'text',
            'image' => 'fotografia', 'image_alt' => 'popis fotografie',
        ];
    }

    /** Fields stored directly on the article (the photo is handled separately). */
    public function articleData(): array
    {
        $data = $this->safe()->except(['image', 'remove_image']);
        if (blank($data['slug'] ?? null)) {
            unset($data['slug']);
        }

        return $data;
    }
}

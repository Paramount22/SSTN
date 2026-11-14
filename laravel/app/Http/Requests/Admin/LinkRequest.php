<?php

namespace App\Http\Requests\Admin;

use App\Models\Link;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'group' => ['required', Rule::in(array_keys(Link::GROUPS))],
            'label' => ['required', 'string', 'max:160'],
            'url' => ['required', 'string', 'max:500', 'regex:#^(https?://|/|mailto:)#i'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return ['url.regex' => 'Adresa musí začínať na https://, http://, / alebo mailto:.'];
    }

    public function attributes(): array
    {
        return ['group' => 'skupina', 'label' => 'názov', 'url' => 'adresa', 'description' => 'popis', 'sort' => 'poradie'];
    }
}

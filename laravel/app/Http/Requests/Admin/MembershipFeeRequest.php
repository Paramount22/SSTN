<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MembershipFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'price' => str_replace(',', '.', (string) $this->input('price')),
        ]);
    }

    public function rules(): array
    {
        return [
            'kind' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999'],
            'votes' => ['required', 'integer', 'min:0', 'max:999'],
            'is_featured' => ['boolean'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function attributes(): array
    {
        return ['kind' => 'druh členstva', 'price' => 'príspevok', 'votes' => 'počet hlasov', 'sort' => 'poradie'];
    }
}

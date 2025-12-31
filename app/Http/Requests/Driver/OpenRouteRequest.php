<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class OpenRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            && ($this->user()->type ?? null) === 'driver';
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ];
    }
}

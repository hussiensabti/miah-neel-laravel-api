<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity'  => ['required', 'integer', 'min:1'],
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'notes'     => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required'  => 'يرجى تحديد عدد القوارير.',
            'quantity.min'       => 'يجب أن يكون العدد واحدًا على الأقل.',
            'latitude.required'  => 'إحداثيات الموقع مطلوبة.',
            'longitude.required' => 'إحداثيات الموقع مطلوبة.',
            'latitude.numeric'   => 'الموقع يجب أن يكون رقمًا.',
            'longitude.numeric'  => 'الموقع يجب أن يكون رقمًا.',
        ];
    }
}

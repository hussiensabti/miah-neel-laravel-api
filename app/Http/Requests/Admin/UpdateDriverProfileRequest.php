<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // لأن لديك ميدلوير أدمن
    }

    public function rules(): array
    {
        return [
            'car_type' => ['nullable', 'string', 'max:150'],

            // صور جديدة (اختيارية)
            'documents.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // إضافة لترات جديدة
            'add_liters' => ['nullable', 'numeric', 'min:0.1'],
        ];
    }

    public function messages(): array
    {
        return [
            'documents.*.image' => 'الوثائق يجب أن تكون صور فقط.',
            'documents.*.max' => 'حجم الصورة يجب ألا يتجاوز 5MB.',
            'add_liters.numeric' => 'اللترات يجب أن تكون رقم.',
            'add_liters.min' => 'اللترات يجب أن تكون أكبر من صفر.',
        ];
    }
}

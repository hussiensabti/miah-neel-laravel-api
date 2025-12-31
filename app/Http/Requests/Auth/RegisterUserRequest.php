<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * =========================
     * Validation Rules
     * =========================
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'phone' => [
                'required',
                'regex:/^07\d{9}$/',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ];
    }

    /**
     * =========================
     * Custom Messages (Arabic)
     * =========================
     */
    public function messages(): array
    {
        return [
            // الاسم
            'name.required' => 'أدخل الاسم',
            'name.max'      => 'الاسم طويل جدًا',

            // الهاتف
            'phone.required'=> 'أدخل رقم الهاتف',
            'phone.regex'   => 'أدخل رقم هاتف عراقي صحيح (07XXXXXXXXX)',
            'phone.unique'  => 'رقم الهاتف مسجل مسبقًا',

            // كلمة المرور
            'password.required'   => 'أدخل كلمة المرور',
            'password.min'        => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ];
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * =========================
     * Rules (Validation)
     * =========================
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'regex:/^07\d{9}$/',
            ],
            'password' => [
                'required',
                'string',
                'min:4',
            ],
        ];
    }

    /**
     * =========================
     * Custom Messages
     * =========================
     */
    public function messages(): array
    {
        return [
            'phone.required'   => 'أدخل رقم الهاتف',
            'phone.regex'      => 'أدخل رقم هاتف عراقي صحيح',
            'password.required'=> 'أدخل كلمة المرور',
            'password.min'     => 'كلمة المرور قصيرة',
        ];
    }

    /**
     * =========================
     * Authenticate User
     * =========================
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('phone', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            // ❗ خطأ تسجيل الدخول (مو Validation)
            abort(401, 'رقم الهاتف أو كلمة المرور غير صحيحة');
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * =========================
     * Rate Limiting
     * =========================
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'phone' => "محاولات كثيرة، انتظر {$seconds} ثانية",
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::lower($this->input('phone')).'|'.$this->ip();
    }
}

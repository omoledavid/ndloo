<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class VerifyEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $cacheValue = intval(Cache::get("EMAIL_VERIFICATION_{$this->email}"));

        return [
            'email' => 'required|email',
            'token' => ['required', 'integer', function ($attr, $val, $fail) use ($cacheValue) {
                if (intval($val) !== $cacheValue) {
                    return $fail('Invalid verification code');
                }
            }]
        ];
    }
}

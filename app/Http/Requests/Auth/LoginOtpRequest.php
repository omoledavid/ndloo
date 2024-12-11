<?php

namespace App\Http\Requests\Auth;

use App\Contracts\Enums\OtpCodeTypes;
use App\Models\OtpCode;
use Illuminate\Foundation\Http\FormRequest;

class LoginOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getAccount(): ?OtpCode
    {
        return OtpCode::query()
            ->where([
                ['email', $this->email],
                ['type', OtpCodeTypes::LOGIN->value],
                ['token', $this->token],
            ])
            ->with('user')
            ->first();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'token' => 'required|integer|min:1000|max:9999',
        ];
    }
}

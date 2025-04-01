<?php

namespace App\Http\Requests\Auth;

use App\Contracts\Enums\UserStates;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function authData(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:filter',
            'password' => 'required|string',
        ];
    }
}

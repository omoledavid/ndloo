<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DetailSignupRequest extends FormRequest
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
        return [
            'firstname' => 'required|string|bail',
            'lastname' => 'required|string|bail',
            'age' => 'required|integer|min:18|max:80|bail',
            'country' => 'required|integer|bail',
            'email' => 'required|email:filter|unique:App\Models\User',
            'phone' => 'required|string',
            'gender' => ['required', Rule::in(['male', 'female'])],
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ];
    }
}

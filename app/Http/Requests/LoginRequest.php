<?php

namespace App\Http\Requests;

class LoginRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Returns true if the user is authorized, false otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define the validation rules that apply to the request.
     *
     * @return array<string, string> The array of validation rules.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Define custom error messages for validation failures.
     *
     * @return array<string, string> The array of custom error messages.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'An email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'A password is required.',
        ];
    }
}

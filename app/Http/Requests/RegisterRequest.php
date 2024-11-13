<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class RegisterRequest extends BaseFormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users|max:191',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required|in:male,female,other',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:191',
            'profile_picture' => 'nullable|string|max:191',
            'agree_terms' => 'required|accepted',
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
            'email.required' => 'An email is required.',
            'email.unique' => 'This email is already taken.',
            'password.confirmed' => 'Password confirmation does not match.',
            'gender.in' => 'The selected gender is invalid.',
            'agree_terms.required' => 'You must agree to the terms.',
            'agree_terms.accepted' => 'You must agree to the terms.',
        ];
    }
}

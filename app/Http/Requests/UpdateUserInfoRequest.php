<?php

namespace App\Http\Requests;

class UpdateUserInfoRequest extends BaseFormRequest
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
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $this->user()->id,
            'gender' => 'required|in:male,female,other',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:191',
            'profile_picture' => 'nullable|url|max:191',
        ];
    }

    /**
     * Define custom messages for validation errors.
     *
     * @return array<string, string> The array of custom error messages.
     */
    public function messages(): array
    {
        return [
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'gender.in' => 'Gender must be one of male, female, or other.',
            'birth_date.date' => 'Please provide a valid birth date.',
            'profile_picture.url' => 'Please provide a valid URL for the profile picture.',
        ];
    }
}

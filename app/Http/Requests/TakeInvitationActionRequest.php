<?php

namespace App\Http\Requests;

class TakeInvitationActionRequest extends BaseFormRequest
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
            'action' => 'required|in:accept,decline'
        ];
    }

    /**
     * Define custom error messages for validation failures.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'action.required' => 'An action is required.',
            'action.in' => 'The action must be either accept or decline.'
        ];
    }
}

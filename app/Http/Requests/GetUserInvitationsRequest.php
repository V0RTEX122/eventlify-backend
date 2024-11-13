<?php

namespace App\Http\Requests;

class GetUserInvitationsRequest extends BaseFormRequest
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
     * @return array<string, string> An array of validation rules for each request attribute.
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:pending,accepted,declined',
            'type' => 'sometimes|in:sent,received',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string> An array of custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The selected status is invalid. Valid options are pending, accepted, or declined.',
            'type.in' => 'The selected type is invalid. Valid options are sent or received.',
        ];
    }
}

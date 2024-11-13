<?php

namespace App\Http\Requests;

class EventRequest extends BaseFormRequest
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
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'date' => 'required|date|after:today',
            'location' => 'nullable|string|max:191',
            'visibility' => 'nullable|in:public,private',
            'created_by' => 'required|exists:users,id',
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
            'title.required' => 'The title of the event is required.',
            'title.string' => 'The title must be a string.',
            'description.string' => 'The description must be a string.',
            'date.required' => 'The date of the event is required.',
            'date.date' => 'Please provide a valid date.',
            'date.after' => 'The event date must be a future date.',
            'location.string' => 'The location must be a string.',
            'visibility.in' => 'Visibility must be either public or private.',
            'created_by.required' => 'The creator ID is required.',
            'created_by.exists' => 'The selected creator ID is invalid.',
        ];
    }
}

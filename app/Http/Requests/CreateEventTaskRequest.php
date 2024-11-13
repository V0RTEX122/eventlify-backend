<?php

namespace App\Http\Requests;

class CreateEventTaskRequest extends BaseFormRequest
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
            'due_date' => 'required|date|after:today',
            'assigned_to' => 'required|exists:users,id',
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
            'due_date.required' => 'The date of the event is required.',
            'due_date.date' => 'Please provide a valid date.',
            'due_date.after' => 'The event date must be a future date.',
            'created_by' => 'The assigned_to ID is required.',
        ];
    }
}

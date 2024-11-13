<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $messages = $validator->errors()->all();

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => implode(' ', $messages),
            'data' => [],
        ], 422));
    }
}

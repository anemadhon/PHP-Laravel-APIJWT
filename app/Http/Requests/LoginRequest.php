<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => Password::required()
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Please fill your email address',
            'email.email' => 'Please fill your valid email address',
            'password.required' => 'Please fill your password',
            'password.min' => 'Please fill your password at least 8 character',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => 'The given data was invalid.',
                        'data' => null, 
                        'errors' => $validator->errors()
                      ], 422));
    }
}

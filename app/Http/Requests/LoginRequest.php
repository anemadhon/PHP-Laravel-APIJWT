<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
}

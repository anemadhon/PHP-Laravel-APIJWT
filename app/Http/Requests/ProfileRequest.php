<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileRequest extends FormRequest
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
        $unique = Rule::unique('users')->ignore(auth()->user());

        return [
            'name' => ['required', 'string', 'min:3', 'max:30', $unique],
            'username' => ['required', 'string', 'min:5', 'max:30', $unique],
            'email' => ['required', 'string', 'email', $unique]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please fill your name',
            'name.min' => 'Please fill your name at least 3 character',
            'name.max' => 'Your name reaches max character (30)',
            'username.required' => 'Please fill your username',
            'username.min' => 'Please fill your username at least 5 character',
            'username.max' => 'Your username reaches max character (30)',
            'email.required' => 'Please fill your email address',
            'email.email' => 'Please fill your valid email address'
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

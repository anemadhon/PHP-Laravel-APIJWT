<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ThreadCommentRequest extends FormRequest
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
            'body' => ['required', 'string', 'min:3', 'max:500']
        ];
    }

    public function messages()
    {
        return [
            'body.required' => 'Please fill your comment',
            'body.min' => 'Please fill your comment at least 3 character',
            'body.max' => 'your comment reaches max character (500)',
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

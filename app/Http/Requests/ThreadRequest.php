<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ThreadRequest extends FormRequest
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
        $unique = $this->isMethod('POST') ? Rule::unique('threads')->ignore($this->thread) : '';

        return [
            'title' => ['required', 'string', 'min:3', 'max:30', $unique],
            'body' => ['required', 'string', 'min:3', 'max:500'],
            'category' => ['required', 'string', 'min:3', 'max:30'],
            'thumbnail' => ['image', 'mimes:png,jpg,jpeg', 'size:2097']
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Please fill your thread title',
            'title.min' => 'Please fill your thread title at least 3 character',
            'title.max' => 'Your thread title reaches max character (30)',
            'body.required' => 'Please fill your thread body',
            'body.min' => 'Please fill your thread body at least 3 character',
            'body.max' => 'your thread body reaches max character (500)',
            'category.required' => 'Please fill your thread category',
            'category.min' => 'Please fill your thread category at least 3 character',
            'category.max' => 'your thread category reaches max character (30)',
            'thumbnail.image' => 'Please just provide an image',
            'thumbnail.mimes' => 'Please provide a PNG or JPG or JPEG images',
            'thumbnail.size' => 'Your image reaches max size (2 MB)'
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

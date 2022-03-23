<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'title' => ['required', 'string', $unique],
            'body' => ['required', 'string'],
            'category' => ['required', 'string']
        ];
    }
}

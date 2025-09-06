<?php

namespace App\Http\Requests\v1\about;

use Illuminate\Foundation\Http\FormRequest;

class SlideshowStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_english' => 'required|string|min:3|max:100',
            'title_pashto' => 'required|string|min:3|max:100',
            'title_farsi' => 'required|string|min:3|max:100',
            'description_english' => 'required|string|min:3|max:400',
            'description_pashto' => 'required|string|min:3|max:400',
            'description_farsi' => 'required|string|min:3|max:400',
            'image' => 'required|mimes:png,jpg,jpeg',
            'visible' => 'required',
        ];
    }
}

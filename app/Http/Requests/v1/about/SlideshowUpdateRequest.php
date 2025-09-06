<?php

namespace App\Http\Requests\v1\about;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class SlideshowUpdateRequest extends FormRequest
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
            'image' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (is_file($value) || $value instanceof \Illuminate\Http\UploadedFile) {
                        // It's a file, validate image and mimes
                        $validator = Validator::make(
                            ['image' => $value],
                            ['image' => 'image|mimes:jpg,jpeg,png']
                        );

                        if ($validator->fails()) {
                            $fail('The image must be a valid JPG, JPEG, or PNG file.');
                        }
                    } elseif (!is_string($value)) {
                        $fail('The image must be either a file or a string.');
                    }
                },
            ],
            'visible' => 'required',
        ];
    }
}

<?php

namespace App\Http\Requests\v1\faqType;

use Illuminate\Foundation\Http\FormRequest;

class FaqTypeStoreRequest extends FormRequest
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
            "name_farsi" => "required|string",
            "name_pashto" => "required|string",
            "name_english" => "required|string",
        ];
    }
}

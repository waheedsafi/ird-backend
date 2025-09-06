<?php

namespace App\Http\Requests\v1\faq;

use Illuminate\Foundation\Http\FormRequest;

class FaqStoreRequest extends FormRequest
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
            "question_farsi" => "required|string|min:3|max:120",
            "question_pashto" => "required|string|min:3|max:120",
            "question_english" => "required|string|min:3|max:120",
            "answer_farsi" => "required|string|min:3|max:255",
            "answer_pashto" => "required|string|min:3|max:255",
            "answer_english" => "required|string|min:3|max:255",
            "type_id" => "required|exists:faq_types,id",
            "type" => "required",
            "show" => "required|boolean",
        ];
    }
}

<?php

namespace App\Http\Requests\v1\representor;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class StoreRepresentorRequest extends FormRequest
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
            "organization_id" => "required",
            "repre_name_english" => "required|min:3|max:60",
            "repre_name_farsi" => "required|min:3|max:60",
            "repre_name_pashto" => "required|min:3|max:60",
            "letter_of_intro" => "required",
        ];
    }
}

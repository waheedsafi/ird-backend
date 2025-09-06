<?php

namespace App\Http\Requests\v1\organization;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class OrganizationUpdatedMoreInformationRequest extends FormRequest
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
            'id' => 'required',
            'vision_english' => 'required|string',
            'vision_farsi' => 'required|string',
            'vision_pashto' => 'required|string',
            'mission_english' => 'required|string',
            'mission_farsi' => 'required|string',
            'mission_pashto' => 'required|string',
            'general_objes_english' => 'required|string',
            'general_objes_farsi' => 'required|string',
            'general_objes_pashto' => 'required|string',
            'objes_in_afg_english' => 'required|string',
            'objes_in_afg_farsi' => 'required|string',
            'objes_in_afg_pashto' => 'required|string',
        ];
    }
}

<?php

namespace App\Http\Requests\v1\director;

use App\Models\Director;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDirectorRequest extends FormRequest
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
            'name_english' => 'required|string|max:32',
            'name_pashto' => 'required|string|max:32',
            'name_farsi' => 'required|string|max:32',
            'gender.id' => 'required|exists:genders,id',
            'nid' => 'required|string|max:50',
            'email' => [
                'required',
                'email:rfc,filter',
                Rule::unique('emails', 'value')->ignore(
                    Director::find($this->id)?->email_id, // the email record id
                    'id' // primary key in emails table
                )
            ],

            'identity_type.id' => 'required|exists:nid_types,id',
            'nationality.id' => 'required|exists:countries,id',
            'province.id' => 'required|exists:provinces,id',
            'district.id' => 'required|exists:districts,id',
            'area_english' => 'required|string|max:255',
            'area_pashto' => 'required|string|max:255',
            'area_farsi' => 'required|string|max:255',
            'surname_english' => 'required|string|max:32',
            'surname_pashto' => 'required|string|max:32',
            'surname_farsi' => 'required|string|max:32',
            'is_active' => 'required',
        ];
    }
}

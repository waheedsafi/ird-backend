<?php

namespace App\Http\Requests\v1\donor;

use App\Models\Donor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DonorUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $donorId = $this->input('id');
        return [
            'id'  => 'required',
            'name_english'  => 'required|string|max:255',
            'name_farsi'     => 'required|string|max:255',
            'name_pashto'    => 'required|string|max:255',
            'abbr' => [
                'required',
                'alpha', // only letters
                'max:50',
                Rule::unique('donors', 'abbr')->ignore($donorId),
            ],
            'username'       => "required|string|max:100|unique:donors,username,{$donorId}",
            'contact'        => 'required|string|max:15',
            'email' => [
                'required',
                'email:rfc,filter',
                Rule::unique('emails', 'value')->ignore(
                    Donor::find($this->id)?->email_id, // the email record id
                    'id' // primary key in emails table
                )
            ],
            'province'       => 'required|max:255',
            'district'       => 'required|max:255',
            'area_english'   => 'required|string|max:255',
            'area_pashto'    => 'required|string|max:255',
            'area_farsi'     => 'required|string|max:255',
        ];
    }
}

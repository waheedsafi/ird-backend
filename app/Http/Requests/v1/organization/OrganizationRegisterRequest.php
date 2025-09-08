<?php

namespace App\Http\Requests\v1\organization;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRegisterRequest extends FormRequest
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
            'email' => 'required|email:rfc,filter|unique:emails,value',

            'contact' => 'required|unique:contacts,value',
            'province_id' => 'required|integer|exists:provinces,id',
            'district_id' => 'required|integer|exists:districts,id',
            "password" => "required",
            'area_english' => 'required|string|max:200',
            'area_farsi' => 'required|string|max:200',
            'area_pashto' => 'required|string|max:200',
            'abbr' => 'required|string|max:50|unique:organizations,abbr',
            'username' => 'required|string|max:50|unique:organizations,username',
            'organization_type_id' => 'required|integer|exists:organization_types,id',
            'name_english' => 'required|string|unique:organization_trans,name',
            'name_pashto' => 'required|string|unique:organization_trans,name',
            'name_farsi' => 'required|string|unique:organization_trans,name',
            'repre_name_english' => 'required|string|max:128',
            'repre_name_farsi' => 'required|string|max:128',
            'repre_name_pashto' => 'required|string|max:128',
            'pending_id' => 'required|integer'
        ];
    }
}

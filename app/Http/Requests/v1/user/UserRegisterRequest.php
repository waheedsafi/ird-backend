<?php

namespace App\Http\Requests\v1\user;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
            "full_name" => ['required', "string", "min:3", "max:45"],
            "username" => ['required', "string", "min:3", "max:45"],
            'email' => 'required|email:rfc,filter|unique:emails,value',
            "password" => [
                'required',
                'string',
                'max:50',
                'min:8'
            ],
            "role" => ["required"],
            "job" => ["required"],
            "job_id" => ["required"],
            "division" => ["required"],
            "division_id" => ["required"]
        ];
    }
}

<?php

namespace App\Http\Requests\v1\profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:45'],
            'email' => 'required|email:rfc,filter|unique:emails,value',
            'full_name' => ['required', 'string', 'max:45'],
            'id' => ['required', 'string'],
        ];
    }
}

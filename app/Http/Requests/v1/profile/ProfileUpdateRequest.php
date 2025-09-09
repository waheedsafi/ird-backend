<?php

namespace App\Http\Requests\v1\profile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'email' => [
                'required',
                'email:rfc,filter',
                Rule::unique('emails', 'value')->ignore(
                    User::find($this->id)?->email_id, // the email record id
                    'id' // primary key in emails table
                )
            ],
            'full_name' => ['required', 'string', 'max:45'],
            'id' => ['required', 'string'],
        ];
    }
}

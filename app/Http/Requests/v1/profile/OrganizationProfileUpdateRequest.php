<?php

namespace App\Http\Requests\v1\profile;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrganizationProfileUpdateRequest extends FormRequest
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
            'contact' => ['required', 'string'],
            'email' => [
                'required',
                'email:rfc,filter',
                Rule::unique('emails', 'value')->ignore(
                    Organization::find(Auth::user()->id)?->email_id
                ),
            ],
        ];
    }
}

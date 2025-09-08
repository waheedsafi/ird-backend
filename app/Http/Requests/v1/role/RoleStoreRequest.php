<?php

namespace App\Http\Requests\v1\role;

use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
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
            "name_farsi" => "required|string|max:50",
            "name_pashto" => "required|string|max:50",
            "name_english" => "required|string|max:50",

            "permissions" => "required|array",
            "permissions.*.id" => "required|integer",
            "permissions.*.view" => "required|boolean",
            "permissions.*.add" => "required|boolean",
            "permissions.*.edit" => "required|boolean",
            "permissions.*.delete" => "required|boolean",
            "permissions.*.sub" => "nullable|array",
            "permissions.*.sub.*.id" => "required|integer",

            "user_role_assignment" => "nullable|array",
            "user_role_assignment.*.id" => "required|integer|exists:roles,id",
        ];
    }
}

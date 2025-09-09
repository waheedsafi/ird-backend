<?php

namespace App\Http\Requests\v1\schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class PresentScheduleRequest extends FormRequest
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
            'schedule_items' => ['required', 'array', 'min:1'],
            'schedule_items.*.id' => ['numeric'],
            'schedule_items.*.project_id' => ['required', 'integer', 'exists:projects,id'],
            'schedule_items.*.comment' => ['nullable', 'string'],
            'schedule_items.*.status' => ['required', 'array'],
            'schedule_items.*.status.id' => ['required', 'integer'],
        ];
    }
}

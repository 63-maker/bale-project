<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'register_number' => ['required', 'string', 'max:50', 'unique:profiles,register_number'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:profiles,email'],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'study_year' => ['required', 'integer', 'min:1', 'max:5'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'register_number.unique' => 'This register number has already been registered.',
            'email.unique' => 'This college email has already been registered.',
            'department_id.exists' => 'The selected department is invalid.',
        ];
    }
}

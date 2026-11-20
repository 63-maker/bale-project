<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:profiles,employee_id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:profiles,email'],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'password' => ['required', 'string', 'min:8'],
            'account_status' => ['nullable', 'string', 'in:active,suspended'],
        ];
    }
}

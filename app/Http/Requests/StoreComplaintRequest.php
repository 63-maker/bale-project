<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() === true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'uuid', 'exists:complaint_categories,id'],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'subject' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'priority' => ['nullable', 'string', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.max' => 'You can upload a maximum of 5 files.',
            'attachments.*.max' => 'Each file cannot exceed 5 MB in size.',
            'attachments.*.mimes' => 'Only JPG, JPEG, PNG, WebP, and PDF files are allowed.',
        ];
    }
}

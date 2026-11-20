<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitResolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isManagement() || $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'resolution_description' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }
}

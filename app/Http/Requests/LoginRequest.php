<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'min:6'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'The email address is required.',
            'email.email'       => 'Please provide a valid email address.',
            'email.min'         => 'The email must be at least 6 characters long.',
            'password.required' => 'The password is required.',
            'password.string'   => 'The password must be a string.',
            'password.min'      => 'The password must be at least 6 characters long.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email'))), // Normalize email
        ]);
    }
}

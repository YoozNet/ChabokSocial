<?php

namespace App\Http\Requests\Installer;

use Illuminate\Foundation\Http\FormRequest;

final class StepAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'رمز عبور باید شامل حروف بزرگ، کوچک و عدد باشد.',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckRoomPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'رمز اتاق را وارد کنید.',
            'password.string'   => 'گذرواژه باید یک مقدار متنی باشد.',
            'password.max'      => 'حداکثر طول مجاز برای گذرواژه ۲۵۵ کاراکتر است.',

            'nickname.string'   => 'نام مستعار باید یک مقدار متنی باشد.',
            'nickname.max'      => 'حداکثر طول مجاز برای نام مستعار ۱۰۰ کاراکتر است.',
        ];
    }
}
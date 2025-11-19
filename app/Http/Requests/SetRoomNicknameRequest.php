<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetRoomNicknameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nickname.required' => 'یه نام مستعار انتخاب کن.',
            'nickname.string'   => 'نام مستعار باید یک مقدار متنی باشد.', // پیام اضافه شده
            'nickname.max'      => 'نام مستعار حداکثر می‌تونه ۱۰۰ کاراکتر باشه.',
        ];
    }
}
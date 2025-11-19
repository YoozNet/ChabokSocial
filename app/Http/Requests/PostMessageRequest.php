<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_nickname' => ['nullable', 'string', 'max:100'],
            'body'            => ['nullable', 'string', 'max:4096'],
            'attachments.*'   => ['nullable', 'file', 'max:20480'], // 20MB each as example
        ];
    }
    public function messages(): array
    {
        return [
            'sender_nickname.string' => 'نام مستعار فرستنده باید یک مقدار متنی باشد.',
            'sender_nickname.max'    => 'حداکثر طول مجاز برای نام مستعار ۱۰۰ کاراکتر است.',

            'body.string' => 'محتوای پیام باید یک مقدار متنی باشد.',
            'body.max'    => 'حداکثر طول مجاز برای محتوای پیام ۴۰۹۶ کاراکتر است.',

            'attachments.*.file' => 'هر پیوست باید یک فایل معتبر باشد.',
            'attachments.*.max'  => 'حجم هر فایل پیوست نباید از ۲۰ مگابایت تجاوز کند.',
        ];
    }
}
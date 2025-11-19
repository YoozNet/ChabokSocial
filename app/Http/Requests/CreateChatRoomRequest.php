<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'slug'        => ['nullable', 'string', 'regex:/^[a-z0-9-]{3,30}$/'],
            'password'    => ['nullable', 'string', 'max:255'],
            'expire'      => ['nullable', 'string', 'in:15m,1h,1d,1w,2w'],
        ];
    }
    public function messages(): array
    {
        return [
            'title.string' => 'عنوان باید یک مقدار متنی (رشته) باشد.',
            'title.max'    => 'حداکثر طول مجاز برای عنوان ۲۵۵ کاراکتر است.',

            'description.string' => 'توضیحات باید یک مقدار متنی (رشته) باشد.',
            'description.max'    => 'حداکثر طول مجاز برای توضیحات ۵۰۰۰ کاراکتر است.',

            'slug.string' => 'نام مستعار (Slug) باید یک مقدار متنی (رشته) باشد.',
            'slug.regex'  => 'نام مستعار (Slug) فقط می‌تواند شامل حروف کوچک انگلیسی (a-z)، اعداد (0-9) و خط تیره (-) باشد و طول آن باید بین ۳ تا ۳۰ کاراکتر باشد.',

            'password.string' => 'گذرواژه باید یک مقدار متنی (رشته) باشد.',
            'password.max'    => 'حداکثر طول مجاز برای گذرواژه ۲۵۵ کاراکتر است.',

            'expire.string' => 'مدت انقضا باید یک مقدار متنی (رشته) باشد.',
            'expire.in'     => 'مدت انقضای انتخاب شده نامعتبر است. مقادیر مجاز: ۱۵m، ۱h، ۱d، ۱w، ۲w.',
        ];
    }
}
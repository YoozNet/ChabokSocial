<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresencePingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:100'],
        ];
    }
    public function messages(): array
    {
        return [
            'client_id.required' => 'شناسه کلاینت الزامی است.',
            'client_id.string'   => 'شناسه کلاینت باید یک مقدار متنی باشد.',
            'client_id.max'      => 'حداکثر طول مجاز برای شناسه کلاینت ۱۰۰ کاراکتر است.',
        ];
    }
}
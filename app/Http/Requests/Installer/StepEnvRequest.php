<?php

namespace App\Http\Requests\Installer;

use Illuminate\Foundation\Http\FormRequest;

final class StepEnvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:100'],
            'app_url'  => ['required', 'url'],
            'cache_driver' => ['required', 'in:file,redis,array,database'],
            'session_driver' => ['required', 'in:file,redis,database'],
            'queue_connection' => ['required', 'in:sync,redis,database'],
        ];
    }
}
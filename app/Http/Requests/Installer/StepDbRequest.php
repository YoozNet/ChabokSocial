<?php

namespace App\Http\Requests\Installer;

use Illuminate\Foundation\Http\FormRequest;

final class StepDbRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'db_connection' => ['required','in:mysql,pgsql,sqlsrv'],
            'db_host' => ['nullable','string'],
            'db_port' => ['nullable','integer'],
            'db_database' => ['nullable','string'],
            'db_username' => ['nullable','string'],
            'db_password' => ['nullable','string'],
        ];
    }
}

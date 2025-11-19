<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminChatRoomIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug'     => ['nullable', 'string', 'max:50'],
            'title'    => ['nullable', 'string', 'max:255'],
            'status'   => ['nullable', 'in:all,active,inactive'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }

    public function validatedFilters(): array
    {
        $data = $this->validated();

        return [
            'slug'   => $data['slug'] ?? null,
            'title'  => $data['title'] ?? null,
            'status' => $data['status'] ?? 'all',
        ];
    }

    public function perPage(): int
    {
        return (int) ($this->validated()['per_page'] ?? 20);
    }
}
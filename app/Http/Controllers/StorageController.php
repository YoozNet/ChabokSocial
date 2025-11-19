<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class StorageController extends Controller
{
    public function local($path)
    {
        if (str_contains($path, '..')) {
            abort(403, 'مسیر نامعتبر.');
        }

        $disk = 'local';
        $fullPath = Storage::disk($disk)->path($path);

        if (!Storage::disk($disk)->exists($path)) {
            throw new FileNotFoundException($fullPath);
        }
        return response()->file($fullPath);
    }
}
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'DMZ' }}</title>
    <link href="{{ asset('assets/css/style.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/tailwindcss.js') }}"></script>

    @yield('style')
    @viteReactRefresh
    @vite('resources/js/app.jsx')
</head>
<body class="flex items-center justify-center min-h-screen">
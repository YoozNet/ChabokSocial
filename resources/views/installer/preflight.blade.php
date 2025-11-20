@extends('layouts.app')
@section('title','پیش‌نیازها')
@section('content')

<div class="bg-white rounded-2xl p-6 md:p-8 shadow-xl mx-auto">
    <h1 class="text-3xl font-extrabold text-indigo-700 mb-6 border-b pb-3">
        ✅ بررسی پیش‌نیازهای نصب سیستم «چابک»
    </h1>

    <div class="mb-8 p-5 bg-indigo-50 rounded-xl border border-indigo-200">
        <h2 class="text-xl font-bold text-indigo-800 mb-3 flex items-center">
            🚀 نسخه PHP مورد نیاز
        </h2>
        <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
            <span class="text-gray-700 font-medium">نسخه فعلی PHP:</span>
            <span class="text-lg font-mono">
                {{ $report['php'] }}
            </span>
            @if (version_compare($report['php'], '8.2.0', '>=')) 
                <span class="text-green-600 font-bold">سازگار (OK)</span>
            @else
                <span class="text-red-600 font-bold">ناسازگار (حداقل 8.2)</span>
            @endif
        </div>
        <p class="text-sm text-gray-500 mt-2">
            توصیه می‌شود از PHP نسخه 8.2 به بالا استفاده شود.
        </p>
    </div>

    <div class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
            🔗 اکستنشن‌های PHP مورد نیاز
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($report['extensions'] as $extension => $status)
                @php
                    $is_ok = $status;
                    $status_text = $is_ok ? 'فعال' : 'غیرفعال';
                    $color_class = $is_ok ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100';
                    $icon = $is_ok ? '✔' : '❌';
                @endphp
                <div class="flex justify-between items-center p-3 rounded-lg border {{ $is_ok ? 'border-green-300' : 'border-red-300' }} bg-white shadow-sm">
                    <span class="font-mono text-gray-700">{{ $extension }}</span>
                    <span class="flex items-center px-2 py-1 text-sm rounded-full {{ $color_class }}">
                        {{ $icon }} {{ $status_text }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-8 p-5 bg-yellow-50 rounded-xl border border-yellow-200">
        <h2 class="text-xl font-bold text-yellow-800 mb-3 flex items-center">
            📂 دسترسی‌های پوشه‌ها و فایل‌ها (Writable)
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($report['permissions'] as $path_key => $status)
                @php
                    $is_writable = $status;
                    $status_text = $is_writable ? 'قابل نوشتن' : 'غیرقابل نوشتن';
                    $color_class = $is_writable ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100';
                    $icon = $is_writable ? '✔' : '❌';

                    $display_name = match ($path_key) {
                        'storage_writable' => 'پوشه storage/',
                        'bootstrap_writable' => 'پوشه bootstrap/cache/',
                        'env_writable' => 'فایل .env',
                        default => $path_key,
                    };
                @endphp
                <div class="flex justify-between items-center p-3 rounded-lg border {{ $is_writable ? 'border-green-300' : 'border-red-300' }} bg-white shadow-sm">
                    <span class="font-medium text-gray-700">{{ $display_name }}</span>
                    <span class="flex items-center px-2 py-1 text-sm rounded-full {{ $color_class }}">
                        {{ $icon }} {{ $status_text }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    
    <form method="post" action="{{ route('install.preft') }}" class="mt-8 text-center">
        @csrf
        @php
            $can_continue = (version_compare($report['php'], '8.2.0', '>=') && 
                             !in_array(false, $report['extensions']) && 
                             !in_array(false, $report['permissions']));
        @endphp

        @if ($can_continue)
            <button type="submit" class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 transition duration-300 text-white font-bold text-lg shadow-lg">
                تمامی پیش‌نیازها برآورده شد. ادامه
            </button>
        @else
            <button type="button" disabled class="px-8 py-3 rounded-xl bg-gray-400 text-white font-bold text-lg cursor-not-allowed">
                لطفاً خطاها را برطرف کنید تا بتوانید ادامه دهید
            </button>
            <p class="text-red-600 mt-3 font-semibold">
                ⚠️ برای ادامه نصب، باید تمامی پیش‌نیازهای بالا با موفقیت (تیک سبز) برآورده شوند.
            </p>
        @endif
    </form>
</div>

@endsection
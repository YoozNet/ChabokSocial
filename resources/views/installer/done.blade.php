@extends('layouts.app')
@section('title','پایان نصب')
@section('content')

<div class="bg-white rounded-3xl p-8 md:p-12 shadow-2xl mx-auto text-center">
    <div class="mb-6">
        <div class="w-24 h-24 mx-auto rounded-full bg-emerald-100 flex items-center justify-center transform transition duration-500 hover:scale-105">
            <span class="text-6xl text-emerald-600 font-extrabold">🎉</span>
        </div>
    </div>

    <h1 class="text-3xl md:text-4xl font-extrabold text-emerald-700 mb-4">
        تبریک! نصب با موفقیت انجام شد.
    </h1>

    <p class="text-gray-600 mb-8 leading-relaxed">
        سیستم جامع قرعه‌کشی و صندوق وام «گردون» اکنون آماده استفاده است. می‌توانید وارد پنل مدیریتی شوید و صندوق‌های خود را راه‌اندازی کنید.
    </p>

    <a href="{{ route("admin.login") }}" class="inline-block px-12 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 transition duration-300 text-white font-bold text-xl shadow-lg hover:shadow-xl transform hover:scale-105">
        🚀 ورود به پنل مدیریت چابک
    </a>

    <div class="mt-8 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-500">
            برای شروع کاربری، از اطلاعات حساب کاربری مدیری که در مرحله قبل ایجاد کردید استفاده نمایید.
        </p>
    </div>

</div>

@endsection
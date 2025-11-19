@extends('layouts.app')

@section('title', 'ورود ادمین')

@section('content')
<h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">
    ورود مدیر سیستم
</h1>

<x-flash class="mt-5 mb-6" />

<form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
            ایمیل یا نام کاربری
        </label>
        <input
            type="text"
            name="login"
            value="{{ old('login') }}"
            class="w-full rounded-lg border text-sm px-3 py-2 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-transparent"
            placeholder="مثلاً: admin یا admin@example.com">
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
            رمز عبور
        </label>
        <input
            type="password"
            name="password"
            class="w-full rounded-lg border text-sm px-3 py-2 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-transparent"
            placeholder="رمز عبور">
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-[11px] text-slate-600 dark:text-slate-300">
            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/70">
            مرا به خاطر بسپار
        </label>
    </div>

    <button
        type="submit"
        class="w-full mt-2 inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 dark:focus:ring-offset-slate-900">
        ورود
    </button>
</form>
@endsection
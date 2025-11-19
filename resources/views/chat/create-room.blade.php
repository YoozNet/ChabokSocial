@extends('layouts.app')

@section('title', 'ساخت اتاق چت جدید')

@section('content')

<x-flash class="mt-5 mb-6" />
<div class="mb-10 p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700">
    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-indigo-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M11.25 9l-3 3m0 0 3 3m-3-3h7.5" />
        </svg>
        ورود به اتاق موجود
    </h2>
    <form method="POST" action="{{ route('chat.join') }}"  class="flex flex-col md:flex-row gap-3" autocomplete="off">
        @csrf
        <input
            type="text"
            name="slug_search" 
            id="slug_search"
            value=""
            required
            dir="ltr"
            class="flex-1 rounded-xl border text-sm px-4 py-2.5 bg-white dark:bg-slate-900  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent font-mono theme-transition"
            placeholder="شناسه اتاق (مثلاً: team-chat-1403 یا یک شناسه تصادفی)">
        
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-bold bg-teal-600 text-white hover:bg-teal-700 active:bg-teal-800 shadow-teal-500/50 shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 dark:focus:ring-offset-gray-900 duration-300 theme-transition md:w-auto w-full">
            <span>ورود</span>
        </button>
    </form>
    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
        برای ورود مستقیم، شناسه اتاقی که قبلاً ساخته شده است را وارد کنید.
    </p>
</div>

<h1 class="text-3xl font-extrabold text-indigo-700 dark:text-indigo-300 drop-shadow-md mb-2">
    ساخت اتاق چت جدید 
</h1>
<p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
    لینک بساز، انقضا رو مشخص کن، رمز بذار (اگه خواستی) و بفرست برای بقیه.
</p>

<form action="{{ route('chat.store') }}" method="POST" class="py-6" autocomplete="off">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            
            <div class="space-y-6">
                
                <div class="form-group">
                    <label for="title" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                        عنوان اتاق (اختیاری)
                    </label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ old('title') }}"
                        class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60 border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-transparent theme-transition"
                        placeholder="مثلاً: گپ سریع تیم، مشاوره، دردِدل شبانه...">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        یک عنوان واضح کمک می‌کنه تا بقیه بدونن چت در مورد چیه.
                    </p>
                </div>
                
                <div class="form-group">
                    <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                        توضیح کوتاه (اختیاری)
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="3"
                        class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60 border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-transparent resize-none theme-transition"
                        placeholder="هر توضیح اضافه‌ای که دوست داری...">{{ old('description') }}</textarea>
                </div>
            </div>
            
            <div class="space-y-6">
                
                <div class="form-group">
                    <label for="slug" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                        لینک اختصاصی (اختیاری)
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        value="{{ old('slug') }}"
                        dir="ltr"
                        class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent font-mono theme-transition"
                        placeholder="مثلاً: team-chat-1403">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold">[a-z0-9-]</span> ، حداقل ۳ و حداکثر ۳۰ کاراکتر. اگه خالی بذاری، خودکار یه لینک تصادفی ساخته می‌شه.
                    </p>
                </div>
                
                <div class="form-group" x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                        رمز برای ورود (اختیاری)
                    </label>
                    <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="password"
                            value="{{ old('password') }}"
                            class="w-full rounded-xl border text-sm px-4 py-2.5 pl-16 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent theme-transition"
                            placeholder="اگه بذاری، فقط کسایی که رمزو دارن می‌تونن وارد شن">
                        <button
                            type="button"
                            x-on:click="show = !show"
                            class="absolute inset-y-0 left-0 px-4 text-xs font-semibold text-indigo-600 dark:text-indigo-400 
                                    hover:text-indigo-800 dark:hover:text-indigo-200 flex items-center theme-transition">
                            <span x-show="!show">نمایش</span>
                            <span x-show="show">مخفی</span>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="expire" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                        زمان انقضای اتاق (پیش‌فرض: ۱ روز)
                    </label>
                    <select
                        name="expire"
                        id="expire"
                        class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition">
                        <option value="15m" @selected(old('expire')==='15m' )>۱۵ دقیقه</option>
                        <option value="1h" @selected(old('expire')==='1h' )>۱ ساعت</option>
                        
                        <option value="1d" @selected(old('expire')==='1d' || !old('expire'))>۱ روز</option>
                        <option value="2d" @selected(old('expire')==='2d' )>۲ روز</option>
                        <option value="3d" @selected(old('expire')==='3d' )>۳ روز</option>
                        <option value="1w" @selected(old('expire')==='1w' )>۱ هفته</option>
                        <option value="2w" @selected(old('expire')==='2w' )>۲ هفته</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        بعد از انقضا، اتاق قابل دسترسی نیست 
                    </p>
                </div>
            </div>
            
        </div> 

        <div class="mt-10 pt-6 border-t border-indigo-300/40 dark:border-indigo-700/50 flex items-center justify-between">
            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                ⚠️ بعد از ساخت، شما مستقیماً به اتاق هدایت می‌شید.
            </p>
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 shadow-indigo-500/50 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transform hover:scale-[1.02] duration-300 theme-transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>شروع چت جدید</span>
            </button>
        </div>
    </form>
@endsection
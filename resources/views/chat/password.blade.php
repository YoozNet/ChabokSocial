@extends('layouts.app')
@section('title', 'ورود به اتاق چت')

@php
    $roomUrl = route('chat.show', $room->slug);
    $shareTitle = 'دعوت به اتاق چت: ' . ($room->title ?? $room->slug);
@endphp

@section('content')
<div class="mb-4">
    <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-1">
        ورود به اتاق «{{ $room->title ?? $room->slug }}»
    </h1>
    <p class="text-xs text-slate-500 dark:text-slate-400 leading-6">
        این اتاق رمزداره. برای ورود، رمز رو بزن و  یه نام مستعار هم برای خودت تعریف کن
        تا تو چت با همون نشونت بدیم.
    </p>
</div>

<x-flash class="mt-5 mb-6" />
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="order-1 p-6 rounded-3xl bg-white/20 dark:bg-gray-700/20 backdrop-blur-lg glass-border shadow-xl theme-transition border border-green-500/30">
        
        <h2 class="text-xl font-bold text-green-700 dark:text-green-300 mb-4">
            🔗 اشتراک‌گذاری اتاق
        </h2>

        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            
            <div class="flex-grow">
                <input 
                    type="text" 
                    id="roomLink"
                    value="{{ $roomUrl }}" 
                    readonly
                    dir="ltr"
                    class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60 border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none select-all  text-center md:text-left font-mono theme-transition"
                />
            </div>
            
            <div class="flex gap-3">
                
                <button
                    type="button"
                    id="copyButton"
                    class="inline-flex items-center gap-1 rounded-xl px-4 py-2.5 text-sm font-semibold whitespace-nowrap bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm focus:ring-2 focus:ring-indigo-500 transform hover:scale-[1.02] duration-300 theme-transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v2.25A2.25 2.25 0 0 1 13.5 22.5h-5.25a2.25 2.25 0 0 1-2.25-2.25v-5.25A2.25 2.25 0 0 1 8.25 12H10.5M15.75 17.25H12a2.25 2.25 0 0 1-2.25-2.25V12" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9V6m0 0a3 3 0 0 0 3-3h3a3 3 0 0 1 3 3v3a3 3 0 0 1-3 3h-3a3 3 0 0 1-3-3V6Z" />
                    </svg>
                    <span>کپی لینک</span>
                </button>
                
                <button
                    type="button"
                    id="shareButton"
                    class="inline-flex items-center gap-1 rounded-xl px-4 py-2.5 text-sm font-semibold whitespace-nowrap bg-green-600 text-white hover:bg-green-700 shadow-sm focus:ring-2 focus:ring-green-500 transform hover:scale-[1.02] duration-300 theme-transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186a2.25 2.25 0 1 1 0 2.186m0-2.186v2.186m0-2.186h-3.935m3.935 0c1.71 0 3.37.669 4.609 1.868l1.79 1.79c.928.927 1.432 2.158 1.432 3.429s-.504 2.492-1.432 3.42l-1.79 1.79a6.25 6.25 0 0 1-4.609 1.868h-3.935a2.25 2.25 0 1 0 0-2.186m0 0a2.25 2.25 0 1 1 0-2.186m0 2.186v-2.186" />
                    </svg>
                    <span>اشتراک‌گذاری</span>
                </button>
            </div>
        </div>
    </div>
    <div class="order-2 p-6 md:p-8 rounded-3xl  bg-white/20 dark:bg-gray-700/20  backdrop-blur-lg glass-border  shadow-xl theme-transition border border-indigo-500/30">
        <h2 class="text-xl font-bold text-indigo-700 dark:text-indigo-300 mb-4">
            🔑 رمز اتاق
        </h2>
        <form action="{{ route('chat.password.check', $room->slug) }}" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            
            <div x-data="{ show: false }">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                    رمز اتاق
                </label>
                <div class="relative">
                    <input
                        :type="show ? 'text' : 'password'"
                        name="password"
                        id="password"
                        required
                        class="w-full rounded-xl border text-sm px-4 py-2.5 pl-16 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent theme-transition"
                        placeholder="رمزی که صاحب اتاق برات فرستاده">
                    <button
                        type="button"
                        x-on:click="show = !show"
                        class="absolute inset-y-0 left-0 px-4 text-xs font-semibold text-indigo-600 dark:text-indigo-400  hover:text-indigo-800 dark:hover:text-indigo-200 flex items-center theme-transition">
                        <span x-show="!show">نمایش</span>
                        <span x-show="show">مخفی</span>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                    نام مستعار
                </label>
                <input
                    type="text"
                    name="nickname"
                    required
                    value="{{ old('nickname') }}"
                    class="w-full rounded-lg border text-sm px-3 py-2 bg-white/90 dark:bg-slate-900/80 border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-transparent"
                    placeholder="مثلاً: کاربر خسته، مهمون شب، برنامه‌نویس له‌شده...">
                    
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                   برای تفکیک چت ها لازمه نام مستعار انتخاب کنی
                </p>
            </div>
        
            <div class="pt-2 flex items-center justify-between">
                <a href="{{ url('/') }}" class="text-[11px] text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                    برگشت به صفحه‌ی ساخت اتاق
                </a>
        
                <button
                    type="submit"
                    class="inline-flex items-center gap-1 rounded-lg px-4 py-2 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 dark:focus:ring-offset-slate-900">
                    ورود به اتاق
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
    const roomLink = document.getElementById('roomLink');
    const copyButton = document.getElementById('copyButton');
    const shareButton = document.getElementById('shareButton');
    const urlToShare = roomLink.value;
    const shareTitle = "{{ $shareTitle }}";
    
    copyButton.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(urlToShare);
            copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M19.929 7.571a.75.75 0 0 1-.034 1.054L13.5 15.118l-3.395-3.395a.75.75 0 0 1 1.061-1.06l2.843 2.843 5.864-6.326a.75.75 0 0 1 1.054.034Z" clip-rule="evenodd" /></svg><span>کپی شد!</span>';
            setTimeout(() => {
                copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v2.25A2.25 2.25 0 0 1 13.5 22.5h-5.25a2.25 2.25 0 0 1-2.25-2.25v-5.25A2.25 2.25 0 0 1 8.25 12H10.5M15.75 17.25H12a2.25 2.25 0 0 1-2.25-2.25V12" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 9V6m0 0a3 3 0 0 0 3-3h3a3 3 0 0 1 3 3v3a3 3 0 0 1-3 3h-3a3 3 0 0 1-3-3V6Z" /></svg><span>کپی لینک</span>';
            }, 2000);
        } catch (err) {
            console.error('Failed to copy text: ', err);
            alert('متأسفانه مرورگر شما از کپی مستقیم پشتیبانی نمی‌کند.');
        }
    });
    
    if (navigator.share) {
        shareButton.addEventListener('click', async () => {
            try {
                await navigator.share({
                    title: shareTitle,
                    url: urlToShare
                });
            } catch (err) {
                console.error('Error sharing:', err);
            }
        });
    } else {
        shareButton.classList.add('hidden');
    }
</script>
@endpush
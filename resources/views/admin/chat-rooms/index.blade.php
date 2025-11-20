@extends('layouts.app')

@section('title', 'لیست اتاق‌های چت')

@section('content')
<div>
    <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">
        اتاق‌های چت
    </h1>
    
    <x-flash class="mt-5 mb-6" />
    <div class="mt-4 mb-6 p-4 bg-indigo-50 dark:bg-slate-800 border-l-4 border-indigo-500 rounded-lg shadow-md text-justify">
        <div class="flex flex-col sm:flex-row sm:items-center">
            <svg class="w-6 h-6 text-indigo-500 mb-2 sm:mb-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
    
            <div class="mr-3 text-sm text-slate-700 dark:text-slate-200">
                <p class="font-bold mb-1 text-indigo-600 dark:text-indigo-400">
                    ⭐ نمونه کار و اطلاعیه توسعه‌دهنده (سعید شانقی)
                </p>
                <p>
                    این پنل مدیریت چت به صورت کامل (صفر تا صد) توسط سعید شانقی به عنوان یک نمونه کار حرفه‌ای توسعه داده شده است.
                    <span class="block mt-1">
                        جهت بررسی کیفیت کد، به‌روزرسانی‌های پیشرفته (مانند تبدیل به چت آنلاین زنده) یا همکاری، می‌توانید با ایشان در ارتباط باشید.
                    </span>
                </p>
    
                <p class="mt-3 font-semibold text-xs">
                    ارادتمند شما:
                    <span class="text-indigo-600 dark:text-indigo-400">سعید شانقی</span> |
                    <a href="https://t.me/bugslay" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 underline transition duration-150">
                        تلگرام: @bugslay
                    </a>
                </p>
            </div>
        </div>
    </div>
    <form method="GET" action="{{ route('admin.chat-rooms.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-[11px] text-slate-500 dark:text-slate-400 mb-1">شناسه (slug)</label>
            <input type="text" name="slug" value="{{ $filters['slug'] ?? '' }}"
                class="w-full rounded-lg border text-xs px-3 py-2 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700">
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 dark:text-slate-400 mb-1">عنوان</label>
            <input type="text" name="title" value="{{ $filters['title'] ?? '' }}"
                class="w-full rounded-lg border text-xs px-3 py-2 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700">
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 dark:text-slate-400 mb-1">وضعیت</label>
            <select name="status"
                class="w-full rounded-lg border text-xs px-3 py-2 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700">
                <option value="all" @selected(($filters['status'] ?? 'all' )==='all' )>همه</option>
                <option value="active" @selected(($filters['status'] ?? 'all' )==='active' )>فعال</option>
                <option value="inactive" @selected(($filters['status'] ?? 'all' )==='inactive' )>غیرفعال</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] text-slate-500 dark:text-slate-400 mb-1">تعداد در صفحه</label>
            <input type="number" name="per_page" value="{{ $perPage }}" min="5" max="100"
                class="w-full rounded-lg border text-xs px-3 py-2 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700">
        </div>
        <div class="flex items-center">
            <button type="submit"
                class="w-full rounded-lg px-3 py-2 text-xs font-semibold bg-slate-800 text-white hover:bg-slate-900 dark:bg-slate-200 dark:text-slate-900">
                اعمال فیلتر
            </button>
        </div>
    </form>
    @if ($rooms->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
            @foreach ($rooms as $room)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg p-5 flex flex-col justify-between transition duration-300 hover:shadow-xl hover:ring-2 hover:ring-indigo-500">
                    <div class="flex justify-between items-start mb-3 border-b pb-3 border-slate-100 dark:border-slate-800">
                        <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100 line-clamp-2">
                            {{ $room->title ?? 'بدون عنوان' }}
                        </h2>
                        @if ($room->status === 'active')
                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-medium mr-2">
                                فعال
                            </span>
                        @else
                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[11px] font-medium mr-2">
                                غیرفعال
                            </span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-xs text-slate-600 dark:text-slate-300">
                        <div>
                            <span class="block font-semibold text-slate-500 dark:text-slate-400">شناسه (#)</span>
                            <span class="block text-slate-800 dark:text-slate-200 font-mono text-[10px]">{{ $room->id }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-500 dark:text-slate-400">Slug</span>
                            <span class="block text-slate-800 dark:text-slate-200 font-mono text-[10px] truncate" title="{{ $room->slug }}">{{ $room->slug }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-500 dark:text-slate-400">تعداد پیام</span>
                            <span class="block text-slate-800 dark:text-slate-200">{{ $room->messages_count ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-500 dark:text-slate-400">آخرین پیام</span>
                            <span class="block text-slate-800 dark:text-slate-200 text-[10px]">{{ $room->last_message_at ? verta($room->last_message_at)->format('Y/m/d H:i') : '-' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block font-semibold text-slate-500 dark:text-slate-400">تاریخ ایجاد</span>
                            <span class="block text-slate-800 dark:text-slate-200 text-[10px]">{{ $room->created_at ? verta($room->created_at)->format('Y/m/d H:i') : '-' }}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <a href="{{ route('admin.chat-rooms.show', $room) }}"
                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition duration-150 inline-flex items-center">
                            جزئیات چت
                            <svg class="w-4 h-4 mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-5 text-center text-xs text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl mt-6">
            اتاقی پیدا نشد.
        </div>
    @endif
    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
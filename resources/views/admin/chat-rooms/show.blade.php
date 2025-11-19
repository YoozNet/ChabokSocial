@extends('layouts.app')

@section('title', 'پروفایل اتاق چت')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">
        اتاق : {{ $room->title ?? $room->slug }}
    </h1>

    <a href="{{ route('admin.chat-rooms.index') }}" class="text-[11px] text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
        ← برگشت به لیست اتاق‌ها
    </a>
</div>

<x-flash class="mt-5 mb-6" />

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-4 py-4">
    <h2 class="text-sm font-semibold mb-3 text-slate-800 dark:text-slate-100">اطلاعات کلی</h2>
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
        <div>
            <dt class="text-slate-500 dark:text-slate-400">شناسه (slug)</dt>
            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $room->slug }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">عنوان</dt>
            <dd>{{ $room->title ?? 'بدون عنوان' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">وضعیت</dt>
            <dd>
                @if ($room->status === 'active')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px]">
                    فعال
                </span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[11px]">
                    غیرفعال
                </span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">تاریخ انقضا</dt>
            <dd class="text-slate-800 dark:text-slate-100">
                {{ $room->expires_at ? $room->expires_at->format('Y-m-d H:i') : '—' }}
            </dd>
        </div>
        <div>
            <dt class="text-slate-500 dark:text-slate-400">ایجاد شده در</dt>
            <dd>{{ $room->created_at?->format('Y-m-d H:i') }}</dd>
        </div>
        <div class="md:col-span-2">
            <dt class="text-slate-500 dark:text-slate-400">توضیحات</dt>
            <dd class="text-slate-800 dark:text-slate-100">
                {{ $room->description ?: '—' }}
            </dd>
        </div>
    </dl>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2 mb-2">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-4 py-3 text-xs">
        <div class="text-slate-500 dark:text-slate-400 mb-1">تعداد پیام‌ها</div>
        <div class="text-lg font-bold text-slate-900 dark:text-slate-100">
            {{ $stats['messages_count'] }}
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-4 py-3 text-xs">
        <div class="text-slate-500 dark:text-slate-400 mb-1">تعداد فایل‌ها</div>
        <div class="text-lg font-bold text-slate-900 dark:text-slate-100">
            {{ $stats['files_count'] }}
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-4 py-3 text-xs">
        <div class="text-slate-500 dark:text-slate-400 mb-1">آخرین فعالیت</div>
        <div class="text-sm text-slate-800 dark:text-slate-100">
            {{ $stats['last_activity'] ? $stats['last_activity']->format('Y-m-d H:i') : '—' }}
        </div>
    </div>
</div>

<div class="mt-2 mb-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-4 py-4 text-xs flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div class="flex items-center gap-2 md:justify-start justify-center">
        @if ($room->status === 'active')
        <form action="{{ route('admin.chat-rooms.deactivate', $room) }}" method="POST">
            @csrf
            <button type="submit" class="px-3 py-1 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200">
                غیرفعال کردن اتاق
            </button>
        </form>
        @else
        <form action="{{ route('admin.chat-rooms.activate', $room) }}" method="POST">
            @csrf
            <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                فعال کردن دوباره
            </button>
        </form>
        @endif

        <form action="{{ route('admin.chat-rooms.extend-expire', $room) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <select name="expire_option"
                class="rounded-lg border px-2 py-1 text-[11px] bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700">
                <option value="15m">+15 دقیقه</option>
                <option value="1h">+1 ساعت</option>
                <option value="1d">+1 روز</option>
                <option value="1w">+1 هفته</option>
                <option value="2w">+2 هفته</option>
            </select>
            <button type="submit" class="px-3 py-1 rounded-lg bg-slate-800 text-white hover:bg-slate-900">
                تمدید
            </button>
        </form>
    </div>

    <div class="flex items-center gap-3 md:justify-end justify-center">
        <a href="{{ route('admin.chat-rooms.messages', $room) }}" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline">
            مشاهده‌ی پیام‌ها
        </a>

        <form action="{{ route('admin.chat-rooms.destroy', $room) }}" method="POST"
            onsubmit="return confirm('اتاق و تمام پیام‌ها و فایل‌ها حذف می‌شود. مطمئنی؟');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-1 rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200">
                حذف کامل اتاق
            </button>
        </form>
    </div>
</div>
@endsection
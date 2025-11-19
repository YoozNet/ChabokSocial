@extends('layouts.app')

@section('title', 'لیست اتاق‌های چت')

@section('content')
<h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">
    اتاق‌های چت
</h1>

<x-flash class="mt-5 mb-6" />

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

<div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80">
    <table class="min-w-full text-xs">
        <thead class="bg-slate-100/80 dark:bg-slate-800/80">
            <tr class="text-[11px] text-slate-600 dark:text-slate-300">
                <th class="px-3 py-2 text-right">#</th>
                <th class="px-3 py-2 text-right">Slug</th>
                <th class="px-3 py-2 text-right">عنوان</th>
                <th class="px-3 py-2 text-right">وضعیت</th>
                <th class="px-3 py-2 text-right">تعداد پیام</th>
                <th class="px-3 py-2 text-right">آخرین پیام</th>
                <th class="px-3 py-2 text-right">ایجاد شده در</th>
                <th class="px-3 py-2 text-right">عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rooms as $room)
            <tr class="border-t border-slate-100 dark:border-slate-800 text-slate-800 dark:text-slate-100">
                <td class="px-3 py-2 align-top">{{ $room->id }}</td>
                <td class="px-3 py-2 align-top font-mono text-[11px]">{{ $room->slug }}</td>
                <td class="px-3 py-2 align-top">
                    {{ $room->title ?? 'بدون عنوان' }}
                </td>
                <td class="px-3 py-2 align-top">
                    @if ($room->status === 'active')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px]">
                        فعال
                    </span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[11px]">
                        غیرفعال
                    </span>
                    @endif
                </td>
                <td class="px-3 py-2 align-top">
                    {{ $room->messages_count ?? '-' }}
                </td>
                <td class="px-3 py-2 align-top text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $room->last_message_at ? verta($room->last_message_at)->format('Y/m/d H:i') : '-' }}
                </td>
                <td class="px-3 py-2 align-top text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $room->created_at ? verta($room->created_at)->format('Y/m/d H:i') : '-' }}
                </td>
                <td class="px-3 py-2 align-top">
                    <a href="{{ route('admin.chat-rooms.show', $room) }}"
                        class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline">
                        جزئیات
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-3 py-4 text-center text-xs text-slate-500 dark:text-slate-400">
                    اتاقی پیدا نشد.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $rooms->links() }}
</div>
@endsection
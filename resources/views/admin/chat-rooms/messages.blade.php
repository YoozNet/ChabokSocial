@extends('layouts.app')

@section('title', 'پیام‌های اتاق')

@section('content')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">
            پیام‌های اتاق: {{ $room->title ?? $room->slug }}
        </h1>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
            پیام‌ها به صورت رمزگشایی شده نمایش داده می‌شن.
        </p>
    </div>

    <a href="{{ route('admin.chat-rooms.show', $room) }}" class="text-[11px] text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
        ← برگشت به پروفایل اتاق
    </a>
</div>

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-4 py-3">
    @forelse ($messages as $message)
    <div class="border-b border-slate-100 dark:border-slate-800 py-3 last:border-none">
        <div class="flex items-center justify-between text-xs mb-1">
            <div class="text-slate-600 dark:text-slate-300">
                {{ $message->sender_nickname ?? 'مهمان' }}
                <span class="text-[10px] text-slate-400 dark:text-slate-500 ml-2">
                    {{ $message->sender_masked_ip }}
                </span>
            </div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500">
                {{ $message->created_at?->format('Y-m-d H:i') }}
            </div>
        </div>
        <div class="text-sm text-slate-900 dark:text-slate-100 whitespace-pre-wrap">
            {{ $message->body }}
        </div>

        @if ($message->attachments->count())
            <div class="mt-2 space-y-1">
                @foreach ($message->attachments as $att)
                    <a href="{{ $att->signed_url }}"
                        target="_blank"
                        class="block text-[11px] text-sky-600 dark:text-sky-400 underline">
                        {{ $att->original_name }} ({{ $att->mime_type }})
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    @empty
    <div class="text-xs text-slate-500 dark:text-slate-400">
        پیامی برای این اتاق ثبت نشده.
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $messages->links() }}
</div>
@endsection
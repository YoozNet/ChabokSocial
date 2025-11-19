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

<div class="mt-4 flex items-center space-x-2 space-x-reverse">
    <label for="refresh-interval" class="text-xs text-slate-600 dark:text-slate-300">
        به‌روزرسانی خودکار هر:
    </label>
    <select id="refresh-interval" class="rounded-lg border px-2 py-1 text-[11px] bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700">
        <option value="0" selected>غیرفعال</option>
        <option value="5">۵ ثانیه</option>
        <option value="10">۱۰ ثانیه</option>
        <option value="30">۳۰ ثانیه</option>
    </select>
    <span id="countdown-display" class="text-xs text-red-500 dark:text-red-400 mr-4 hidden">
        (آپدیت در <span id="countdown-time"></span> ثانیه)
    </span>
</div>

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 px-4 py-3 mt-5">
    @forelse ($messages as $message)
    <div class="border-b border-slate-100 dark:border-slate-800 py-3 last:border-none">
        <div class="flex items-center justify-between text-xs mb-1">
            <div class="text-slate-600 dark:text-slate-300">
                {{ $message->sender_nickname ?? 'مهمان' }}
                <span class="text-[10px] text-slate-400 dark:text-slate-500 ml-2" dir="ltr">
                    {{ $message->sender_masked_ip }}
                </span>
            </div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500" dir="ltr">
                {{ $message->created_at ? verta($message->created_at)->format('Y/m/d H:i') : '-' }}
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
@push('scripts')
<script>
    const refreshIntervalSelect = document.getElementById('refresh-interval');
    const countdownDisplay = document.getElementById('countdown-display');
    const countdownTime = document.getElementById('countdown-time');
    
    const STORAGE_KEY = 'chat_room_refresh_interval';
    let timer = null;
    let countdownTimer = null;
    let remainingTime = 0;

    // --- توابع اصلی ---

    function reloadPage() {
        if (timer) clearInterval(timer);
        if (countdownTimer) clearInterval(countdownTimer);
        window.location.reload(); 
    }

    function updateCountdown() {
        if (remainingTime > 0) {
            remainingTime--;
            countdownTime.textContent = remainingTime;
            countdownDisplay.classList.remove('hidden');
        } else {
            if (countdownTimer) clearInterval(countdownTimer);
        }
    }

    function setupTimer(initialLoad = false) {
        if (timer) clearInterval(timer);
        if (countdownTimer) clearInterval(countdownTimer);
        timer = null;
        countdownTimer = null;
        countdownDisplay.classList.add('hidden');

        const intervalSeconds = parseInt(refreshIntervalSelect.value);
        
        if (intervalSeconds > 0) {
            const intervalMilliseconds = intervalSeconds * 1000;
            if (!initialLoad) {
                localStorage.setItem(STORAGE_KEY, intervalSeconds);
            }
            timer = setInterval(reloadPage, intervalMilliseconds);
            
            remainingTime = intervalSeconds;
            countdownTime.textContent = remainingTime;
            countdownDisplay.classList.remove('hidden');
            countdownTimer = setInterval(updateCountdown, 1000); 

        } else {
            localStorage.setItem(STORAGE_KEY, 0);
        }
    }
    
    function initializeRefreshSetting() {
        const storedInterval = localStorage.getItem(STORAGE_KEY);

        if (storedInterval !== null && storedInterval !== '0') {
            const selectedValue = storedInterval;
            
            let found = false;
            for (let i = 0; i < refreshIntervalSelect.options.length; i++) {
                if (refreshIntervalSelect.options[i].value === selectedValue) {
                    refreshIntervalSelect.value = selectedValue;
                    found = true;
                    break;
                }
            }
            
            if (found) {
                setupTimer(true);
            }
        } else {
            setupTimer(true); 
        }
        refreshIntervalSelect.addEventListener('change', () => setupTimer(false));
    }
    if (refreshIntervalSelect) {
        initializeRefreshSetting(); 
    }
    window.addEventListener('beforeunload', () => {
        if (timer) clearInterval(timer);
        if (countdownTimer) clearInterval(countdownTimer);
    });

</script>
@endpush
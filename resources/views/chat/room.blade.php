@extends('layouts.app')
@php
    $messagesIndexUrl = route('chat.messages.index', ['slug' => $room->slug]);
    $messagesStoreUrl = route('chat.messages.store', ['slug' => $room->slug]);
    $pingUrl = route('chat.presence.ping', ['slug' => $room->slug]);
    $activeCountUrl = route('chat.presence.count', ['slug' => $room->slug]);
@endphp
@section('content')
<div x-data="chatRoomComponent(
        '{{ $room->slug }}', 
        '{{ $nickname ?? '' }}',
        '{{ $messagesIndexUrl }}',
        '{{ $messagesStoreUrl }}',
        '{{ $pingUrl }}',
        '{{ $activeCountUrl }}'
    )">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">اتاق : {{ $room->title ?? $room->slug }}</h1>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            آنلاین: <span x-text="activeCount"></span> نفر
        </p>
        <a href="{{ url('/') }}" class="text-[11px] text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            خروج از اتاق
        </a>
    </div>
    <x-flash class="mt-5 mb-6" />
    <div class="grid grid-cols-12 gap-4 md:gap-6">
        <div class="col-span-12 md:col-span-4 order-2 md:order-1">
            <div class="h-full border rounded-2xl p-4 bg-white/90 dark:bg-slate-900/90 border-slate-200 dark:border-slate-700 flex flex-col">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">
                    پیام جدید
                </h2>

                <form x-on:submit.prevent="sendMessage" class="space-y-3 flex-1 flex flex-col">
                    <input
                        type="hidden"
                        x-model="nickname" />

                    <div class="flex-1 flex flex-col">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            متن پیام
                        </label>
                        <textarea
                            x-model="body"
                            rows="4"
                            placeholder="پیامت رو اینجا بنویس..."
                            class="w-full border rounded-lg px-3 py-2 text-sm bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-transparent resize-none flex-1"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            پیوست فایل (اختیاری)
                        </label>
                        <input
                            type="file"
                            multiple
                            x-ref="fileInput"
                            class="block w-full text-[11px] text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 dark:file:bg-slate-800 dark:file:text-slate-200 dark:hover:file:bg-slate-700">
                    </div>

                    <div x-show="error" class="text-xs text-red-500" x-text="error"></div>

                    <div class="pt-1">
                        <button
                            type="submit"
                            class="w-full px-4 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed"
                            :disabled="isSending">
                            <span x-show="!isSending">ارسال پیام</span>
                            <span x-show="isSending">در حال ارسال...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-span-12 md:col-span-8 order-1 md:order-2">
            <div id="messages" class="h-[28rem] border rounded-2xl p-3 md:p-4 overflow-y-auto bg-white/80 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700">
                <template x-if="messages.length === 0">
                    <div class="h-full flex items-center justify-center text-sm text-slate-400 dark:text-slate-500">
                        هنوز پیامی نیست. تو اولیش رو بفرست 🙂
                    </div>
                </template>

                <template x-for="(msg, index) in messages" :key="getKey(msg, index)">
                    <div class="mb-3 last:mb-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <div class="text-xs font-semibold text-slate-600 dark:text-slate-300" x-text="msg.sender_nickname || 'مهمان'"></div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500" dir="ltr" x-text="formatDate(msg.created_at ?? '')"></div>
                        </div>
                        <div class="text-sm text-slate-800 dark:text-slate-100 whitespace-pre-wrap" x-text="msg.body"></div>

                        <template x-if="msg.attachments && msg.attachments.length">
                            <div class="mt-2 space-y-1">
                                <template x-for="att in msg.attachments" :key="att.id">
                                    <div class="text-xs text-sky-600 dark:text-sky-400 underline cursor-pointer">
                                        <a :href="att.signed_url" target="_blank" x-text="att.original_name"></a>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="mt-2 h-px bg-slate-200/70 dark:bg-slate-700/70"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function generateUUID() {
        if (typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        const d = new Date().getTime(); // Timestamp
        const d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0; 
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            let r = Math.random() * 16;
            if (d > 0) { 
                r = (d + r) % 16 | 0;
            } else {
                r = (d2 + r) % 16 | 0;
            }
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16); 
        });
    }
    function getOrCreateClientId(slug) {
        const storageKey = `chat_client_id_${slug}`;
        let storedClientId = localStorage.getItem(storageKey);
        
        if (!storedClientId) {
            storedClientId = generateUUID();
            localStorage.setItem(storageKey, storedClientId);
        }
        
        return storedClientId;
    }
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatRoomComponent', (
            slug,
            initialNickname = '',
            messagesIndexUrl,
            messagesStoreUrl,
            pingUrl,
            activeCountUrl
        ) => ({
            slug,
            messages: [],
            lastId: null,
            nickname: initialNickname || '',
            body: '',
            error: '',
            clientId: null,
            activeCount: 0,
            isSending: false,
            theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

            _messagesIndexUrl: messagesIndexUrl,
            _messagesStoreUrl: messagesStoreUrl,
            _pingUrl: pingUrl,
            _activeCountUrl: activeCountUrl,
            
            formatDate(timestamp) {
                if (!timestamp) return '';

                try {
                    const date = new Date(timestamp);
                    
                    const options = {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false, 
                        timeZone: 'Asia/Tehran'
                    };
                    return new Intl.DateTimeFormat('fa-IR', options).format(date);
                } catch (e) {
                    this.displayError('خطا در تبدیل تاریخ: ' + timestamp);
                    return timestamp;
                }
            },
            getKey(msg, index) {
                return 'msg-' + (msg.id || 'temp-' + index + '-' + Date.now());
            },
            displayError(message) {
                this.error = message;
            },
            init() {
                try {
                    this.applyTheme();

                    this.fetchMessages();
                    this.fetchActiveCount();
                    this.pingPresence();

                    setInterval(() => this.fetchMessages(), 2000);
                    setInterval(() => this.fetchActiveCount(), 5000);
                    setInterval(() => this.pingPresence(), 15000);

                    this.clientId = getOrCreateClientId(this.slug);
                } catch (e) {
                    const errorMessage = e.message || e.toString() || 'خطا در راه‌اندازی کامپوننت چت.';
                    this.displayError('خطا در راه‌اندازی: ' + errorMessage);
                }
            },

            applyTheme() {
                document.documentElement.classList.toggle('dark', this.theme === 'dark');
                localStorage.setItem('theme', this.theme);
            },

            async fetchMessages() {
                try {
                    const url = this._messagesIndexUrl + (this.lastId ? `?after=${this.lastId}` : '');
                    const res = await fetch(url);

                    if (!res.ok) {
                        this.displayError(`خطا (${res.status}) در دریافت پیام‌ها.`);
                        return;
                    }

                    const data = await res.json();

                    if (!data || !Array.isArray(data.data)) {
                        this.displayError('پاسخ نامعتبر از سرور هنگام دریافت پیام‌ها.');
                        return;
                    }

                    data.data.forEach(msg => {
                        this.messages.push(msg);
                        this.lastId = msg.id;
                    });

                    this.$nextTick(() => {
                        const el = document.getElementById('messages');
                        if (el) {
                            el.scrollTop = el.scrollHeight;
                        }
                    });
                } catch (e) {
                    this.displayError('خطای شبکه در دریافت پیام‌ها.');
                }
            },

            async sendMessage() {
                this.error = '';
                this.isSending = true;

                try {
                    const files = this.$refs.fileInput?.files || [];
                    const isBodyEmpty = !this.body || !this.body.trim();
                    const hasAttachments = files.length > 0;
                    if (isBodyEmpty && !hasAttachments) {
                        this.displayError('لطفاً متن پیام را وارد کنید یا یک فایل پیوست کنید.');
                        this.isSending = false;
                        return;
                    }
                    const formData = new FormData();
                    formData.append('sender_nickname', this.nickname);
                    formData.append('body', this.body);

                    for (let i = 0; i < files.length; i++) {
                        formData.append('attachments[]', files[i]);
                    }

                    const url = this._messagesStoreUrl;

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData,
                    });

                    if (res.status === 429) {
                        const data = await res.json().catch(() => ({}));
                        this.displayError(data.message || 'خیلی سریع پیام می‌فرستی، ۲ ثانیه صبر کن.');
                        return;
                    }

                    if (!res.ok) {
                        try {
                            const errorData = await res.json();
                            
                            if (errorData.message) {
                                this.displayError(errorData.message);
                            }
                            if (errorData.errors && Object.keys(errorData.errors).length > 0) {
                                const firstErrorKey = Object.keys(errorData.errors)[0];
                                this.displayError(errorData.errors[firstErrorKey][0]);
                            }

                        } catch (e) {
                            this.displayError(`خطا در ارسال پیام. کد وضعیت: ${res.status}`);
                        }
                        
                        if (!this.error) {
                            this.displayError(`خطا در ارسال پیام. کد وضعیت: ${res.status}`);
                        }

                        return;
                    }

                    const data = await res.json();

                    if (!data || !data.data) {
                        this.displayError('پاسخ نامعتبر از سرور پس از ارسال پیام.');
                        return;
                    }

                    this.messages.push(data.data);
                    this.lastId = data.data.id;
                    this.body = '';
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }

                    this.$nextTick(() => {
                        const el = document.getElementById('messages');
                        if (el) {
                            el.scrollTop = el.scrollHeight;
                        }
                    });
                } catch (e) {
                    this.displayError('یه خطای غیرمنتظره هنگام ارسال پیام رخ داد. (احتمالاً خطای شبکه)');
                } finally {
                    this.isSending = false;
                }
            },

            async pingPresence() {
                try {
                    const res = await fetch(this._pingUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            client_id: this.clientId,
                        }),
                    });

                    if (!res.ok) {
                        this.displayError(`خطا در پینگ حضور (${res.status}).`);
                    }
                } catch (e) {
                    this.displayError('خطای شبکه در پینگ حضور.');
                }
            },

            async fetchActiveCount() {
                try {
                    const res = await fetch(this._activeCountUrl);

                    if (!res.ok) {
                        this.displayError(`خطا (${res.status}) در دریافت تعداد کاربران فعال.`);
                        return;
                    }

                    const data = await res.json();
                    if (typeof data.count !== 'number') {
                        this.displayError('پاسخ نامعتبر از سرور هنگام دریافت تعداد کاربران.');
                        return;
                    }

                    this.activeCount = data.count;
                } catch (e) {
                    this.displayError('خطای شبکه در دریافت تعداد کاربران.');
                }
            },
        }));
    });
</script>
@endpush
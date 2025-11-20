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
        <div class="col-span-12 order-1 flex flex-col h-[calc(100vh-200px)]">
            <div id="messages" x-ref="messagesContainer" class="border rounded-2xl p-3 md:p-4 overflow-y-auto bg-white/80 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700">
                <template x-if="messages.length === 0">
                    <div class="h-full flex items-center justify-center text-sm text-slate-400 dark:text-slate-500">
                        هنوز پیامی نیست. تو اولیش رو بفرست 🙂
                    </div>
                </template>

                <template x-for="(msg, index) in messages" :key="getKey(msg, index)">
                    <div
                        class="mb-2 flex"
                        :class="isOwnMessage(msg) ? 'justify-start' : 'justify-end'"
                    >
                        <div
                            class="max-w-[80%] md:max-w-[40%] w-full rounded-2xl px-3 py-2 text-sm shadow-sm"
                            :class="isOwnMessage(msg)
                                ? 'bg-indigo-600 text-white rounded-br-sm'
                                : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-bl-sm'"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span
                                    class="text-[11px] font-semibold"
                                    :class="isOwnMessage(msg) ? 'text-indigo-100' : 'text-slate-600 dark:text-slate-300'"
                                    x-text="msg.sender_nickname || 'مهمان'"
                                ></span>
                                <span
                                    class="text-[10px]"
                                    :class="isOwnMessage(msg) ? 'text-indigo-100/80' : 'text-slate-400 dark:text-slate-400'"
                                    dir="ltr"
                                    x-text="formatDate(msg.created_at ?? '')"
                                ></span>
                            </div>

                            <div class="whitespace-pre-wrap leading-relaxed" x-text="msg.body"></div>

                            <template x-if="msg.attachments && msg.attachments.length">
                                <div class="mt-2 space-y-1">
                                    <template x-for="att in msg.attachments" :key="att.id">
                                        <a
                                            :href="att.signed_url"
                                            target="_blank"
                                            class="block text-[11px] underline"
                                            :class="isOwnMessage(msg) ? 'text-indigo-100' : 'text-sky-600 dark:text-sky-400'"
                                            x-text="att.original_name"
                                        ></a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            <div class="order-2">
                <div class="border rounded-2xl p-3 bg-white/90 dark:bg-slate-900/90 border-slate-200 dark:border-slate-700 relative">
                
                    <form x-on:submit.prevent="sendMessage" class="flex items-center gap-2">
                        
                        <input type="hidden" x-model="nickname" />
                        <div x-show="error" class="absolute bottom-full mb-1 text-xs text-red-500 bg-white dark:bg-slate-800 p-1 rounded-md shadow-lg right-0 left-0" x-text="error"></div>
                        
                        <label class="p-2 cursor-pointer text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.3291 21.3404C11.2391 21.3404 10.1491 20.9304 9.31906 20.1004C7.65906 18.4404 7.65906 15.7504 9.31906 14.0904L11.7991 11.6204C12.0891 11.3304 12.5691 11.3304 12.8591 11.6204C13.1491 11.9104 13.1491 12.3904 12.8591 12.6804L10.3791 15.1504C9.30906 16.2204 9.30906 17.9704 10.3791 19.0404C11.4491 20.1104 13.1991 20.1104 14.2691 19.0404L18.1591 15.1504C19.3391 13.9704 19.9891 12.4004 19.9891 10.7304C19.9891 9.06035 19.3391 7.49035 18.1591 6.31035C15.7191 3.87035 11.7591 3.87035 9.31906 6.31035L5.07906 10.5504C4.08906 11.5404 3.53906 12.8604 3.53906 14.2604C3.53906 15.6604 4.08906 16.9804 5.07906 17.9704C5.36906 18.2604 5.36906 18.7404 5.07906 19.0304C4.78906 19.3204 4.30906 19.3204 4.01906 19.0304C2.74906 17.7504 2.03906 16.0604 2.03906 14.2604C2.03906 12.4604 2.73906 10.7604 4.01906 9.49035L8.25906 5.25035C11.2791 2.23035 16.1991 2.23035 19.2191 5.25035C20.6791 6.71035 21.4891 8.66035 21.4891 10.7304C21.4891 12.8004 20.6791 14.7504 19.2191 16.2104L15.3291 20.1004C14.4991 20.9304 13.4191 21.3404 12.3291 21.3404Z" fill="#292D32"/>
                            </svg>

                            <input
                                type="file"
                                x-ref="fileInput"
                                @change="hasAttachment = $refs.fileInput.files.length > 0" class="hidden">
                        </label>

                        <textarea
                            x-model="body"
                            rows="1"
                            @keydown.enter.prevent="sendMessage"
                            placeholder="پیام خود را بنویسید..."
                            class="flex-1 max-h-40 min-h-[30px] border-none rounded-2xl px-3 py-2 text-sm bg-transparent dark:bg-transparent focus:outline-none focus:ring-0 resize-none overflow-y-auto"
                            style="transition: height 0.2s ease-out;">
                        </textarea> 
                        <button
                            type="submit"
                            class="p-2 rounded-full text-white transition duration-150 ease-in-out"
                            :class="!isSending && (body.trim() || hasAttachment) ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-slate-400 dark:bg-slate-600 cursor-not-allowed'"
                            :disabled="isSending || (!body.trim() && !hasAttachment)" >
                            
                            <svg x-show="!isSending" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform -rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <svg x-show="isSending" class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                    
                    <div x-show="hasAttachment" class="mt-2 text-xs text-slate-600 dark:text-slate-400">
                فایل پیوست شده       <button type="button" @click="$refs.fileInput.value = ''; hasAttachment = false;" class="text-red-500 hover:text-red-700 ml-2"> (حذف)</button>
                    </div>
                    
                </div>
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
                isFetchingMessages: false,
                messagesCacheKey: null,
                theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
                hasAttachment: false,

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
                isUserNearBottom() {
                    const el = this.$refs.messagesContainer;
                    if (!el) return true;

                    const threshold = 80;
                    const distanceFromBottom = el.scrollHeight - el.scrollTop - el.clientHeight;

                    return distanceFromBottom < threshold;
                },

                scrollToBottom(force = false) {
                    const el = this.$refs.messagesContainer;
                    if (!el) return;

                    if (!force && !this.isUserNearBottom()) {
                        return;
                    }

                    el.scrollTop = el.scrollHeight;
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

                        this.clientId = getOrCreateClientId(this.slug);
                        this.messagesCacheKey = `chat_room_${this.slug}_messages`;

                        this.loadCachedMessages();

                        this.$nextTick(() => {
                            this.scrollToBottom(true); 
                        });
                        this.fetchMessages(false);
                        this.fetchActiveCount();
                        this.pingPresence();

                        this.$watch('$refs.fileInput.files.length', (count) => {
                            this.hasAttachment = count > 0;
                        });
                        this.hasAttachment = this.$refs.fileInput?.files?.length > 0;
                        
                        setInterval(() => this.fetchMessages(false), 2000);
                        setInterval(() => this.fetchActiveCount(), 5000);
                        setInterval(() => this.pingPresence(), 15000);
                    } catch (e) {
                        const errorMessage = e.message || e.toString() || 'خطا در راه‌اندازی کامپوننت چت.';
                        this.displayError('خطا در راه‌اندازی: ' + errorMessage);
                    }
                },
                loadCachedMessages() {
                    try {
                        if (!this.messagesCacheKey) return;
                        const raw = sessionStorage.getItem(this.messagesCacheKey);
                        if (!raw) return;

                        const parsed = JSON.parse(raw);
                        if (!Array.isArray(parsed)) return;

                        this.messages = parsed;
                        if (this.messages.length) {
                            this.lastId = this.messages[this.messages.length - 1].id || null;
                        }
                    } catch (e) {
                    }
                },
                saveMessagesToCache() {
                    try {
                        if (!this.messagesCacheKey) return;
                        const maxMessages = 200;
                        const pruned = this.messages.slice(-maxMessages);
                        sessionStorage.setItem(this.messagesCacheKey, JSON.stringify(pruned));
                    } catch (e) {
                    }
                },
                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.theme === 'dark');
                    localStorage.setItem('theme', this.theme);
                },

                async fetchMessages(forceScroll = false) {
                    if (this.isFetchingMessages) return;
                    this.isFetchingMessages = true;

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

                        if (!data.data.length) return;

                        const shouldStick = this.isUserNearBottom();

                        data.data.forEach(msg => {
                            if (!this.messages.find(m => m.id === msg.id)) {
                                this.messages.push(msg);
                                this.lastId = msg.id;
                            }
                        });

                        this.saveMessagesToCache();

                        this.$nextTick(() => {
                            this.scrollToBottom(forceScroll || shouldStick);
                        });
                    } catch (e) {
                        this.displayError('خطای شبکه در دریافت پیام‌ها.');
                    } finally {
                        this.isFetchingMessages = false;
                    }
                },
                isOwnMessage(msg) {
                    if (msg.client_id && this.clientId) {
                        return msg.client_id === this.clientId;
                    }

                    if (msg.sender_nickname && this.nickname) {
                        return msg.sender_nickname === this.nickname;
                    }

                    return false;
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
                        formData.append('client_id', this.clientId);

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
                            this.hasAttachment = false;
                        }

                        this.saveMessagesToCache();

                        this.$nextTick(() => {
                            this.scrollToBottom(true);
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
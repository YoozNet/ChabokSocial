<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>چابک | @yield('title', 'چت مهمان')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="color-scheme" content="light dark">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ config('app.version', time()) }}">
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    backgroundImage: {
                        'web3-light': 'linear-gradient(to bottom right, #e0f2fe, #f0f9ff)',
                        'web3-dark': 'linear-gradient(to bottom right, #1f2937, #0f172a)',
                    }
                }
            }
        }
    </script>

    <style>
        .theme-transition {transition: background-color 0.3s, color 0.3s, border-color 0.3s;}
        .glass-border {border: 1px solid rgba(255, 255, 255, 0.5);}
        .dark .glass-border {border: 1px solid rgba(255, 255, 255, 0.1);}
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
            border: 2px solid transparent;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background-color: #475569;
        }
        .dark ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        #messages::-webkit-scrollbar {
            width: 8px;
        }
    </style>

    @stack('styles')
</head>

<body class="theme-transition bg-web3-light dark:bg-web3-dark text-gray-900 dark:text-gray-100 min-h-screen flex flex-col overflow-x-hidden">
    <header class="theme-transition sticky top-0 z-10  bg-white/40 dark:bg-gray-800/40  backdrop-blur-md glass-border rounded-b-xl shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold text-indigo-700 dark:text-indigo-300 drop-shadow-md">
                <a href="{{ url('/') }}">چابک</a>
            </h1>
            @if (request()->routeIs('admin.*'))
                <nav class="flex items-center gap-3 text-[11px]">
                    @auth('admin')
                        <a href="{{ route('admin.chat-rooms.index') }}"
                        class="text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
                            اتاق‌ها
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400">
                                خروج
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.login') }}"
                        class="text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
                            ورود ادمین
                        </a>
                    @endauth
                </nav>
            @endif
            <button id="themeToggle">
                <span id="sunIcon" class="hidden text-xl text-yellow-500">☀️</span>
                <span id="moonIcon" class="text-xl text-gray-300">🌙</span>
            </button>

        </div>
    </header>
    <main class="container mx-auto px-4 py-5 w-full">
        <div class="grid gap-5">
            <div class="theme-transition p-4 sm:p-8 rounded-3xl bg-white/20 dark:bg-gray-700/20  backdrop-blur-lg glass-border shadow-xl transform duration-300">
                @yield('content')
            </div>
        </div>
    </main>
    <footer class="theme-transition mt-8 bg-white/40 dark:bg-gray-800/40 backdrop-blur-md rounded-t-xl border-t border-slate-200 dark:border-slate-700">
        <div class="container mx-auto px-4 py-4 text-center text-sm text-slate-700 dark:text-slate-300">
            <p>
                © {{ date('Y') }} سامانه چت | توسعه و طراحی توسط <span class="font-bold text-indigo-600 dark:text-indigo-400">سعید شانقی</span>
            </p>
        </div>
    </footer>
    @stack('modals')
    @stack('scripts')
    <script src="{{ asset('js/alpinejs.min.js') }}" defer></script>
    <script>
        const htmlElement = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');

        const setInitialTheme = () => {
            const isDarkMode = localStorage.getItem('theme') === 'dark' ||
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDarkMode) {
                htmlElement.classList.add('dark');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                htmlElement.classList.remove('dark');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        };

        themeToggle.addEventListener('click', () => {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            } else {
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            }
        });

        setInitialTheme();
    </script>

</body>

</html>
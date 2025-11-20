@extends('layouts.app')
@section('title','تنظیمات محیط')
@section('content')

<form method="post" action="{{ route('install.env') }}" class="bg-white rounded-3xl p-6 md:p-10 shadow-2xl mx-auto">
    @csrf

    <h1 class="text-3xl font-extrabold text-indigo-700 mb-8 border-b pb-3">
        🛠️ پیکربندی محیط و زیرساخت برنامه
    </h1>

    <div class="mb-8 border-b pb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            🌍 اطلاعات پایه برنامه (Application Settings)
        </h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">
                    نام برنامه (APP_NAME)
                </label>
                <input name="app_name" id="app_name" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition" value="چابک" placeholder="مثال: چت روم بومی چابک" required>
            </div>
            <div>
                <label for="app_url" class="block text-sm font-medium text-gray-700 mb-1">
                    آدرس برنامه (APP_URL)
                    <span class="text-xs text-gray-500 mr-2">(شروع با https://)</span>
                </label>
                <input name="app_url" id="app_url" value="{{ url('/') }}" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition" placeholder="https://chabok.ir" required>
            </div>
        </div>
    </div>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            ⚙️ تنظیمات درایورهای کش، جلسه و صف
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
            
            <div>
                <label for="cache_driver" class="block text-sm font-medium text-gray-700 mb-1">
                    درایور کش (CACHE_DRIVER)
                </label>
                <select name="cache_driver" id="cache_driver" class="w-full rounded-xl border border-indigo-500 p-3 bg-indigo-50 font-semibold text-indigo-700">
                    <option value="database" selected>Database (پیشنهادی)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Database: مناسب برای سرورهای اشتراکی یا جلوگیری از مشکلات دسترسی به فایل.
                </p>
            </div>

            <div>
                <label for="session_driver" class="block text-sm font-medium text-gray-700 mb-1">
                    درایور جلسه (SESSION_DRIVER)
                </label>
                <select name="session_driver" id="session_driver" class="w-full rounded-xl border border-indigo-500 p-3 bg-indigo-50 font-semibold text-indigo-700">
                    <option value="database"selected>Database (پیشنهادی)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Database: برای محیط‌های چند سروره و تضمین ذخیره شدن اطلاعات جلسه کاربران.
                </p>
            </div>

            <div>
                <label for="queue_connection" class="block text-sm font-medium text-gray-700 mb-1">
                    اتصال صف (QUEUE_CONNECTION)
                </label>
                <select name="queue_connection" id="queue_connection" class="w-full rounded-xl border border-indigo-500 p-3 bg-indigo-50 font-semibold text-indigo-700">
                    <option value="database" selected>Database (پیشنهادی)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Database: برای اطمینان از پردازش وظایف سنگین پس‌زمینه (مثل ارسال ایمیل انبوه یا گزارش‌گیری).
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center">
        <button type="submit" class="px-10 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 transition duration-300 text-white font-bold text-lg shadow-lg hover:shadow-xl">
            ذخیره تنظیمات و ادامه نصب
        </button>
    </div>
</form>

@endsection
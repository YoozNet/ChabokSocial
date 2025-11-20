@extends('layouts.app')
@section('title','تنظیمات دیتابیس')
@section('content')

<form method="post" action="{{ route('install.database') }}" class="bg-white rounded-3xl p-6 md:p-10 shadow-2xl mx-auto">
    @csrf

    <h1 class="text-3xl font-extrabold text-indigo-700 mb-8 border-b pb-3">
        🔌 اتصال به پایگاه داده
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-3 mb-4">
            <label for="db_connection" class="block text-sm font-medium text-gray-700 mb-1">
                درایور دیتابیس (DB_CONNECTION)
            </label>
            <select name="db_connection" id="db_connection" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition">
                <option value="mysql" selected>MySQL</option>
            </select>
        </div>

        <div id="connection-fields" class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
                <label for="db_host" class="block text-sm font-medium text-gray-700 mb-1">
                    هاست (DB_HOST)
                </label>
                <input name="db_host" id="db_host" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition" value="127.0.0.1" placeholder="مثال: localhost">
            </div>

            <div>
                <label for="db_port" class="block text-sm font-medium text-gray-700 mb-1">
                    پورت (DB_PORT)
                </label>
                <input name="db_port" id="db_port" type="number" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition" value="3306" placeholder="مثال: 5432">
            </div>

            <div>
                <label for="db_database" class="block text-sm font-medium text-gray-700 mb-1">
                    نام دیتابیس (DB_DATABASE)
                </label>
                <input name="db_database" id="db_database" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition" placeholder="مثال: chabok_db">
            </div>

            <div>
                <label for="db_username" class="block text-sm font-medium text-gray-700 mb-1">
                    نام کاربری (DB_USERNAME)
                </label>
                <input name="db_username" id="db_username" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition" placeholder="مثال: root">
            </div>

            <div class="md:col-span-2">
                <label for="db_password" class="block text-sm font-medium text-gray-700 mb-1">
                    رمز عبور (DB_PASSWORD)
                </label>
                <input name="db_password" id="db_password" type="password" class="w-full rounded-xl border text-sm px-4 py-2.5 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent appearance-none theme-transition">
            </div>

        </div>

    </div>

    <div class="mt-8 text-center">
        <button type="submit" class="px-10 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 transition duration-300 text-white font-bold text-lg shadow-lg hover:shadow-xl">
            تست اتصال و ذخیره تنظیمات
        </button>
    </div>
</form>
@endsection
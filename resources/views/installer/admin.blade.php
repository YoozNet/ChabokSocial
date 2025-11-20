@extends('layouts.app')
@section('title','ساخت ادمین')
@section('content')

<form method="post" action="{{ route('install.admin') }}" class="bg-white rounded-3xl p-6 md:p-10 shadow-2xl mx-auto">
    @csrf

    <h1 class="text-3xl font-extrabold text-indigo-700 mb-6 border-b pb-3 text-center">
        👤 ایجاد حساب مدیر سیستم
    </h1>

    <p class="text-gray-600 mb-8 text-center">
        لطفاً اطلاعات حساب مدیر ارشد پلتفرم «چابک» را با دقت وارد نمایید.
    </p>

    <div class="grid gap-6">

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                نام کامل
            </label>
            <input name="name" id="name" class="w-full rounded-xl border text-sm px-4 py-2.5 pl-16 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent theme-transition" required placeholder="مثال: علی محمدی">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                آدرس ایمیل
                <span class="text-xs text-gray-500 mr-2">(جهت ورود به پنل)</span>
            </label>
            <input name="email" id="email" type="email" class="w-full rounded-xl border text-sm px-4 py-2.5 pl-16 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent theme-transition" required placeholder="example@gardon.ir">
        </div>
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                نام کاربری
                <span class="text-xs text-gray-500 mr-2">(جهت ورود به پنل)</span>
            </label>
            <input name="username" id="username" type="text" class="w-full rounded-xl border text-sm px-4 py-2.5 pl-16 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent theme-transition" required placeholder="username">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                رمز عبور
            </label>
            <input name="password" id="password" type="password" class="password-field w-full rounded-xl border text-sm px-4 py-2.5 pl-16 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent theme-transition" required minlength="12" placeholder="حداقل 12 کاراکتر">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                تکرار رمز عبور
            </label>
            <input name="password_confirmation" id="password_confirmation" type="password" class="password-field w-full rounded-xl border text-sm px-4 py-2.5 pl-16 bg-white/70 dark:bg-slate-900/60  border-indigo-300/50 dark:border-indigo-700/50 focus:outline-none focus:ring-2  focus:ring-indigo-500/70 focus:border-transparent theme-transition" required minlength="12">

            <p id="password-match-error" class="text-sm text-red-600 mt-2 hidden">
                ❌ رمز عبور و تکرار آن با هم مطابقت ندارند.
            </p>
        </div>

    </div>

    <div class="mt-8 text-center">
        <button id="submit-button" type="submit" disabled class="w-full px-10 py-3 rounded-xl bg-gray-400 text-white font-bold text-lg cursor-not-allowed shadow-lg">
            ایجاد حساب مدیر و اتمام نصب
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const submitButton = document.getElementById('submit-button');
        const errorText = document.getElementById('password-match-error');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');

        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            const isMatch = (password === confirm) && password.length >= 12;

            const isOtherFieldsFilled = nameInput.value.trim() !== '' && emailInput.value.trim() !== '';

            const canSubmit = isMatch && isOtherFieldsFilled;


            if (password !== '' && confirm !== '' && !isMatch) {
                errorText.classList.remove('hidden');
                confirmInput.classList.remove('border-gray-300');
                confirmInput.classList.add('border-red-500');
            } else {
                errorText.classList.add('hidden');
                confirmInput.classList.remove('border-red-500');
                confirmInput.classList.add('border-gray-300');
            }

            submitButton.disabled = !canSubmit;

            if (canSubmit) {
                submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                submitButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            }
        }

        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmInput.addEventListener('input', validatePasswordMatch);
        nameInput.addEventListener('input', validatePasswordMatch);
        emailInput.addEventListener('input', validatePasswordMatch);

        validatePasswordMatch();
    });
</script>

@endsection
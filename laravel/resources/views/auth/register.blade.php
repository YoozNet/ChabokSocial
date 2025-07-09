@include('layouts.header', ['title' => 'ثبت نام'])

<div class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-6 transition-all duration-500" id="registerCard">
    <h2 class="text-center text-2xl font-bold text-white mb-4 animate-pulse">ثبت نام</h2>

    <div id="messageBox" class="hidden text-sm p-2 rounded-xl mt-4 text-center"></div>

    <form id="registerForm" class="space-y-4">
        @csrf
        <div>
            <label class="block text-white mb-1">نام</label>
            <input type="text" id="name" class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all" name="name"
            placeholder="نام شما"
            required
            autocomplete="off"
            autocorrect="off"
            autocapitalize="none"
            spellcheck="false" />
        </div>
        <div>
            <label class="block text-white mb-1">نام کاربری</label>
            <input type="text" id="username" class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all" name="username"
            placeholder="مثلا akbar"
            required
            autocomplete="off"
            autocorrect="off"
            autocapitalize="none"
            spellcheck="false" />
        </div>
        <div class="relative">
            <label class="block text-white mb-1">رمز عبور</label>
            <input type="password" id="password"
                class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all pr-10"
                placeholder="••••••"
                required
                autocomplete="new-password"
                autocorrect="off"
                maxlength="32"
                minlength="8"
                autocapitalize="none"
                oninvalid="this.setCustomValidity('رمز عبور باید بین ۸ تا ۳۲ نویسه باشد')"
                oninput="this.setCustomValidity('')"
                spellcheck="false" />
            <button type="button" id="togglePassword"
                class="absolute top-9 left-3 text-gray-300 hover:text-white focus:outline-none">
                <!-- heroicons eye svg -->
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
        <div class="relative">
            <label class="block text-white mb-1">تکرار رمز عبور</label>
            <input type="password" id="confirmPassword"
                class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all pr-10"
                placeholder="••••••"
                required
                maxlength="32"
                minlength="8"
                autocomplete="new-password"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false" />
            <span id="matchIcon" class="absolute top-9 left-3 text-xl hidden transition-colors duration-300">✔️</span>
        </div>
        <button type="button" id="nextStep" class="w-full p-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:scale-105 transition-transform">ادامه</button>
    </form>

    <div id="twofaStep" class="hidden mt-6 animate-fade-in">
        <h3 class="text-center text-xl text-white mb-4">تایید دو مرحله‌ای</h3>
        <div class="flex justify-center mb-4">
            <img id="qrImage" src="" alt="QR Code" class="rounded shadow-lg" />
        </div>
        <div class="bg-white/20 rounded-xl p-4 flex items-center justify-between text-white">
            <span id="2faSecret" style="flex: 1;padding: 0.5rem;border: 1px dashed #999;border-radius: 6px;font-family: monospace;font-size: 0.9rem;direction: ltr;text-align: left;">---</span>
            <button type="button" id="copySecret" class="text-xs bg-purple-600 px-2 py-1 rounded hover:bg-purple-700">کپی</button>
        </div>
        <div class="mt-4">
            <label class="block text-white mb-1">کد ۶ رقمی</label>
            <input type="text" id="totp_code" maxlength="6" class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all" name="totp_code"
                required
                inputmode="numeric"
                pattern="[0-9]{6}"
                autocomplete="one-time-code"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false"
                placeholder="123456" />
        </div>
        <button type="button" id="finalSubmit" class="w-full mt-4 p-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl hover:scale-105 transition-transform">ثبت نهایی</button>
    </div>
</div>

@section('script')
<script>
window.routes = {
    registerSubmit: "{{ route('register.submit') }}",
    verifySubmit: "{{ route('verify.submit') }}"
}

const password = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');
const eyeIcon = document.getElementById('eyeIcon');
togglePassword.addEventListener('click', () => {
    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                a9.956 9.956 0 012.153-3.363m3.204-2.42A9.956 9.956 0 0112 5
                c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.155 2.328M15 12
                a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3l18 18" />`;
    } else {
        password.type = 'password';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943
                9.542 7-1.274 4.057-5.065 7-9.542 7-4.477
                0-8.268-2.943-9.542-7z" />`;
    }
});

const confirmPassword = document.getElementById('confirmPassword');
const matchIcon = document.getElementById('matchIcon');
confirmPassword.addEventListener('input', () => {
    if (confirmPassword.value.length > 0) {
        if (confirmPassword.value === password.value) {
            matchIcon.textContent = '✔️';
            matchIcon.style.color = 'limegreen';
            matchIcon.classList.remove('hidden');
        } else {
            matchIcon.textContent = '❌';
            matchIcon.style.color = 'red';
            matchIcon.classList.remove('hidden');
        }
    } else {
        matchIcon.classList.add('hidden');
    }
});

const strength = document.getElementById('passwordStrength');
const strengthText = document.getElementById('strengthText');
password.addEventListener('input', () => {
    const val = password.value;
    let score = 0;
    if (val.length >= 8) score += 30;
    if (/[A-Z]/.test(val)) score += 20;
    if (/[0-9]/.test(val)) score += 20;
    if (/[^A-Za-z0-9]/.test(val)) score += 30;

    strength.classList.remove('bg-red-500','bg-yellow-500','bg-green-500');
    if (score < 40) {
        strength.classList.add('bg-red-500');
        strengthText.textContent = 'ضعیف';
    } else if (score < 70) {
        strength.classList.add('bg-yellow-500');
        strengthText.textContent = 'متوسط';
    } else {
        strength.classList.add('bg-green-500');
        strengthText.textContent = 'قوی';
    }
    strength.style.width = score + '%';
});

// فرم و دکمه‌ها
const nextStep = document.getElementById('nextStep');
const form = document.getElementById('registerForm');
const twofa = document.getElementById('twofaStep');
const qrImage = document.getElementById('qrImage');
const twofaSecret = document.getElementById('2faSecret');
const finalSubmit = document.getElementById('finalSubmit');
const messageBox = document.getElementById('messageBox');

function showMessage(text, type = 'error') {
    messageBox.textContent = text;
    messageBox.classList.remove('hidden','bg-red-600','bg-green-600');
    messageBox.classList.add(type === 'success' ? 'bg-green-600' : 'bg-red-600', 'text-white','animate-pulse');
    setTimeout(() => messageBox.classList.remove('animate-pulse'),1500);
}

nextStep.addEventListener('click', async () => {
    const name = document.getElementById('name').value.trim();
    const username = document.getElementById('username').value.trim();
    const pass = password.value;
    const confirm = confirmPassword.value;
    
    if (!name || !username || !pass || !confirm) {
        showMessage("لطفا فیلدها را کامل وارد کنید.","error");
        return;
    }

    if (pass.length < 8 || pass.length > 32) {
        showMessage("رمز عبور باید بین ۸ تا ۳۲ نویسه باشد.","error");
        return;
    }

    if (pass !== confirm) {
        showMessage("رمز عبور و تکرار آن یکسان نیستند.","error");
        return;
    }

    if (name && username && pass && confirm && pass === confirm) {
        nextStep.disabled = true;
        try {
            const res = await fetch(window.routes.registerSubmit, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                credentials: "same-origin",
                body: JSON.stringify({ name, username, password: pass, password_confirmation: confirm })
            });
            const data = await res.json();
            if (data.status === "ok") {
                twofaSecret.textContent = data.secret;
                qrImage.src = data.qr;
                twofa.classList.remove('hidden');
                form.classList.add('hidden');
                showMessage("حالا کد ۶ رقمی را وارد کنید","success");
            } else {
                showMessage(data.message || "خطا در ثبت نام","error");
                nextStep.disabled = false;
            }
        } catch(e) {
            console.error(e);
            showMessage("مشکل در ارتباط با سرور","error");
            nextStep.disabled = false;
        }
    } else {
        showMessage("لطفا فیلدها را کامل و رمز را درست تکرار کنید.","error");
    }
});

finalSubmit.addEventListener('click', async () => {
    const code = document.getElementById('totp_code').value.trim();
    if (code.length === 6) {
        finalSubmit.disabled = true;
        try {
            const res = await fetch(window.routes.verifySubmit, {
                method: "POST",
                headers: {"Content-Type": "application/json","X-CSRF-TOKEN": "{{ csrf_token() }}","Accept": "application/json"},
                credentials: "same-origin",
                body: JSON.stringify({ totp_code: code })
            });
            const data = await res.json();
            if (data.status === "ok") {
                showMessage("ثبت نام تکمیل شد، منتقل می‌شوید ...","success");
                setTimeout(() => window.location.href = "/dashboard", 1500);
            } else {
                showMessage(data.message || "کد اشتباه است","error");
                finalSubmit.disabled = false;
            }
        } catch(e) {
            console.error(e);
            showMessage("مشکل در ارتباط با سرور","error");
            finalSubmit.disabled = false;
        }
    } else {
        showMessage("کد معتبر وارد کنید","error");
    }
});

document.getElementById('copySecret').addEventListener('click', () => {
    navigator.clipboard.writeText(twofaSecret.textContent);
    showMessage("کپی شد!","success");
});
</script>
@endsection

@include('layouts.footer')

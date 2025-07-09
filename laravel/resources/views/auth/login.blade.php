@include('layouts.header', ['title' => 'ورود'])

<div class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-6 transition-all duration-500">
    <h2 class="text-center text-2xl font-bold text-white mb-4 animate-pulse">ورود</h2>

    <div id="messageBox" class="hidden text-sm p-2 rounded-xl mt-4 text-center"></div>

    {{-- فرم ورود --}}
    <form id="loginForm" class="space-y-4">
        @csrf
        <div>
            <label class="block text-white mb-1">نام کاربری</label>
            <input type="text" id="username" name="username"
                   class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all"
                   placeholder="نام کاربری" required
                   autocomplete="off"
                   autocorrect="off"
                   autocapitalize="none"
                   spellcheck="false"/>
        </div>
        <div class="relative">
            <label class="block text-white mb-1">رمز عبور</label>
            <input type="password" id="password" name="password" maxlength="32" minlength="8"
                    class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all"
                    placeholder="••••••" required autocomplete="current-password" autocorrect="off" autocapitalize="none" spellcheck="false"/>
            <!-- آیکن چشم -->
            <button type="button" id="togglePassword" class="absolute left-3 top-9 text-white hover:text-purple-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 4.5c-7 0-10 7.5-10 7.5s3 7.5 10 7.5 10-7.5 10-7.5-3-7.5-10-7.5zm0 12c-2.5 0-4.5-2-4.5-4.5S9.5 7.5 12 7.5 16.5 9.5 16.5 12 14.5 16.5 12 16.5z"/>
                <circle cx="12" cy="12" r="2.5" fill="#fff"/>
                </svg>
            </button>
        </div>
        <button type="button" id="nextStep"
                class="w-full p-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:scale-105 transition-transform">ادامه</button>
    </form>

    {{-- مرحله وارد کردن کد --}}
    <div id="twofaStep" class="hidden mt-6 animate-fade-in">
        <h3 class="text-center text-xl text-white mb-4">تایید دو مرحله‌ای</h3>
        <div id="qrContainer" class="flex justify-center mb-4 hidden">
            <img id="qrImage" src="" alt="QR Code" class="rounded shadow-lg" />
        </div>
        <div id="secretContainer" class="bg-white/20 rounded-xl p-4 flex items-center justify-between text-white mb-4 hidden">
            <span id="2faSecret"
                  style="flex: 1;padding: 0.5rem;border: 1px dashed #999;border-radius: 6px;font-family: monospace;font-size: 0.9rem;direction: ltr;text-align: left;">---</span>
            <button type="button" id="copySecret"
                    class="text-xs bg-purple-600 px-2 py-1 rounded hover:bg-purple-700">کپی</button>
        </div>
        <div class="mt-4">
            <label class="block text-white mb-1">کد ۶ رقمی</label>
            <input type="text" id="totp_code" maxlength="6"
                   class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all"
                   placeholder="123456" required inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" autocorrect="off" autocapitalize="none" spellcheck="false" placeholder="123456" />
        </div>
        <div class="mt-4">
            <label class="block text-white mb-1">کد ۸ رقمی بکاپ (اختیاری)</label>
            <input type="text" id="backup_code"
                class="w-full p-2 rounded-xl bg-white/20 text-white placeholder-gray-300 focus:ring focus:ring-purple-500 transition-all"
                placeholder="مثلاً 4G7XZQ9K"
                maxlength="8"
                autocomplete="one-time-code"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false" />
        </div>
        <button type="button" id="finalSubmit"
                class="w-full mt-4 p-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl hover:scale-105 transition-transform">تایید نهایی</button>
    </div>
</div>

@section('script')
<script>
document.getElementById('togglePassword').addEventListener('click', () => {
    const passField = document.getElementById('password');
    if(passField.type === 'password') {
        passField.type = 'text';
    } else {
        passField.type = 'password';
    }
});

document.getElementById('loginForm').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault(); 
        nextStep.click();
    }
});
document.getElementById('totp_code').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('finalSubmit').click();
    }
});
</script>
<script>
    const nextStep = document.getElementById('nextStep');
    const messageBox = document.getElementById('messageBox');
    const twofa = document.getElementById('twofaStep');
    const qrImage = document.getElementById('qrImage');
    const twofaSecret = document.getElementById('2faSecret');
    const finalSubmit = document.getElementById('finalSubmit');
    const qrContainer = document.getElementById('qrContainer');
    const secretContainer = document.getElementById('secretContainer');
    const loginForm = document.getElementById('loginForm');

    function showMessage(text, type = 'error') {
        messageBox.textContent = text;
        messageBox.classList.remove('hidden', 'bg-green-600', 'bg-red-600');
        if (type === 'success') {
            messageBox.classList.add('bg-green-600', 'text-white');
        } else {
            messageBox.classList.add('bg-red-600', 'text-white');
        }
        messageBox.classList.add('animate-pulse');
        setTimeout(() => {
            messageBox.classList.remove('animate-pulse');
        }, 1500);
    }

    nextStep.addEventListener('click', async () => {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        if(username && password){
            nextStep.disabled = true;
            try {
                const res = await fetch("{{ route('login.submit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });
                const data = await res.json();

                if(data.status === "twofa_activation"){
                    qrImage.src = data.qr;
                    twofaSecret.textContent = data.secret;
                    qrContainer.classList.remove('hidden');
                    secretContainer.classList.remove('hidden');
                    twofa.classList.remove('hidden');
                    loginForm.classList.add('hidden');
                    showMessage(data.message, "success");
                }
                else if(data.status === "twofa_login"){
                    qrContainer.classList.add('hidden');
                    secretContainer.classList.add('hidden');
                    twofa.classList.remove('hidden');
                    loginForm.classList.add('hidden');
                    showMessage(data.message, "success");
                }
                else {
                    showMessage(data.message || "خطا در ورود", "error");
                    nextStep.disabled = false;
                }
            } catch (e) {
                console.error(e);
                showMessage("مشکل در ارتباط با سرور", "error");
                nextStep.disabled = false;
            }
        } else {
            showMessage("نام کاربری و رمز را کامل وارد کنید", "error");
        }
    });

    finalSubmit.addEventListener('click', async () => {
        const totp_code = document.getElementById('totp_code').value.trim();
        const backup_code = document.getElementById('backup_code').value.trim();

        if (totp_code.length !== 6 && backup_code.length !== 8) {
            showMessage("کد ۶ رقمی یا کد بکاپ ۸ رقمی را وارد کنید", "error");
            return;
        }

        finalSubmit.disabled = true;

        try {
            const res = await fetch("{{ route('login.verify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    totp_code,
                    backup_code
                })
            });

            const data = await res.json();

            if (data.status === "ok") {
                showMessage("ورود انجام شد، در حال انتقال ...", "success");
                setTimeout(() => window.location.href="/dashboard", 1500);
            } else {
                showMessage(data.message || "کد وارد شده معتبر نیست", "error");
                finalSubmit.disabled = false;
            }
        } catch (e) {
            console.error(e);
            showMessage("مشکل در ارتباط با سرور", "error");
            finalSubmit.disabled = false;
        }
    });

    document.getElementById('copySecret').addEventListener('click', () => {
        navigator.clipboard.writeText(twofaSecret.textContent);
        showMessage("کپی شد!", "success");
    });
</script>
@endsection

@include('layouts.footer')

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>پیام‌رسان چابک</title>
    <link href="{{ asset('assets/css/style.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/tailwindcss.js') }}"></script>
    <style>
      @keyframes float {
        0%,100% { transform: translateY(0); }
        50%     { transform: translateY(-15px); }
      }
      .animate-float { animation: float 4s ease-in-out infinite; }

      @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
      }
      .animate-spin-slow { animation: spin-slow 12s linear infinite; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <!-- Header -->
  <header class="bg-white shadow-md">
    <div class="container mx-auto flex justify-between items-center p-4">
      <!-- Hamburger & Logo (در حالت موبایل) -->
      <div class="flex items-center lg:hidden">
        <!-- دکمه همبرگر -->
        <button id="menu-toggle" class="focus:outline-none">
          <!-- SVG آیکون سه‌خط -->
          <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <!-- لوگو -->
        <div class="text-2xl font-bold mr-4">پیامرسان چابک</div>
      </div>

      <!-- Login & Desktop Menu -->
      <div class="hidden lg:flex justify-between items-center w-full">
        <!-- Login Section -->
        <div>
          <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:text-blue-800">ورود</a>
        </div>
        <!-- Logo در دسکتاپ (می‌توانید این را مخفی کنید اگر ترجیح می‌دهید) -->
        <div class="text-2xl font-bold px-6">پیامرسان چابک</div>
        <!-- Navigation -->
        <nav class="space-x-6">
          <a href="{{ route('welcome') }}" class="hover:text-blue-600">خانه</a>
        </nav>
      </div>
    </div>

    <!-- منوی موبایل (پنهان به‌صورت پیش‌فرض) -->
    <nav id="mobile-menu" class="hidden bg-white border-t border-gray-200 lg:hidden">
      <div class="px-4 py-3 space-y-2">
        <a href="{{ route('login') }}" class="block text-blue-600 font-semibold hover:text-blue-800">ورود</a>
        <a href="{{ route('welcome') }}" class="block hover:text-blue-600">خانه</a>
      </div>
    </nav>

  </header>


  <!-- Hero Section -->
  <section class="container mx-auto flex flex-col lg:flex-row items-center py-16 px-4 relative">
    <!-- Text -->
    <div class="lg:w-1/2 text-center lg:text-right z-10 text-white">
      <h1 class="text-4xl lg:text-5xl font-extrabold mb-4">پیام‌رسان نسل جدید</h1>
      <p class="text-lg mb-6 leading-relaxed" style="text-align: justify !important;">
        پیام‌رسان ما با تکیه بر فناوری Web3 بستری کاملاً غیرمتمرکز فراهم می‌کند تا کنترل کامل داده‌ها و حریم خصوصی شما در دستان خودتان باشد.  
        ارتباطات بلادرنگ (Real-Time) با رمزنگاری End-to-End تضمین می‌کند که پیام‌هایتان تنها برای مخاطب مورد نظر شما خواندنی باشند.  
        پروتکل‌های مدرنِ انتقال (مثل WebSocket و IPFS) سرعت فوق‌العاده‌ای در ارسال و دریافت پیام ارائه می‌دهند و از ازدحام سرورهای متمرکز جلوگیری می‌کنند.  
        احراز هویت دو مرحله‌ای (2FA) و احراز اصالت بر مبنای کلیدهای خصوصی، لایه‌ای اضافی از امنیت را فراهم کرده تا خیالتان از سوء‌استفاده‌ها راحت باشد.  
      </p>
      <a href="{{ route('register.form') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
        شروع کنیم
      </a>
    </div>

    <!-- Animated SVG Cluster -->
    <div class="lg:w-1/2 mt-8 lg:mt-0 relative w-full h-80">
      <!-- دایره‌ای که بالا و پایین شناور می‌شود -->
      <svg class="absolute w-24 h-24 top-0 left-1/4 animate-float" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="40" fill="#3B82F6"/>
      </svg>

      <!-- آیکون قفل با دوران کند -->
      <svg class="absolute w-20 h-20 bottom-10 right-1/3 animate-spin-slow" viewBox="0 0 24 24">
        <path fill="#F59E0B" d="M12 1a4 4 0 014 4v3h1a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V10a2 2 0 012-2h1V5a4 4 0 014-4z"/>
      </svg>

      <!-- بلور کوچک که با تاخیر پالس می‌زند -->
      <svg class="absolute w-16 h-16 top-1/3 right-1/4 animate-pulse delay-200"
          viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="30" fill="#10B981"/>
      </svg>

      <!-- متن مرکزی یا آیکون چت -->
      <svg class="absolute w-28 h-28 top-1/4 left-1/2 transform -translate-x-1/2 animate-bounce" viewBox="0 0 24 24">
        <path fill="#EF4444" d="M2 2h20v16H6l-4 4V2z"/>
      </svg>
    </div>

  </section>

  <!-- Features -->
  <section id="features" class="bg-white py-12">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8">ویژگی‌های برجسته</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- ارگون -->
        <div class="p-6 bg-gray-100 rounded-lg shadow-sm text-center">
          <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
            <!-- مثال آیکون کاربر -->
            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 2a4 4 0 100 8 4 4 0 000-8zM2 18a8 8 0 0116 0H2z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-2">ارگون</h3>
          <p class="text-gray-600">رابط کاربری ارگونومیک و ساده برای همه کاربران</p>
        </div>

        <!-- حریم خصوصی -->
        <div class="p-6 bg-gray-100 rounded-lg shadow-sm text-center">
          <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
            <!-- مثال آیکون قفل -->
            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 1a4 4 0 00-4 4v3H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V10a2 2 0 00-2-2h-1V5a4 4 0 00-4-4z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-2">حریم خصوصی</h3>
          <p class="text-gray-600">رمزنگاری End-to-End برای هر پیام</p>
        </div>

        <!-- احراز هویت دو مرحله‌ای -->
        <div class="p-6 bg-gray-100 rounded-lg shadow-sm text-center">
          <div class="w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center">
            <!-- مثال آیکون شیلد -->
            <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 0L2 4v6c0 5 4 9 8 10 4-1 8-5 8-10V4l-8-4z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-2">احراز هویت دو مرحله‌ای</h3>
          <p class="text-gray-600">لایه اضافی امنیت با تایید دومرحله‌ای</p>
        </div>

        <!-- اتصال Real-Time -->
        <div class="p-6 bg-gray-100 rounded-lg shadow-sm text-center">
          <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
            <!-- مثال آیکون صاعقه -->
            <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M11 0L3 12h6v8l8-12h-6z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-2">اتصال Real-Time</h3>
          <p class="text-gray-600">سریع‌ترین متودها برای ارتباط آنی</p>
        </div>

      </div>

      <div class="text-center mt-10">
        <a href="{{ route('register.form') }}"
          class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition">
          همین الان ثبت‌نام کنید
        </a>
      </div>
    </div>
  </section>


  <!-- Footer -->
  <footer class="bg-gray-900 text-gray-400 py-8">
    <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center">
      <!-- Copy -->
      <div class="text-center md:text-left mb-4 md:mb-0">
        <p class="text-sm">&copy; 2025 پیام‌رسان وب3. همه حقوق محفوظ است.</p>
      </div>
      <!-- Links -->
      <div class="flex items-center space-x-6">
        <a href="#" class="text-gray-400 hover:text-white transition">سیاست حفظ حریم خصوصی</a>
        <a href="#" class="text-gray-400 hover:text-white transition">شرایط استفاده</a>
        <!-- GitHub -->
        <a href="https://github.com/your-repo" target="_blank" rel="noopener"
          class="flex items-center text-gray-400 hover:text-white transition">
          <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M12 .297a12 12 0 00-3.797 23.405c.6.11.82-.26.82-.578 0-.286-.01-1.04-.015-2.04-3.338.726-4.042-1.61-4.042-1.61-.546-1.387-1.334-1.756-1.334-1.756-1.09-.745.082-.73.082-.73 1.205.085 1.84 1.238 1.84 1.238 1.07 1.835 2.807 1.305 3.492.998.108-.775.418-1.305.762-1.605-2.665-.304-5.467-1.333-5.467-5.93 0-1.31.468-2.382 1.236-3.222-.124-.303-.536-1.525.117-3.176 0 0 1.008-.323 3.3 1.23a11.5 11.5 0 016.003 0c2.29-1.553 3.297-1.23 3.297-1.23.654 1.651.242 2.873.118 3.176.77.84 1.235 1.912 1.235 3.222 0 4.61-2.807 5.624-5.48 5.92.43.372.815 1.102.815 2.222 0 1.604-.015 2.896-.015 3.286 0 .32.218.694.825.576A12.003 12.003 0 0012 .297z"/>
          </svg>
          <span>GitHub</span>
        </a>
      </div>
    </div>
  </footer>

  <script>
    const btn = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  </script>
</body>
</html>

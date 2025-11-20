@extends('layouts.app')
@section('title','ویزارد نصب')
@section('content')
<div class="p-6 md:p-10 bg-white shadow-xl rounded-2xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-extrabold text-indigo-700 mb-6 text-center">
       💬 به چت روم کاملاً بومی چابک خوش آمدید!
    </h1>
    <p class="text-gray-700 text-lg mb-8 leading-relaxed text-center">
        چابک، یک سیستم چت روم سریع و امن است که با افتخار، توسعه و کدنویسی آن به طور کامل توسط متخصصین ایرانی انجام شده و بستر مناسبی برای گفتگوهای آنلاین کاربران فراهم می‌کند.
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <div class="col-span-1 lg:col-span-2">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                🛠️ گزارش خطا و پشتیبانی فنی
            </h2>
            <p class="text-gray-600 mb-4 leading-relaxed">
                <span class="font-bold text-red-600">توجه:</span> در صورتی که در هر بخشی از سیستم، با باگ یا خطای عملکردی مواجه شدید، لطفاً بلافاصله جهت رفع رایگان، موضوع را به آیدی تلگرام زیر اطلاع دهید:
            </p>
            <blockquote class="bg-red-50 border-r-4 border-blue-500 p-4 rounded-lg font-mono text-blue-700">
                <a href="tg://resolve?domain=bugslay" class="text-blue-700 hover:text-blue-900">
                    &#64;bugslay (تلگرام)
                </a>
            </blockquote>
        </div>

        <div class="col-span-1">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                ⚖️ حق کپی رایت
            </h2>
            <p class="text-gray-600 leading-relaxed">
                توسعه این سیستم چت روم کاملاً توسط سعید شانقی انجام شده است. هرگونه نقض حق کپی‌رایت، کپی‌برداری از سورس کد یا توزیع بدون مجوز آن، ممنوع و موجب پیگرد قانونی خواهد بود.
            </p>
        </div>
    </div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4 border-b pb-2 flex items-center">
        💰 قوانین و تعرفه‌های به‌روزرسانی (آپدیت)
    </h2>
    <p class="text-gray-600 mb-6 leading-relaxed">
        هرگونه درخواست به‌روزرسانی یا افزودن قابلیت جدید به سیستم، مشمول هزینه خواهد بود و بر اساس سه مدل زیر انجام می‌پذیرد:
    </p>

    <div class="space-y-6">
        <div class="bg-blue-50 p-5 rounded-xl border border-blue-200">
            <h3 class="text-xl font-semibold text-blue-700 mb-2 flex items-center">
                1. آپدیت عمومی (Community Update) 🌍
            </h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2 leading-relaxed">
                <li>ما نیاز به به‌روزرسانی مورد نظر را در کانال رسمی اعلام می‌کنیم.</li>
                <li>برای شروع کار، به تأمین هزینه از طرف حداقل N نفر نیاز است.</li>
                <li>در صورت نرسیدن به حد نصاب، مبالغ پرداختی عودت داده می‌شود یا مابقی هزینه بین اعضای پرداخت‌کننده تقسیم و دریافت خواهد شد.</li>
            </ul>
        </div>

        <div class="bg-yellow-50 p-5 rounded-xl border border-yellow-200">
            <h3 class="text-xl font-semibold text-yellow-700 mb-2 flex items-center">
                2. آپدیت نیمه عمومی (Semi-Public) 🔄
            </h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2 leading-relaxed">
                <li>به‌روزرسانی مورد نظر شما، با تعرفه‌ای کمتر اعمال و توسعه داده می‌شود.</li>
                <li>کد توسعه‌یافته به صورت عمومی منتشر می‌شود.</li>
                <li>کد اصلی در اختیار شما به صورت Open Source قرار می‌گیرد، اما نسخه منتشر شده برای عموم به صورت Encoded خواهد بود.</li>
                <li>این روش زمان کمتری می‌برد.</li>
            </ul>
        </div>

        <div class="bg-green-50 p-5 rounded-xl border border-green-200">
            <h3 class="text-xl font-semibold text-green-700 mb-2 flex items-center">
                3. آپدیت خصوصی (Private/Custom) 🔒
            </h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2 leading-relaxed">
                <li>تعرفه توسعه بر اساس توافق مستقیم تعیین می‌شود.</li>
                <li>محصول یا قابلیت مورد نظر به صورت اختصاصی برای شما توسعه و ارائه خواهد شد.</li>
            </ul>
        </div>
    </div>
    
    <div class="grid gap-4 mt-10">
        <a href="{{ route('install.preflight') }}" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 transition duration-300 text-white text-center font-bold text-lg shadow-lg">
            شروع نصب و پیکربندی سیستم
        </a>
    </div>
</div>
@endsection
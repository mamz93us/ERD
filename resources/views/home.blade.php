@php
    use App\Models\SystemSetting;
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $name = $isRtl
        ? SystemSetting::get('system.name_ar', 'مجموعة عدلي')
        : SystemSetting::get('system.name', 'Adly Group Agency');
    $logoPath = SystemSetting::get('system.logo_path');
    $logoUrl = $logoPath ? asset('storage/' . ltrim($logoPath, '/')) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', system-ui, sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-amber-900 text-white min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-10">

        {{-- Locale switcher --}}
        <div class="flex justify-{{ $isRtl ? 'start' : 'end' }} mb-6">
            <a href="{{ url('?locale=' . ($isRtl ? 'en' : 'ar')) }}"
               class="text-xs bg-white/10 hover:bg-white/20 rounded-full px-3 py-1">
                {{ $isRtl ? 'English' : 'العربية' }}
            </a>
        </div>

        <div class="text-center mb-10">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $name }}" class="h-20 mx-auto mb-4 drop-shadow-lg" />
            @endif
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight drop-shadow-lg">{{ $name }}</h1>
            <p class="mt-3 text-amber-200 text-lg">{{ $isRtl ? 'منظومة إدارة تأجير السيارات' : 'Car Rental Management System' }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Admin panel --}}
            <a href="{{ url('/admin') }}"
               class="group bg-white/10 hover:bg-white/15 backdrop-blur border border-white/20 rounded-2xl p-6 shadow-xl transition transform hover:-translate-y-1">
                <div class="text-5xl mb-3">🛠️</div>
                <h2 class="text-xl font-bold mb-1">{{ $isRtl ? 'لوحة التحكم الإدارية' : 'Admin Panel' }}</h2>
                <p class="text-sm text-white/70">{{ $isRtl ? 'الموظفون والمحاسبون ومنسقو الرحلات' : 'Staff, accountants, dispatchers' }}</p>
                <div class="mt-4 text-xs font-semibold text-amber-300 group-hover:text-amber-200">
                    {{ $isRtl ? 'دخول →' : 'Open →' }}
                </div>
            </a>

            {{-- Driver portal --}}
            <a href="{{ url('/driver') }}"
               class="group bg-white/10 hover:bg-white/15 backdrop-blur border border-white/20 rounded-2xl p-6 shadow-xl transition transform hover:-translate-y-1">
                <div class="text-5xl mb-3">🚗</div>
                <h2 class="text-xl font-bold mb-1">{{ $isRtl ? 'بوابة السائقين' : 'Driver Portal' }}</h2>
                <p class="text-sm text-white/70">{{ $isRtl ? 'رحلات اليوم، البدء والإنهاء، الرواتب' : "Today's trips, start/end, payroll" }}</p>
                <div class="mt-4 text-xs font-semibold text-amber-300 group-hover:text-amber-200">
                    {{ $isRtl ? 'دخول →' : 'Open →' }}
                </div>
            </a>

            {{-- Customer portal --}}
            <a href="{{ url('/portal') }}"
               class="group bg-white/10 hover:bg-white/15 backdrop-blur border border-white/20 rounded-2xl p-6 shadow-xl transition transform hover:-translate-y-1">
                <div class="text-5xl mb-3">👤</div>
                <h2 class="text-xl font-bold mb-1">{{ $isRtl ? 'بوابة العملاء' : 'Customer Portal' }}</h2>
                <p class="text-sm text-white/70">{{ $isRtl ? 'الحجوزات، العروض، الفواتير' : 'Bookings, quotations, invoices' }}</p>
                <div class="mt-4 text-xs font-semibold text-amber-300 group-hover:text-amber-200">
                    {{ $isRtl ? 'دخول →' : 'Open →' }}
                </div>
            </a>
        </div>

        <div class="mt-10 text-center text-xs text-white/40">
            {{ $isRtl ? 'جميع الحقوق محفوظة' : 'All rights reserved' }} © {{ date('Y') }} {{ $name }}
        </div>
    </div>
</body>
</html>

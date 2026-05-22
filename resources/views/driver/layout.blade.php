@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = $isRtl ? 'مجموعة عدلي' : 'Adly Group Agency';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('driver.brand') }} — {{ $brand }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    @auth('driver')
        <header class="bg-amber-500 text-white shadow-md sticky top-0 z-10">
            <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
                <a href="{{ route('driver.dashboard') }}" class="font-bold text-lg">{{ $brand }}</a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('driver.payroll') }}" class="text-xs bg-white/20 rounded-full px-3 py-1">{{ __('driver.payroll') }}</a>
                    <form method="POST" action="{{ route('driver.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs bg-white/20 rounded-full px-3 py-1">{{ __('driver.logout') }}</button>
                    </form>
                </div>
            </div>
        </header>
    @endauth

    @if(session('status'))
        <div class="max-w-md mx-auto mt-3 px-4">
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-lg px-3 py-2 text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-md mx-auto mt-3 px-4">
            <div class="bg-rose-100 border border-rose-300 text-rose-900 rounded-lg px-3 py-2 text-sm">
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <main class="max-w-md mx-auto px-4 py-4">
        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = $isRtl ? 'مجموعة عدلي' : 'Adly Group Agency';
    $supportPhone = '+201000000000'; // TODO config: portal.support_phone
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('portal.brand') }} — {{ $brand }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', system-ui, sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    @auth('customer')
        <header class="bg-slate-900 text-white shadow-md sticky top-0 z-10">
            <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
                <a href="{{ route('portal.dashboard') }}" class="font-bold text-lg">{{ $brand }}</a>
                <nav class="flex items-center gap-1 text-xs">
                    <a href="{{ route('portal.dashboard') }}" class="px-2 py-1 rounded hover:bg-white/10">{{ __('portal.nav_dashboard') }}</a>
                    <a href="{{ route('portal.trips.index') }}" class="px-2 py-1 rounded hover:bg-white/10">{{ __('portal.nav_trips') }}</a>
                    <a href="{{ route('portal.quotations.index') }}" class="px-2 py-1 rounded hover:bg-white/10">{{ __('portal.nav_quotations') }}</a>
                    <a href="{{ route('portal.invoices.index') }}" class="px-2 py-1 rounded hover:bg-white/10">{{ __('portal.nav_invoices') }}</a>
                    <a href="{{ route('portal.profile') }}" class="px-2 py-1 rounded hover:bg-white/10">{{ __('portal.nav_profile') }}</a>
                    <form method="POST" action="{{ route('portal.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-2 py-1 rounded hover:bg-white/10">{{ __('portal.logout') }}</button>
                    </form>
                </nav>
            </div>
        </header>
    @endauth

    @if(session('status'))
        <div class="max-w-3xl mx-auto mt-3 px-4">
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-lg px-3 py-2 text-sm">{{ session('status') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-3xl mx-auto mt-3 px-4">
            <div class="bg-rose-100 border border-rose-300 text-rose-900 rounded-lg px-3 py-2 text-sm">
                @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            </div>
        </div>
    @endif

    <main class="max-w-3xl mx-auto px-4 py-4">
        @yield('content')
    </main>

    @auth('customer')
        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $supportPhone) }}?text={{ urlencode($isRtl ? 'مرحباً، أحتاج للمساعدة' : 'Hi, I need help') }}"
           target="_blank" rel="noopener"
           class="fixed bottom-5 end-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full shadow-xl p-4 z-20"
           aria-label="WhatsApp support">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
                <path d="M.057 24l1.687-6.163A11.867 11.867 0 010 11.92C0 5.336 5.335 0 11.913 0a11.821 11.821 0 018.413 3.488 11.823 11.823 0 013.5 8.414c-.003 6.585-5.336 11.916-11.91 11.916a11.9 11.9 0 01-5.69-1.45L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.887-9.881-9.889-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 001.594 5.281l-.999 3.648 3.894-.631zM18.066 14.92c-.075-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.766.967-.94 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.298-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.668-1.611-.916-2.206-.241-.579-.486-.501-.668-.51-.173-.008-.371-.01-.57-.01-.198 0-.521.074-.794.371-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
            </svg>
        </a>
    @endauth
</body>
</html>

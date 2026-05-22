@extends('driver.layout')

@section('content')
<div class="mt-12 bg-white rounded-2xl shadow-lg p-6">
    <div class="text-center mb-6">
        <div class="text-4xl mb-2">🚗</div>
        <h1 class="text-2xl font-bold text-slate-900">{{ __('driver.login_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('driver.login_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('driver.login.submit') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('driver.phone_or_id') }}</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required autofocus
                   class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-amber-500"
                   placeholder="+201234567890" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('driver.password') }}</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-amber-500"
                   placeholder="••••••••" />
        </div>
        <button type="submit"
                class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg py-3 text-base shadow-md transition">
            {{ __('driver.login_button') }}
        </button>
    </form>

    <p class="mt-6 text-xs text-center text-slate-400">
        {{ __('driver.login_help') }}
    </p>
</div>
@endsection

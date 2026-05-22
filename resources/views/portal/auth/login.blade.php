@extends('portal.layout')

@section('content')
<div class="mt-12 max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6">
    <div class="text-center mb-6">
        <div class="text-4xl mb-2">🔐</div>
        <h1 class="text-2xl font-bold">{{ __('portal.login_title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('portal.login_subtitle') }}</p>
    </div>
    <form method="POST" action="{{ route('portal.login.submit') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('portal.email_or_phone') }}</label>
            <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                   class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('portal.password') }}</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base" />
        </div>
        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg py-3 shadow-md">
            {{ __('portal.login_button') }}
        </button>
    </form>
    <p class="mt-6 text-xs text-center text-slate-400">{{ __('portal.login_help') }}</p>
</div>
@endsection

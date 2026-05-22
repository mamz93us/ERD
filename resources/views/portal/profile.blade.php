@extends('portal.layout')

@php $isAr = app()->getLocale() === 'ar'; @endphp

@section('content')
<h1 class="text-xl font-bold mb-4">{{ __('portal.my_profile') }}</h1>

<div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-200">
    <div class="space-y-3 text-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <span class="text-slate-500">{{ __('portal.full_name') }}</span>
            <span class="font-semibold">{{ $isAr ? ($customer->full_name_ar ?? $customer->full_name) : $customer->full_name }}</span>
        </div>
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <span class="text-slate-500">{{ __('portal.phone') }}</span>
            <span class="font-semibold">{{ $customer->phone }}</span>
        </div>
        @if($customer->whatsapp_phone)
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-slate-500">{{ __('portal.whatsapp') }}</span>
                <span class="font-semibold">{{ $customer->whatsapp_phone }}</span>
            </div>
        @endif
        @if($customer->email)
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-slate-500">{{ __('portal.email') }}</span>
                <span class="font-semibold">{{ $customer->email }}</span>
            </div>
        @endif
        @if($customer->corporateAccount)
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-slate-500">{{ __('portal.company') }}</span>
                <span class="font-semibold">{{ $customer->corporateAccount->company_name }}</span>
            </div>
        @endif
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <span class="text-slate-500">{{ __('portal.loyalty_points') }}</span>
            <span class="font-bold text-amber-600">{{ number_format((int) $customer->loyalty_points) }} ⭐</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-slate-500">{{ __('portal.preferred_language') }}</span>
            <span class="font-semibold">{{ $customer->preferred_language?->value === 'ar' ? 'العربية' : 'English' }}</span>
        </div>
    </div>

    <p class="mt-4 text-xs text-slate-400">{{ __('portal.profile_contact_to_edit') }}</p>
</div>
@endsection

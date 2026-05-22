@extends('driver.layout')

@section('content')
    <a href="{{ route('driver.trips.show', $trip->id) }}" class="text-sm text-amber-600 mb-3 inline-block">← {{ __('driver.back_to_trip') }}</a>

    <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-200">
        <h1 class="text-xl font-bold mb-1">{{ __('driver.new_expense') }}</h1>
        <div class="text-xs text-slate-500 mb-4">{{ __('driver.trip_number') }}: {{ $trip->trip_number }}</div>

        <form method="POST" action="{{ route('driver.expenses.store', $trip->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('driver.expense_type') }}</label>
                <select name="type" required class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base">
                    @foreach(\App\Enums\TripExpenseType::cases() as $type)
                        <option value="{{ $type->value }}">{{ $type->getLabel() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('driver.amount') }}</label>
                <div class="relative">
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base" />
                    <span class="absolute end-3 top-3 text-slate-500 text-sm">EGP</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('driver.notes') }}</label>
                <textarea name="notes" rows="2"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-base"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('driver.receipt_photo') }}</label>
                <input type="file" name="receipt" accept="image/*" capture="environment"
                       class="w-full rounded-lg border border-slate-300 px-3 py-3 text-base" />
                <p class="text-xs text-slate-500 mt-1">{{ __('driver.receipt_help') }}</p>
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg py-3 shadow-md">
                {{ __('driver.submit_expense') }}
            </button>
        </form>
    </div>
@endsection

@php
    /** @var \App\Models\Quotation $quotation */
    $isArabic = ($locale ?? 'en') === 'ar';
    $direction = $isArabic ? 'rtl' : 'ltr';
    $appName = $isArabic ? __('app.name', [], 'ar') : __('app.name', [], 'en');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $quotation->quotation_number }}</title>
    <style>
        @page { margin: 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; direction: {{ $direction }}; }
        .header { border-bottom: 2px solid #f59e0b; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 22px; color: #111827; }
        .header .sub { font-size: 11px; color: #6b7280; }
        .meta { width: 100%; margin: 16px 0; border-collapse: collapse; }
        .meta td { padding: 4px 6px; vertical-align: top; }
        .meta .label { color: #6b7280; width: 30%; }
        .meta .value { color: #111827; font-weight: 600; }
        table.lines { width: 100%; border-collapse: collapse; margin: 18px 0; }
        table.lines th, table.lines td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; text-align: {{ $isArabic ? 'right' : 'left' }}; }
        table.lines th { background: #f9fafb; color: #374151; font-weight: 600; }
        .totals { width: 50%; margin-{{ $isArabic ? 'right' : 'left' }}: auto; margin-top: 18px; }
        .totals td { padding: 4px 8px; }
        .totals .row.total td { border-top: 2px solid #111827; font-weight: bold; padding-top: 8px; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #6b7280; }
        .terms { margin-top: 16px; padding: 8px; background: #f9fafb; border-{{ $isArabic ? 'right' : 'left' }}: 3px solid #f59e0b; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $appName }}</h1>
        <div class="sub">{{ $isArabic ? 'عرض سعر' : 'Quotation' }} — {{ $quotation->quotation_number }}</div>
    </div>

    <table class="meta">
        <tr>
            <td class="label">{{ $isArabic ? 'العميل' : 'Customer' }}</td>
            <td class="value">{{ $isArabic ? ($quotation->customer->full_name_ar ?? $quotation->customer->full_name) : $quotation->customer->full_name }}</td>
            <td class="label">{{ $isArabic ? 'تاريخ الإصدار' : 'Issued' }}</td>
            <td class="value">{{ $quotation->created_at?->timezone(config('app.timezone'))->format('Y-m-d') }}</td>
        </tr>
        @if($quotation->corporateAccount)
        <tr>
            <td class="label">{{ $isArabic ? 'الشركة' : 'Company' }}</td>
            <td class="value">{{ $quotation->corporateAccount->company_name }}</td>
            <td class="label">{{ $isArabic ? 'صالح حتى' : 'Valid until' }}</td>
            <td class="value">{{ $quotation->valid_until?->format('Y-m-d') }}</td>
        </tr>
        @else
        <tr>
            <td class="label">{{ $isArabic ? 'صالح حتى' : 'Valid until' }}</td>
            <td class="value" colspan="3">{{ $quotation->valid_until?->format('Y-m-d') }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">{{ $isArabic ? 'فئة السيارة' : 'Car category' }}</td>
            <td class="value">{{ $isArabic ? ($quotation->category->name_ar ?? $quotation->category->name) : $quotation->category->name }}</td>
            <td class="label">{{ $isArabic ? 'المسافة التقديرية' : 'Estimated distance' }}</td>
            <td class="value">{{ number_format($quotation->estimated_distance_km) }} km</td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>{{ $isArabic ? 'من' : 'Pickup' }}</th>
                <th>{{ $isArabic ? 'إلى' : 'Drop-off' }}</th>
                <th>{{ $isArabic ? 'تاريخ البداية' : 'Start' }}</th>
                <th>{{ $isArabic ? 'تاريخ النهاية' : 'End' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $quotation->pickup_location }}</td>
                <td>{{ $quotation->dropoff_location }}</td>
                <td>{{ $quotation->pickup_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                <td>{{ $quotation->dropoff_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>{{ $isArabic ? 'المجموع الفرعي' : 'Subtotal' }}</td>
            <td style="text-align: {{ $isArabic ? 'left' : 'right' }};">EGP {{ number_format((float) $quotation->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>{{ $isArabic ? 'ضريبة القيمة المضافة' : 'VAT' }} ({{ number_format(((float) config('billing.vat_rate', 0.14)) * 100, 0) }}%)</td>
            <td style="text-align: {{ $isArabic ? 'left' : 'right' }};">EGP {{ number_format((float) $quotation->vat_amount, 2) }}</td>
        </tr>
        <tr class="row total">
            <td>{{ $isArabic ? 'الإجمالي' : 'Total' }}</td>
            <td style="text-align: {{ $isArabic ? 'left' : 'right' }};">EGP {{ number_format((float) $quotation->total_amount, 2) }}</td>
        </tr>
    </table>

    @if($quotation->terms_and_conditions)
        <div class="terms">{{ $quotation->terms_and_conditions }}</div>
    @endif

    <div class="footer">
        {{ $isArabic ? 'تم إنشاء هذا المستند بواسطة نظام مجموعة عدلي.' : 'This document was generated by Adly Group Agency CRMS.' }}
    </div>
</body>
</html>

@php
    /** @var \App\Models\Invoice $invoice */
    $isArabic = ($locale ?? 'en') === 'ar';
    $direction = $isArabic ? 'rtl' : 'ltr';
    $appName = $isArabic ? 'مجموعة عدلي' : 'Adly Group Agency';
    $align = $isArabic ? 'right' : 'left';
    $oppAlign = $isArabic ? 'left' : 'right';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; direction: {{ $direction }}; }
        .header { border-bottom: 2px solid #f59e0b; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 22px; color: #111827; }
        .header .sub { font-size: 11px; color: #6b7280; }
        .header .doc-no { float: {{ $oppAlign }}; font-size: 14px; font-weight: bold; color: #111827; }
        .status-pill { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; color: #ffffff; }
        .meta { width: 100%; margin: 16px 0; border-collapse: collapse; }
        .meta td { padding: 4px 6px; vertical-align: top; }
        .meta .label { color: #6b7280; width: 18%; }
        .meta .value { color: #111827; font-weight: 600; width: 32%; }
        table.lines { width: 100%; border-collapse: collapse; margin: 18px 0; }
        table.lines th, table.lines td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; text-align: {{ $align }}; }
        table.lines th { background: #f9fafb; color: #374151; font-weight: 600; font-size: 11px; }
        table.lines td.num { text-align: {{ $oppAlign }}; white-space: nowrap; }
        .totals { width: 50%; margin-{{ $isArabic ? 'right' : 'left' }}: auto; margin-top: 18px; border-collapse: collapse; }
        .totals td { padding: 4px 8px; }
        .totals td.amount { text-align: {{ $oppAlign }}; white-space: nowrap; }
        .totals .row.subtle td { color: #6b7280; }
        .totals .row.total td { border-top: 2px solid #111827; font-weight: bold; padding-top: 8px; font-size: 14px; color: #111827; }
        .totals .row.balance td { color: #b91c1c; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #6b7280; }
        .terms { margin-top: 16px; padding: 8px; background: #f9fafb; border-{{ $isArabic ? 'right' : 'left' }}: 3px solid #f59e0b; white-space: pre-wrap; font-size: 10px; }
        .notes { margin-top: 8px; font-size: 10px; color: #4b5563; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <span class="doc-no">{{ $invoice->invoice_number }}</span>
        <h1>{{ $appName }}</h1>
        <div class="sub">
            {{ $isArabic ? 'فاتورة ضريبية' : 'Tax Invoice' }}
            @php
                $statusLabel = $invoice->status?->getLabel();
                $statusColor = match($invoice->status?->value) {
                    'draft' => '#64748b',
                    'sent' => '#0284c7',
                    'partially_paid' => '#f59e0b',
                    'paid' => '#059669',
                    'overdue' => '#e11d48',
                    'cancelled' => '#6b7280',
                    default => '#6b7280',
                };
            @endphp
            &nbsp;&nbsp;<span class="status-pill" style="background: {{ $statusColor }};">{{ $statusLabel }}</span>
        </div>
    </div>

    <table class="meta">
        <tr>
            <td class="label">{{ $isArabic ? 'العميل' : 'Customer' }}</td>
            <td class="value">{{ $isArabic ? ($invoice->customer->full_name_ar ?? $invoice->customer->full_name) : $invoice->customer->full_name }}</td>
            <td class="label">{{ $isArabic ? 'تاريخ الإصدار' : 'Issue date' }}</td>
            <td class="value">{{ \Illuminate\Support\Carbon::parse($invoice->issue_date)->format('Y-m-d') }}</td>
        </tr>
        @if($invoice->corporateAccount)
        <tr>
            <td class="label">{{ $isArabic ? 'الشركة' : 'Company' }}</td>
            <td class="value">{{ $isArabic ? ($invoice->corporateAccount->company_name_ar ?? $invoice->corporateAccount->company_name) : $invoice->corporateAccount->company_name }}</td>
            <td class="label">{{ $isArabic ? 'تاريخ الاستحقاق' : 'Due date' }}</td>
            <td class="value">{{ \Illuminate\Support\Carbon::parse($invoice->due_date)->format('Y-m-d') }}</td>
        </tr>
        @if($invoice->corporateAccount->tax_id)
        <tr>
            <td class="label">{{ $isArabic ? 'الرقم الضريبي' : 'Tax ID' }}</td>
            <td class="value" colspan="3">{{ $invoice->corporateAccount->tax_id }}</td>
        </tr>
        @endif
        @else
        <tr>
            <td class="label">{{ $isArabic ? 'الهاتف' : 'Phone' }}</td>
            <td class="value">{{ $invoice->customer->phone ?? '—' }}</td>
            <td class="label">{{ $isArabic ? 'تاريخ الاستحقاق' : 'Due date' }}</td>
            <td class="value">{{ \Illuminate\Support\Carbon::parse($invoice->due_date)->format('Y-m-d') }}</td>
        </tr>
        @endif
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ $isArabic ? 'الوصف' : 'Description' }}</th>
                <th class="num">{{ $isArabic ? 'الكمية' : 'Qty' }}</th>
                <th class="num">{{ $isArabic ? 'سعر الوحدة' : 'Unit price' }}</th>
                <th class="num">{{ $isArabic ? 'الضريبة' : 'VAT' }}</th>
                <th class="num">{{ $isArabic ? 'الإجمالي' : 'Total' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $i => $line)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $line->description }}</td>
                <td class="num">{{ number_format((float) $line->quantity, 2) }}</td>
                <td class="num">{{ number_format((float) $line->unit_price, 2) }}</td>
                <td class="num">{{ number_format((float) $line->vat_amount, 2) }}</td>
                <td class="num">{{ number_format((float) $line->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr class="row subtle">
            <td>{{ $isArabic ? 'المجموع الفرعي' : 'Subtotal' }}</td>
            <td class="amount">EGP {{ number_format((float) $invoice->subtotal, 2) }}</td>
        </tr>
        @if(bccomp((string) $invoice->discount_amount, '0.00', 2) > 0)
        <tr class="row subtle">
            <td>{{ $isArabic ? 'الخصم' : 'Discount' }}</td>
            <td class="amount">− EGP {{ number_format((float) $invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="row subtle">
            <td>{{ $isArabic ? 'ضريبة القيمة المضافة' : 'VAT' }} (14%)</td>
            <td class="amount">EGP {{ number_format((float) $invoice->vat_amount, 2) }}</td>
        </tr>
        <tr class="row total">
            <td>{{ $isArabic ? 'الإجمالي' : 'Total' }}</td>
            <td class="amount">EGP {{ number_format((float) $invoice->total, 2) }}</td>
        </tr>
        @if(bccomp((string) $invoice->paid_amount, '0.00', 2) > 0)
        <tr class="row subtle">
            <td>{{ $isArabic ? 'المدفوع' : 'Paid' }}</td>
            <td class="amount">EGP {{ number_format((float) $invoice->paid_amount, 2) }}</td>
        </tr>
        <tr class="row balance">
            <td>{{ $isArabic ? 'الرصيد المستحق' : 'Balance due' }}</td>
            <td class="amount">EGP {{ number_format((float) $invoice->balance_due, 2) }}</td>
        </tr>
        @endif
    </table>

    @if($invoice->terms)
        <div class="terms">{{ $invoice->terms }}</div>
    @endif

    @if($invoice->notes)
        <div class="notes">{{ $invoice->notes }}</div>
    @endif

    <div class="footer">
        {{ $isArabic
            ? 'تم إنشاء هذه الفاتورة بواسطة نظام مجموعة عدلي — هذا مستند ساري المفعول بدون توقيع.'
            : 'This invoice was generated by Adly Group Agency CRMS — valid without a signature.' }}
    </div>
</body>
</html>

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Quotations;

use App\Filament\Admin\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\Admin\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Admin\Resources\Quotations\Pages\ListQuotations;
use App\Filament\Admin\Resources\Quotations\Schemas\QuotationForm;
use App\Filament\Admin\Resources\Quotations\Tables\QuotationsTable;
use App\Models\Customer;
use App\Models\Quotation;
use App\Services\Pricing\PricingService;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    public static function getNavigationLabel(): string
    {
        return __('navigation.quotations');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.pricing');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.quotations');
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    /**
     * Resolve rate_card_id, subtotal, vat_amount, total_amount via PricingService
     * before persisting. Called from Create/Edit page lifecycle hooks so users
     * never need to touch these fields directly — they always reflect the source-of-truth math.
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    public static function applyPricing(array $data): array
    {
        if (empty($data['category_id']) || empty($data['pickup_at']) || empty($data['dropoff_at'])) {
            return $data;
        }

        $corporateId = $data['corporate_account_id']
            ?? Customer::query()->whereKey($data['customer_id'] ?? null)->value('corporate_account_id');

        // Auto-detect inter-city trips from the governorate selects so PricingService
        // adds the rate card's cross_city_surcharge when pickup ≠ dropoff governorate.
        $surchargeFlags = [];
        if (filled($data['pickup_location'] ?? null)
            && filled($data['dropoff_location'] ?? null)
            && $data['pickup_location'] !== $data['dropoff_location']) {
            $surchargeFlags[] = 'cross_city';
        }

        try {
            $result = app(PricingService::class)->calculate(
                categoryId: $data['category_id'],
                corporateAccountId: $corporateId,
                start: CarbonImmutable::parse($data['pickup_at']),
                end: CarbonImmutable::parse($data['dropoff_at']),
                estimatedKm: (int) ($data['estimated_distance_km'] ?? 0),
                surchargeFlags: $surchargeFlags,
            );
        } catch (\Throwable) {
            return $data;
        }

        $data['rate_card_id'] = $result->rateCardId;
        $data['subtotal'] = $result->subtotal;
        $data['vat_amount'] = $result->vatAmount;
        $data['total_amount'] = $result->total;

        return $data;
    }
}

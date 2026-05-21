<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Quotations\Pages;

use App\Filament\Admin\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    /**
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();

        return QuotationResource::applyPricing($data);
    }
}

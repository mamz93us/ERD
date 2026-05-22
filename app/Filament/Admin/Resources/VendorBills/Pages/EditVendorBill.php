<?php

namespace App\Filament\Admin\Resources\VendorBills\Pages;

use App\Filament\Admin\Resources\VendorBills\VendorBillResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorBill extends EditRecord
{
    protected static string $resource = VendorBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

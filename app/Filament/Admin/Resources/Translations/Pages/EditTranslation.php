<?php

namespace App\Filament\Admin\Resources\Translations\Pages;

use App\Filament\Admin\Resources\Translations\TranslationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTranslation extends EditRecord
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

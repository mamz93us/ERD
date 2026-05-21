<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Translations\Tables;

use App\Models\Translation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TranslationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->label(__('translations.group'))
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('key')
                    ->label(__('translations.key'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('text_ar')
                    ->label(__('translations.text_ar'))
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('text_en')
                    ->label(__('translations.text_en'))
                    ->limit(60)
                    ->wrap(),
                IconColumn::make('is_system')
                    ->label(__('translations.is_system'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(fn () => Translation::query()->distinct()->pluck('group', 'group')->toArray()),
                SelectFilter::make('is_system')
                    ->label(__('translations.is_system'))
                    ->options(['1' => __('common.yes'), '0' => __('common.no')]),
            ])
            ->defaultSort('group')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') ?? false),
                ]),
            ]);
    }
}

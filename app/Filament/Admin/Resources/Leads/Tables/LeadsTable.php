<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Leads\Tables;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('customer.full_name')
                    ->label(__('leads.customer'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('source')
                    ->label(__('leads.source'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('leads.status'))
                    ->badge(),
                TextColumn::make('estimated_value')
                    ->label(__('leads.estimated_value'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('assignedUser.full_name')
                    ->label(__('leads.assigned_user'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('due_at')
                    ->label(__('leads.due_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options(LeadStatus::class),
                SelectFilter::make('source')->options(LeadSource::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

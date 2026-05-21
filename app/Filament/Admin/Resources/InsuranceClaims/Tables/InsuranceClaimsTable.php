<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InsuranceClaims\Tables;

use App\Enums\InsuranceClaimStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InsuranceClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('incident_date', 'desc')
            ->columns([
                TextColumn::make('claim_number')
                    ->label(__('insurance_claims.claim_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('car.plate')
                    ->label(__('insurance_claims.car'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('trip.trip_number')
                    ->label(__('insurance_claims.trip'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('incident_date')
                    ->label(__('insurance_claims.incident_date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('incident_location')
                    ->label(__('insurance_claims.incident_location'))
                    ->toggleable(),
                TextColumn::make('claim_amount')
                    ->label(__('insurance_claims.claim_amount'))
                    ->money('EGP'),
                TextColumn::make('payout_amount')
                    ->label(__('insurance_claims.payout_amount'))
                    ->money('EGP')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label(__('insurance_claims.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(InsuranceClaimStatus::class),
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

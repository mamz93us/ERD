<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InsuranceClaims\Schemas;

use App\Enums\InsuranceClaimStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InsuranceClaimForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('car_id')
                ->label(__('insurance_claims.car'))
                ->relationship('car', 'plate')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('trip_id')
                ->label(__('insurance_claims.trip'))
                ->relationship('trip', 'trip_number')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('claim_number')
                ->label(__('insurance_claims.claim_number'))
                ->required()
                ->unique(ignoreRecord: true),
            DateTimePicker::make('incident_date')
                ->label(__('insurance_claims.incident_date'))
                ->required()
                ->seconds(false),
            TextInput::make('incident_location')
                ->label(__('insurance_claims.incident_location')),
            TextInput::make('police_report_number')
                ->label(__('insurance_claims.police_report_number')),
            TextInput::make('claim_amount')
                ->label(__('insurance_claims.claim_amount'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            TextInput::make('payout_amount')
                ->label(__('insurance_claims.payout_amount'))
                ->numeric()
                ->prefix('EGP'),
            Select::make('status')
                ->label(__('insurance_claims.status'))
                ->options(InsuranceClaimStatus::class)
                ->default(InsuranceClaimStatus::Reported->value)
                ->required(),
            FileUpload::make('documents')
                ->label(__('insurance_claims.documents'))
                ->multiple()
                ->disk('public')
                ->directory('insurance-claims')
                ->columnSpanFull(),
            Textarea::make('description')
                ->label(__('insurance_claims.description'))
                ->rows(4)
                ->required()
                ->columnSpanFull(),
            Textarea::make('notes')
                ->label(__('insurance_claims.notes'))
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrafficFines\Schemas;

use App\Enums\TrafficFinePaymentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrafficFineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('car_id')
                ->label(__('traffic_fines.car'))
                ->relationship('car', 'plate')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('violation_number')
                ->label(__('traffic_fines.violation_number'))
                ->required()
                ->unique(ignoreRecord: true),
            DateTimePicker::make('violation_date')
                ->label(__('traffic_fines.violation_date'))
                ->required()
                ->seconds(false)
                ->helperText(__('traffic_fines.violation_date_help')),
            TextInput::make('violation_type')
                ->label(__('traffic_fines.violation_type'))
                ->required(),
            TextInput::make('location')
                ->label(__('traffic_fines.location')),
            TextInput::make('amount')
                ->label(__('traffic_fines.amount'))
                ->numeric()
                ->prefix('EGP')
                ->required(),
            Select::make('trip_id')
                ->label(__('traffic_fines.trip'))
                ->relationship('trip', 'trip_number')
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText(__('traffic_fines.trip_help')),
            Select::make('driver_id')
                ->label(__('traffic_fines.driver'))
                ->relationship('driver', 'full_name')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('payment_status')
                ->label(__('traffic_fines.payment_status'))
                ->options(TrafficFinePaymentStatus::class)
                ->default(TrafficFinePaymentStatus::Unpaid->value)
                ->live()
                ->required(),
            DatePicker::make('paid_date')
                ->label(__('traffic_fines.paid_date'))
                ->visible(fn ($get) => $get('payment_status') === TrafficFinePaymentStatus::Paid->value),
            TextInput::make('paid_amount')
                ->label(__('traffic_fines.paid_amount'))
                ->numeric()
                ->prefix('EGP')
                ->visible(fn ($get) => $get('payment_status') === TrafficFinePaymentStatus::Paid->value),
            Toggle::make('deducted_from_driver')
                ->label(__('traffic_fines.deducted_from_driver'))
                ->helperText(__('traffic_fines.deducted_from_driver_help'))
                ->default(false),
            FileUpload::make('attachment_path')
                ->label(__('traffic_fines.attachment'))
                ->disk('public')
                ->directory('traffic-fines'),
            Textarea::make('notes')
                ->label(__('traffic_fines.notes'))
                ->rows(3)
                ->columnSpanFull(),
            Placeholder::make('attribution_note')
                ->label('')
                ->content(__('traffic_fines.attribution_note'))
                ->columnSpanFull()
                ->visible(fn ($record) => $record === null),
        ]);
    }
}

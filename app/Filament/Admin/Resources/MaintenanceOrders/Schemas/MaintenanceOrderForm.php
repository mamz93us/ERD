<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MaintenanceOrders\Schemas;

use App\Enums\MaintenanceOrderStatus;
use App\Enums\MaintenanceOrderType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MaintenanceOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('car_id')
                ->label(__('maintenance_orders.car'))
                ->relationship('car', 'plate')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('garage_id')
                ->label(__('maintenance_orders.garage'))
                ->relationship('garage', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('order_type')
                ->label(__('maintenance_orders.order_type'))
                ->options(MaintenanceOrderType::class)
                ->default(MaintenanceOrderType::Preventive->value)
                ->required(),
            Select::make('status')
                ->label(__('maintenance_orders.status'))
                ->options(MaintenanceOrderStatus::class)
                ->default(MaintenanceOrderStatus::Scheduled->value)
                ->required()
                ->helperText(__('maintenance_orders.status_help')),
            DateTimePicker::make('scheduled_start')
                ->label(__('maintenance_orders.scheduled_start'))
                ->required()
                ->seconds(false),
            DateTimePicker::make('scheduled_end')
                ->label(__('maintenance_orders.scheduled_end'))
                ->required()
                ->after('scheduled_start')
                ->seconds(false),
            DateTimePicker::make('actual_start')
                ->label(__('maintenance_orders.actual_start'))
                ->seconds(false),
            DateTimePicker::make('actual_end')
                ->label(__('maintenance_orders.actual_end'))
                ->seconds(false),
            TextInput::make('odometer_at_service')
                ->label(__('maintenance_orders.odometer_at_service'))
                ->numeric()
                ->suffix(' km'),
            TextInput::make('subtotal')
                ->label(__('maintenance_orders.subtotal'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('vat_amount')
                ->label(__('maintenance_orders.vat_amount'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            TextInput::make('total_cost')
                ->label(__('maintenance_orders.total_cost'))
                ->numeric()
                ->prefix('EGP')
                ->default(0),
            FileUpload::make('invoice_file_path')
                ->label(__('maintenance_orders.invoice_file'))
                ->disk('public')
                ->directory('maintenance-invoices'),
            Textarea::make('description')
                ->label(__('maintenance_orders.description'))
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('notes')
                ->label(__('maintenance_orders.notes'))
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }
}

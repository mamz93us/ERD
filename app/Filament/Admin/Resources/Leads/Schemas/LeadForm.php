<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Leads\Schemas;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('customer_id')
                ->label(__('leads.customer'))
                ->relationship('customer', 'full_name')
                ->searchable()
                ->nullable(),
            Select::make('assigned_user_id')
                ->label(__('leads.assigned_user'))
                ->relationship('assignedUser', 'full_name')
                ->searchable()
                ->nullable(),
            Select::make('source')
                ->label(__('leads.source'))
                ->options(LeadSource::class)
                ->required(),
            Select::make('status')
                ->label(__('leads.status'))
                ->options(LeadStatus::class)
                ->default(LeadStatus::New_->value)
                ->live()
                ->required(),
            Textarea::make('requirements')
                ->label(__('leads.requirements'))
                ->rows(3),
            TextInput::make('estimated_value')
                ->label(__('leads.estimated_value'))
                ->numeric()
                ->prefix('EGP')
                ->default(0)
                ->required(),
            DateTimePicker::make('due_at')
                ->label(__('leads.due_at'))
                ->seconds(false),
            TextInput::make('lost_reason')
                ->label(__('leads.lost_reason'))
                ->visible(fn ($get) => $get('status') === LeadStatus::Lost->value),
            DateTimePicker::make('closed_at')
                ->label(__('leads.closed_at'))
                ->visible(fn ($get) => in_array($get('status'), [LeadStatus::Won->value, LeadStatus::Lost->value], true))
                ->seconds(false),
        ]);
    }
}

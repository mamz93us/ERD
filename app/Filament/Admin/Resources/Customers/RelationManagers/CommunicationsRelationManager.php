<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\RelationManagers;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommunicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'communications';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('channel')
                ->label(__('customer_communications.channel'))
                ->options(CommunicationChannel::class)
                ->required(),
            Select::make('direction')
                ->label(__('customer_communications.direction'))
                ->options(CommunicationDirection::class)
                ->required(),
            TextInput::make('subject')
                ->label(__('customer_communications.subject'))
                ->maxLength(255),
            Textarea::make('body')
                ->label(__('customer_communications.body'))
                ->rows(4)
                ->required(),
            DateTimePicker::make('sent_at')
                ->label(__('customer_communications.sent_at'))
                ->default(now())
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->defaultSort('sent_at', 'desc')
            ->columns([
                TextColumn::make('sent_at')
                    ->label(__('customer_communications.sent_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('channel')
                    ->label(__('customer_communications.channel'))
                    ->badge(),
                TextColumn::make('direction')
                    ->label(__('customer_communications.direction'))
                    ->badge(),
                TextColumn::make('subject')
                    ->label(__('customer_communications.subject'))
                    ->searchable(),
                TextColumn::make('body')
                    ->label(__('customer_communications.body'))
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('user.full_name')
                    ->label(__('customer_communications.logged_by'))
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('channel')->options(CommunicationChannel::class),
                SelectFilter::make('direction')->options(CommunicationDirection::class),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

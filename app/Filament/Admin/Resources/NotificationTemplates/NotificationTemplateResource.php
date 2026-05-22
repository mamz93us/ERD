<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\NotificationTemplates;

use App\Filament\Admin\Resources\NotificationTemplates\Pages\CreateNotificationTemplate;
use App\Filament\Admin\Resources\NotificationTemplates\Pages\EditNotificationTemplate;
use App\Filament\Admin\Resources\NotificationTemplates\Pages\ListNotificationTemplates;
use App\Models\NotificationTemplate;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    public static function getNavigationLabel(): string
    {
        return __('navigation.notification_templates');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.notification_templates');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')
                ->label(__('notification_templates.key'))
                ->required()
                ->maxLength(64)
                ->helperText(__('notification_templates.key_help')),
            Select::make('channel')
                ->label(__('notification_templates.channel'))
                ->options(['whatsapp' => 'WhatsApp', 'mail' => 'Email'])
                ->required(),
            Select::make('locale')
                ->label(__('notification_templates.locale'))
                ->options(['ar' => 'العربية', 'en' => 'English'])
                ->required(),
            TextInput::make('subject')
                ->label(__('notification_templates.subject'))
                ->maxLength(255)
                ->helperText(__('notification_templates.subject_help'))
                ->columnSpanFull(),
            Textarea::make('body')
                ->label(__('notification_templates.body'))
                ->required()
                ->rows(8)
                ->helperText(__('notification_templates.body_help'))
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label(__('notification_templates.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('key')
            ->columns([
                TextColumn::make('key')
                    ->label(__('notification_templates.key'))
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('channel')
                    ->label(__('notification_templates.channel'))
                    ->badge(),
                TextColumn::make('locale')
                    ->label(__('notification_templates.locale'))
                    ->badge(),
                TextColumn::make('subject')
                    ->label(__('notification_templates.subject'))
                    ->limit(40)
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('notification_templates.is_active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('notification_templates.updated_at'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('channel')->options(['whatsapp' => 'WhatsApp', 'mail' => 'Email']),
                SelectFilter::make('locale')->options(['ar' => 'العربية', 'en' => 'English']),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationTemplates::route('/'),
            'create' => CreateNotificationTemplate::route('/create'),
            'edit' => EditNotificationTemplate::route('/{record}/edit'),
        ];
    }
}

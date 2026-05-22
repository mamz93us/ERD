<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\SystemSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Phase 15: single-page settings editor.
 *
 *   - Branding: system name (ar + en), logo upload
 *   - Mail (SMTP): host, port, username, password, encryption, from
 *   - WhatsApp (Green API): instance_id, token
 *
 * All values land in system_settings via SystemSetting::put(). Secret
 * keys are auto-encrypted (mail.password + whatsapp.token, declared in
 * SystemSetting::ENCRYPTED_KEYS). The change is reflected on the very
 * next request — AppServiceProvider::applySystemSettings() runs on
 * every boot.
 */
class SystemSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.admin.pages.system-settings';

    /** @var array<string, mixed> */
    public array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('navigation.system_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.settings');
    }

    public function getTitle(): string
    {
        return __('navigation.system_settings');
    }

    public function mount(): void
    {
        $this->data = [
            'system_name' => SystemSetting::get('system.name', config('app.name')),
            'system_name_ar' => SystemSetting::get('system.name_ar', 'مجموعة عدلي'),
            'logo_path' => SystemSetting::get('system.logo_path'),

            'mail_host' => SystemSetting::get('mail.host', config('mail.mailers.smtp.host')),
            'mail_port' => (string) SystemSetting::get('mail.port', config('mail.mailers.smtp.port') ?? 587),
            'mail_username' => SystemSetting::get('mail.username', config('mail.mailers.smtp.username')),
            'mail_password' => SystemSetting::get('mail.password'),
            'mail_encryption' => SystemSetting::get('mail.encryption', 'tls'),
            'mail_from_address' => SystemSetting::get('mail.from_address', config('mail.from.address')),
            'mail_from_name' => SystemSetting::get('mail.from_name', config('mail.from.name')),

            'whatsapp_instance_id' => SystemSetting::get('whatsapp.instance_id', config('services.green_api.instance_id')),
            'whatsapp_token' => SystemSetting::get('whatsapp.token'),
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make(__('system_settings.branding'))
                    ->description(__('system_settings.branding_help'))
                    ->schema([
                        TextInput::make('system_name')
                            ->label(__('system_settings.system_name'))
                            ->required()
                            ->maxLength(120),
                        TextInput::make('system_name_ar')
                            ->label(__('system_settings.system_name_ar'))
                            ->maxLength(120),
                        FileUpload::make('logo_path')
                            ->label(__('system_settings.logo'))
                            ->image()
                            ->disk('public')
                            ->directory('system')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make(__('system_settings.mail_title'))
                    ->description(__('system_settings.mail_help'))
                    ->schema([
                        TextInput::make('mail_host')
                            ->label(__('system_settings.mail_host'))
                            ->placeholder('email-smtp.eu-west-1.amazonaws.com')
                            ->maxLength(180),
                        TextInput::make('mail_port')
                            ->label(__('system_settings.mail_port'))
                            ->numeric()
                            ->default(587),
                        TextInput::make('mail_username')
                            ->label(__('system_settings.mail_username'))
                            ->maxLength(180),
                        TextInput::make('mail_password')
                            ->label(__('system_settings.mail_password'))
                            ->password()
                            ->revealable()
                            ->helperText(__('system_settings.mail_password_help'))
                            ->maxLength(255),
                        TextInput::make('mail_encryption')
                            ->label(__('system_settings.mail_encryption'))
                            ->placeholder('tls'),
                        TextInput::make('mail_from_address')
                            ->label(__('system_settings.mail_from_address'))
                            ->email(),
                        TextInput::make('mail_from_name')
                            ->label(__('system_settings.mail_from_name'))
                            ->maxLength(180),
                    ])
                    ->columns(2),

                Section::make(__('system_settings.whatsapp_title'))
                    ->description(__('system_settings.whatsapp_help'))
                    ->schema([
                        TextInput::make('whatsapp_instance_id')
                            ->label(__('system_settings.whatsapp_instance_id'))
                            ->placeholder('1101000001'),
                        TextInput::make('whatsapp_token')
                            ->label(__('system_settings.whatsapp_token'))
                            ->password()
                            ->revealable()
                            ->helperText(__('system_settings.whatsapp_token_help'))
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $map = [
            'system.name' => $data['system_name'] ?? null,
            'system.name_ar' => $data['system_name_ar'] ?? null,
            'system.logo_path' => $data['logo_path'] ?? null,

            'mail.host' => $data['mail_host'] ?? null,
            'mail.port' => $data['mail_port'] ?? null,
            'mail.username' => $data['mail_username'] ?? null,
            'mail.password' => $data['mail_password'] ?? null,
            'mail.encryption' => $data['mail_encryption'] ?? null,
            'mail.from_address' => $data['mail_from_address'] ?? null,
            'mail.from_name' => $data['mail_from_name'] ?? null,

            'whatsapp.instance_id' => $data['whatsapp_instance_id'] ?? null,
            'whatsapp.token' => $data['whatsapp_token'] ?? null,
        ];

        foreach ($map as $key => $value) {
            SystemSetting::put($key, $value);
        }

        Notification::make()
            ->title(__('system_settings.saved'))
            ->success()
            ->send();
    }

    /** @return array<int, Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('system_settings.save'))
                ->submit('save'),
        ];
    }
}

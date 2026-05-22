<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\DriverLeaderboardWidget;
use App\Filament\Admin\Widgets\ExpiringDocumentsWidget;
use App\Filament\Admin\Widgets\OperationsStatsWidget;
use App\Filament\Admin\Widgets\OutstandingReceivablesWidget;
use App\Filament\Admin\Widgets\RevenueComparisonWidget;
use App\Http\Middleware\SetLocale;
use App\Models\SystemSetting;
use Filament\Auth\Pages\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName(function () {
                $key = app()->getLocale() === 'ar' ? 'system.name_ar' : 'system.name';
                $fallback = app()->getLocale() === 'ar' ? 'مجموعة عدلي' : 'Adly Group Agency';

                return SystemSetting::get($key, $fallback);
            })
            ->brandLogo(function () {
                $logo = SystemSetting::get('system.logo_path');

                return $logo ? asset('storage/'.ltrim($logo, '/')) : null;
            })
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountWidget::class,
                OperationsStatsWidget::class,
                RevenueComparisonWidget::class,
                ExpiringDocumentsWidget::class,
                OutstandingReceivablesWidget::class,
                DriverLeaderboardWidget::class,
            ])
            ->userMenuItems([
                'locale' => MenuItem::make()
                    ->label(fn () => app()->getLocale() === 'ar' ? 'English' : 'العربية')
                    ->icon('heroicon-o-language')
                    ->url(fn () => request()->fullUrlWithQuery([
                        'locale' => app()->getLocale() === 'ar' ? 'en' : 'ar',
                    ])),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

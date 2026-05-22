<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\CarDocument;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\MaintenanceOrder;
use App\Models\MaintenanceSchedule;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\SystemSetting;
use App\Models\TrafficFine;
use App\Models\Trip;
use App\Models\VendorBill;
use App\Notifications\Channels\WhatsappChannel;
use App\Observers\CarDocumentObserver;
use App\Observers\CreditNoteNumberObserver;
use App\Observers\InvoiceNumberObserver;
use App\Observers\MaintenanceOrderObserver;
use App\Observers\MaintenanceScheduleObserver;
use App\Observers\PaymentNumberObserver;
use App\Observers\QuotationNumberObserver;
use App\Observers\TrafficFineObserver;
use App\Observers\TripNumberObserver;
use App\Observers\VendorBillNumberObserver;
use App\Services\Notifications\WhatsappService;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WhatsappService::class, fn () => WhatsappService::fromConfig());
    }

    public function boot(): void
    {
        if ($this->app->environment() !== 'local' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        $this->app->make(ChannelManager::class)
            ->extend('whatsapp', fn ($app) => $app->make(WhatsappChannel::class));

        // Phase 15: pull mail + green-api creds from system_settings if set.
        // Operator can flip these in admin without redeploy / .env edit.
        $this->applySystemSettings();

        CarDocument::observe(CarDocumentObserver::class);
        Quotation::observe(QuotationNumberObserver::class);
        Trip::observe(TripNumberObserver::class);
        MaintenanceOrder::observe(MaintenanceOrderObserver::class);
        MaintenanceSchedule::observe(MaintenanceScheduleObserver::class);
        TrafficFine::observe(TrafficFineObserver::class);
        Invoice::observe(InvoiceNumberObserver::class);
        CreditNote::observe(CreditNoteNumberObserver::class);
        Payment::observe(PaymentNumberObserver::class);
        VendorBill::observe(VendorBillNumberObserver::class);
    }

    /**
     * Overlay system_settings values onto runtime config. Skipped on the
     * very first install (before the migration runs) by catching the
     * "missing table" exception.
     */
    private function applySystemSettings(): void
    {
        try {
            $get = SystemSetting::get(...);
        } catch (\Throwable) {
            return;
        }

        if ($name = $get('system.name')) {
            config(['app.name' => $name]);
        }

        $mailHost = $get('mail.host');
        if (filled($mailHost)) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $mailHost,
                'mail.mailers.smtp.port' => (int) ($get('mail.port') ?? 587),
                'mail.mailers.smtp.username' => $get('mail.username'),
                'mail.mailers.smtp.password' => $get('mail.password'),
                'mail.mailers.smtp.encryption' => $get('mail.encryption') ?? 'tls',
                'mail.from.address' => $get('mail.from_address') ?? config('mail.from.address'),
                'mail.from.name' => $get('mail.from_name') ?? config('mail.from.name'),
            ]);
        }

        $waInstance = $get('whatsapp.instance_id');
        $waToken = $get('whatsapp.token');
        if (filled($waInstance) && filled($waToken)) {
            config([
                'services.green_api.instance_id' => $waInstance,
                'services.green_api.token' => $waToken,
            ]);
            // Re-bind WhatsappService so it picks up the fresh creds.
            $this->app->singleton(WhatsappService::class, fn () => WhatsappService::fromConfig());
        }
    }
}

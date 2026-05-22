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
}

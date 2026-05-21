<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\CarDocument;
use App\Models\Quotation;
use App\Models\Trip;
use App\Observers\CarDocumentObserver;
use App\Observers\QuotationNumberObserver;
use App\Observers\TripNumberObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        CarDocument::observe(CarDocumentObserver::class);
        Quotation::observe(QuotationNumberObserver::class);
        Trip::observe(TripNumberObserver::class);
    }
}

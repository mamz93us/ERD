<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CarStatus;
use App\Models\CarDocument;
use App\Models\DriverDocument;
use App\Models\User;
use App\Notifications\DocumentExpired;
use App\Notifications\DocumentExpiringSoon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class CheckCarDocumentExpiry extends Command
{
    /** Days-until-expiry windows that trigger a DocumentExpiringSoon notification (per spec §9.3). */
    private const WARN_WINDOWS = [60, 30, 7, 1, 0];

    protected $signature = 'documents:check-expiry';

    protected $description = 'Notify on car/driver documents nearing expiry (60/30/7/1/0 days) and out-of-service expired ones';

    public function handle(): int
    {
        $today = Carbon::now()->startOfDay();
        $warned = 0;
        $expired = 0;

        // Active car documents
        CarDocument::query()
            ->with('car.branch')
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->cursor()
            ->each(function (CarDocument $doc) use ($today, &$warned, &$expired): void {
                $days = (int) $today->copy()->diffInDays($doc->expiry_date->startOfDay(), false);

                if ($days < 0) {
                    if ($doc->car && $doc->car->status !== CarStatus::OutOfService) {
                        $doc->car->status = CarStatus::OutOfService;
                        $doc->car->save();
                    }
                    Notification::send($this->recipientsForCar($doc), new DocumentExpired($doc));
                    $expired++;

                    return;
                }

                if (in_array($days, self::WARN_WINDOWS, true)) {
                    Notification::send($this->recipientsForCar($doc), new DocumentExpiringSoon($doc, $days));
                    $warned++;
                }
            });

        // Active driver documents (notify fleet_manager + branch_manager of driver's branch)
        DriverDocument::query()
            ->with('driver.branch')
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->cursor()
            ->each(function (DriverDocument $doc) use ($today, &$warned, &$expired): void {
                $days = (int) $today->copy()->diffInDays($doc->expiry_date->startOfDay(), false);

                if ($days < 0) {
                    Notification::send($this->recipientsForDriver($doc), new DocumentExpired($doc));
                    $expired++;

                    return;
                }

                if (in_array($days, self::WARN_WINDOWS, true)) {
                    Notification::send($this->recipientsForDriver($doc), new DocumentExpiringSoon($doc, $days));
                    $warned++;
                }
            });

        $this->info("Document expiry sweep: {$warned} warnings, {$expired} expired");

        return self::SUCCESS;
    }

    /** @return Collection<int,User> */
    private function recipientsForCar(CarDocument $doc): Collection
    {
        return $this->recipientsForBranch($doc->car?->branch_id);
    }

    /** @return Collection<int,User> */
    private function recipientsForDriver(DriverDocument $doc): Collection
    {
        return $this->recipientsForBranch($doc->driver?->branch_id);
    }

    /** @return Collection<int,User> */
    private function recipientsForBranch(?string $branchId): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->where(function ($q) use ($branchId): void {
                $q->whereHas('roles', fn ($r) => $r->where('name', 'fleet_manager'))
                    ->orWhere(function ($q2) use ($branchId): void {
                        $q2->where('branch_id', $branchId)
                            ->whereHas('roles', fn ($r) => $r->where('name', 'branch_manager'));
                    });
            })
            ->get();
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CreditNotes\Pages;

use App\Enums\CreditNoteReason;
use App\Filament\Admin\Resources\CreditNotes\CreditNoteResource;
use App\Models\Invoice;
use App\Services\Invoicing\CreditNoteService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCreditNote extends CreateRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $invoice = Invoice::findOrFail($data['invoice_id']);

        return app(CreditNoteService::class)->create(
            $invoice,
            auth()->user(),
            CreditNoteReason::from($data['reason']),
            $data['reason_details'],
            (string) $data['amount'],
        );
    }
}

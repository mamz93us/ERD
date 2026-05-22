<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CreditNote;

class CreditNoteNumberObserver
{
    public function creating(CreditNote $note): void
    {
        if (empty($note->note_number)) {
            $note->note_number = CreditNote::nextNumber();
        }
    }
}

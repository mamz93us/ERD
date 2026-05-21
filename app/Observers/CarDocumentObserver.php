<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CarDocument;

/**
 * Enforces the spec's "unique active doc per type per car" rule (CLAUDE.md §5.2).
 *
 * When a new or updated CarDocument is marked is_active=true, deactivate any
 * other active doc of the same type for the same car. Old docs stay in the
 * table with is_active=false so we keep a history.
 */
class CarDocumentObserver
{
    public function saving(CarDocument $doc): void
    {
        if (! $doc->is_active) {
            return;
        }

        CarDocument::query()
            ->where('car_id', $doc->car_id)
            ->where('doc_type', $doc->doc_type)
            ->where('is_active', true)
            ->when($doc->exists, fn ($q) => $q->where('id', '!=', $doc->id))
            ->update(['is_active' => false]);
    }
}

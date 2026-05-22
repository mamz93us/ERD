<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\NotificationTemplateFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Owner-editable copy for WhatsApp + email notifications. One row per
 * (key, channel, locale). Loaded by TemplateRenderer at send time;
 * unknown placeholders are left literal so a typo doesn't crash a
 * notification dispatch.
 *
 * Audit-logged so changes to customer-facing copy are traceable per
 * CLAUDE.md §10.
 */
class NotificationTemplate extends Model implements Auditable
{
    /** @use HasFactory<NotificationTemplateFactory> */
    use AuditableTrait, HasFactory, HasUuids;

    protected $table = 'notification_templates';

    protected $fillable = [
        'key',
        'channel',
        'locale',
        'subject',
        'body',
        'is_active',
        'updated_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function lookup(string $key, string $channel, string $locale): ?self
    {
        return static::query()
            ->where('key', $key)
            ->where('channel', $channel)
            ->where('locale', $locale)
            ->where('is_active', true)
            ->first()
            ?? static::query()
                ->where('key', $key)
                ->where('channel', $channel)
                ->where('locale', 'en')
                ->where('is_active', true)
                ->first();
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Phase 15: key/value store for owner-editable system settings.
 *
 * Settings live in the DB so the operator can flip them through Filament
 * without redeploying. Sensitive keys (mail password, Green API token)
 * are encrypted at rest via Crypt::encryptString.
 *
 * Audit-logged per CLAUDE.md §10 — changes to mail/WhatsApp creds need
 * traceability.
 *
 * Read-side helpers:
 *   SystemSetting::get('system.name', 'Adly Group Agency')   // cached
 *   SystemSetting::put('mail.password', 'plaintext', encrypt: true)
 */
class SystemSetting extends Model implements Auditable
{
    use AuditableTrait, HasUuids;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'is_encrypted',
        'updated_by_user_id',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /** @var list<string> */
    protected $auditInclude = ['value', 'is_encrypted'];

    /** Keys whose value should be Crypt-encrypted at rest. */
    public const ENCRYPTED_KEYS = [
        'mail.password',
        'whatsapp.token',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        // Resilient against missing table (very first install, before
        // migrations) and cache-unavailable scenarios. Settings-aware
        // code (Filament brandName, AppServiceProvider::applySystemSettings,
        // home page) can run before this migration has been applied.
        try {
            return Cache::rememberForever("system_setting:{$key}", function () use ($key, $default) {
                /** @var self|null $row */
                $row = static::query()->where('key', $key)->first();
                if ($row === null) {
                    return $default;
                }
                if ($row->is_encrypted && filled($row->value)) {
                    try {
                        return Crypt::decryptString($row->value);
                    } catch (\Throwable) {
                        return $default;
                    }
                }

                return $row->value ?? $default;
            });
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function put(string $key, mixed $value, ?bool $encrypt = null): void
    {
        $shouldEncrypt = $encrypt ?? in_array($key, self::ENCRYPTED_KEYS, true);
        $stored = $value === null ? null : (string) $value;
        if ($shouldEncrypt && filled($stored)) {
            $stored = Crypt::encryptString($stored);
        }

        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $stored,
                'is_encrypted' => $shouldEncrypt,
                'updated_by_user_id' => auth()->id(),
            ],
        );

        Cache::forget("system_setting:{$key}");
    }

    protected static function booted(): void
    {
        static::saved(fn (self $s) => Cache::forget("system_setting:{$s->key}"));
        static::deleted(fn (self $s) => Cache::forget("system_setting:{$s->key}"));
    }
}

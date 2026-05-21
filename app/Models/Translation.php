<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\TranslationLoader\LanguageLine;

class Translation extends LanguageLine implements Auditable
{
    use AuditableTrait, HasUuids;

    protected $table = 'translations';

    /** @var list<string> */
    public array $translatable = [];

    /** @var list<string> */
    protected $fillable = [
        'group',
        'key',
        'text_ar',
        'text_en',
        'is_system',
        'updated_by_user_id',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /** @var list<string> */
    protected $auditInclude = ['text_ar', 'text_en'];

    public function getTranslation(string $locale): ?string
    {
        $value = $this->columnFor($locale);

        if ($value !== null) {
            return $value;
        }

        $fallback = config('app.fallback_locale');

        return $fallback !== $locale ? $this->columnFor($fallback) : null;
    }

    public function setTranslation(string $locale, string $value): static
    {
        $col = $locale === 'ar' ? 'text_ar' : 'text_en';
        $this->{$col} = $value;

        return $this;
    }

    /** @return list<string> */
    protected function getTranslatedLocales(): array
    {
        $locales = [];
        if (filled($this->text_ar)) {
            $locales[] = 'ar';
        }
        if (filled($this->text_en)) {
            $locales[] = 'en';
        }

        return $locales;
    }

    private function columnFor(string $locale): ?string
    {
        return match ($locale) {
            'ar' => $this->text_ar,
            'en' => $this->text_en,
            default => null,
        };
    }
}

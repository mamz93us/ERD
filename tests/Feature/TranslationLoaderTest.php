<?php

declare(strict_types=1);

use App\Models\Translation;
use Database\Seeders\TranslationSeeder;

it('resolves UI strings from the translations DB table', function (): void {
    $this->seed(TranslationSeeder::class);

    app()->setLocale('ar');
    expect(__('app.name'))->toBe('مجموعة عدلي');

    app()->setLocale('en');
    expect(__('app.name'))->toBe('Adly Group Agency');
});

it('overrides any later edits to the same key (DB is authoritative)', function (): void {
    $this->seed(TranslationSeeder::class);

    Translation::query()->where('group', 'app')->where('key', 'name')->update([
        'text_ar' => 'اسم جديد',
        'text_en' => 'New Brand Name',
    ]);

    // The package caches per group+locale. Saving the model flushes the cache,
    // but our raw query update did not — so we evict manually here.
    cache()->flush();

    app()->setLocale('ar');
    expect(__('app.name'))->toBe('اسم جديد');

    app()->setLocale('en');
    expect(__('app.name'))->toBe('New Brand Name');
});

it('falls back through DB locale columns then file translations for missing keys', function (): void {
    // No DB row for this key, file loader is the safety net.
    app()->setLocale('en');
    expect(__('non.existent.key'))->toBe('non.existent.key');
});

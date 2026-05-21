<?php

declare(strict_types=1);
use App\Models\Translation;
use Spatie\TranslationLoader\TranslationLoaders\Db;

return [
    /*
     * DB loader resolves __() and trans() from the `translations` table first.
     * File loader (Laravel's default) is the fallback so missing DB keys still
     * find a string in lang/ar/*.php or lang/en/*.php (or vendor lang for
     * packages like Filament).
     */
    'translation_loaders' => [
        Db::class,
    ],

    'model' => Translation::class,
];

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

/**
 * Seeds a minimal set of bilingual UI strings used to verify the DB loader.
 *
 * Phase 2+ resources will add their own translation rows through their seeders
 * with `is_system => true`. Owner-authored strings are added through admin UI
 * with `is_system => false`.
 */
class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['group' => 'app', 'key' => 'name', 'text_ar' => 'مجموعة عدلي', 'text_en' => 'Adly Group Agency'],
            ['group' => 'navigation', 'key' => 'branches', 'text_ar' => 'الفروع', 'text_en' => 'Branches'],
            ['group' => 'navigation', 'key' => 'translations', 'text_ar' => 'الترجمات', 'text_en' => 'Translations'],
            ['group' => 'navigation', 'key' => 'users', 'text_ar' => 'المستخدمون', 'text_en' => 'Users'],
            ['group' => 'common', 'key' => 'yes', 'text_ar' => 'نعم', 'text_en' => 'Yes'],
            ['group' => 'common', 'key' => 'no', 'text_ar' => 'لا', 'text_en' => 'No'],
            ['group' => 'common', 'key' => 'active', 'text_ar' => 'نشط', 'text_en' => 'Active'],
            ['group' => 'common', 'key' => 'inactive', 'text_ar' => 'غير نشط', 'text_en' => 'Inactive'],
        ];

        foreach ($rows as $row) {
            Translation::query()->updateOrCreate(
                ['group' => $row['group'], 'key' => $row['key']],
                array_merge($row, ['is_system' => true])
            );
        }
    }
}

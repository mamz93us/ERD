<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'code' => 'ABH',
                'name' => 'Abu Hammad',
                'name_ar' => 'أبو حماد',
                'city' => 'Abu Hammad',
            ],
            [
                'code' => 'CAI',
                'name' => 'Cairo',
                'name_ar' => 'القاهرة',
                'city' => 'Cairo',
            ],
        ];

        foreach ($branches as $attrs) {
            Branch::query()->updateOrCreate(['code' => $attrs['code']], $attrs);
        }
    }
}

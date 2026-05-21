<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CarCategory;
use Illuminate\Database\Seeder;

class CarCategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['class_code' => 'economy', 'name' => 'Economy', 'name_ar' => 'اقتصادية', 'default_seats' => 5, 'sort_order' => 10],
            ['class_code' => 'midsize', 'name' => 'Midsize', 'name_ar' => 'متوسطة', 'default_seats' => 5, 'sort_order' => 20],
            ['class_code' => 'suv', 'name' => 'SUV', 'name_ar' => 'دفع رباعي', 'default_seats' => 7, 'sort_order' => 30],
            ['class_code' => 'luxury', 'name' => 'Luxury', 'name_ar' => 'فاخرة', 'default_seats' => 5, 'sort_order' => 40],
            ['class_code' => 'van', 'name' => 'Van', 'name_ar' => 'فان', 'default_seats' => 9, 'sort_order' => 50],
            ['class_code' => 'minibus', 'name' => 'Minibus', 'name_ar' => 'ميني باص', 'default_seats' => 14, 'sort_order' => 60],
        ];

        foreach ($rows as $row) {
            CarCategory::query()->updateOrCreate(['class_code' => $row['class_code']], $row);
        }
    }
}

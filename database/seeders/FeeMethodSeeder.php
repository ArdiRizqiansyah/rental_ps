<?php

namespace Database\Seeders;

use App\Models\FeeMethod;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FeeMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attr = [
            [
                'code' => 'qris',
                'name' => 'Qris',
                'percent' => 0.7,
                'is_published' => 1
            ],
            [
                'code' => 'gopay',
                'name' => 'Gopay',
                'percent' => 2,
                'is_published' => 1
            ],
            [
                'code' => 'shopeepay',
                'name' => 'ShopeePay',
                'percent' => 2,
                'is_published' => 1
            ],
            [
                'code' => 'bni',
                'name' => 'BNI',
                'price' => 4500,
                'is_published' => 1
            ],
            [
                'code' => 'bri',
                'name' => 'BRI',
                'price' => 4500,
                'is_published' => 1
            ],
            [
                'code' => 'mandiri',
                'name' => 'Mandiri',
                'price' => 4500,
                'is_published' => 1
            ]
        ];

        foreach ($attr as $attr) {
            FeeMethod::create($attr);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'PS 4',
                'price' => 30000,
            ],
            [
                'name' => 'PS 5',
                'price' => 40000,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate($service);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Options
        $options = [
            ['name' => 'Kelola Produk'],
            ['name' => 'Kelola Pembelian'],
            ['name' => 'Kelola Penjualan'],
            ['name' => 'Laporan Pembelian'],
            ['name' => 'Laporan Penjualan'],
            ['name' => 'Dashboard Realtime'],
        ];

        foreach ($options as $option) {
            Option::updateOrCreate($option);
        }

        // Plans
        $plans = [
            [
                'name' => 'Free Trial',
                'description' => 'Mulai secara gratis!',
                'price' => 0,
                'duration_type' => 'months',
                'duration' => 1,
                'is_active' => true,
                'priority' => false,
                'options' => [1, 2, 3],
            ],
            [
                'name' => 'Basic',
                'description' => 'Cocok untuk usaha kecil',
                'price' => 100000,
                'duration_type' => 'months',
                'duration' => 1,
                'is_active' => true,
                'priority' => true,
                'options' => [1, 2, 3, 4],
            ],
            [
                'name' => 'Pro',
                'description' => 'Cocok untuk usaha menengah',
                'price' => 200000,
                'duration_type' => 'months',
                'duration' => 1,
                'is_active' => true,
                'priority' => false,
                'options' => [1, 2, 3, 4, 5],
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Cocok untuk usaha makro',
                'price' => 300000,
                'duration_type' => 'months',
                'duration' => 1,
                'is_active' => true,
                'priority' => false,
                'options' => [1, 2, 3, 4, 5, 6],
            ],
        ];

        foreach ($plans as $plan) {
            $options = $plan['options'];
            unset($plan['options']);

            $plan = Plan::updateOrCreate($plan);

            $plan->options()->sync($options);
        }
    }
}

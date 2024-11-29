<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\UserStore;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::updateOrCreate([
            'name' => 'Komunitas',
            'description' => 'Komunitas area Bantul, DI Yogyakarta',
            'address' => 'Bantul, DI Yogyakarta',
            'is_community' => true,
            'community_id' => 1,
            'activated_at' => now(),
        ]);

        UserStore::updateOrCreate([
            'user_id' => 3,
            'store_id' => 1,
        ]);
    }
}

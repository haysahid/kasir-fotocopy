<?php

namespace Database\Seeders;

use App\Models\Community;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Community::updateOrCreate([
            'name' => 'Komunitas',
            'address' => 'Bantul, DI Yogyakarta',
            // 'description' => 'Komunitas adalah sebuah komunitas yang beranggotakan para pemilik toko yang tergabung dalam satu wadah.',
        ]);
    }
}

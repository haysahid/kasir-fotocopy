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
            'name' => 'Ritel Bantul Sejahtera',
            'address' => 'Bantul, DI Yogyakarta',
            // 'description' => 'Paguyuban adalah sebuah komunitas yang beranggotakan para pemilik toko yang tergabung dalam satu wadah.',
        ]);
    }
}

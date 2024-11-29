<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('superadmin2024'),
                'role_id' => 1,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin2024'),
                'role_id' => 2,
            ],
            [
                'name' => 'Komunitas',
                'email' => 'komunitas@gmail.com',
                'password' => Hash::make('komunitas2024'),
                'role_id' => 3,
            ],
        ];

        foreach ($users as $user) {
            if (!User::where('email', $user['email'])->first()) {
                User::updateOrCreate($user);
            }
        }
    }
}

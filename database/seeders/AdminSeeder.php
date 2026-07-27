<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'BrainBlitz Admin',
                'nickname' => 'Admin',
                'password' => 'admin1234',
                'role' => 'admin',
            ]
        );
    }
}

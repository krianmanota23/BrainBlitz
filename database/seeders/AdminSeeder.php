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
        User::create([
            'full_name' => 'BrainBlitz Admin',
            'username' => 'admin',
            'nickname' => 'Admin',
            'password' => 'admin1234', // Model cast 'hashed' handles hashing if set right, but many seeders hash manually
            'role' => 'admin',
        ]);
    }
}

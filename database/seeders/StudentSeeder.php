<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'student1'],
            [
                'full_name' => 'Alex Hunter',
                'nickname' => 'ShadowBlitz',
                'password' => 'password123',
                'role' => 'student',
            ]
        );

        User::firstOrCreate(
            ['username' => 'student2'],
            [
                'full_name' => 'Sarah Connor',
                'nickname' => 'NeonVortex',
                'password' => 'password123',
                'role' => 'student',
            ]
        );
    }
}

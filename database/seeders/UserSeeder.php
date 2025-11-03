<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'is_admin' => true,
                'status' => 'approved',
                'password' => Hash::make('password'),
            ],
            [
                'id' => 2,
                'name' => 'User',
                'email' => 'user@gmail.com',
                'is_admin' => false,
                'status' => 'approved',
                'password' => Hash::make('password'),
            ],
        ];
        DB::table('users')->insert($users);
    }
}

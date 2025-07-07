<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => ' Tul Khatri',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('acpassword'),
                'role' => 'admin'
            ],
            [
                'name' => 'Ram Bhandari',
                'email' => 'user@gmail.com',
                'password' => Hash::make('dcpassword'),
                'role' => 'user'
            ]
        ]);
    }
}

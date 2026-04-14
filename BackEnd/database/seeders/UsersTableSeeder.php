<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'date' => '1990-01-01',
                'gender' => 'Male',
                'gmail' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'status' => true,
                'id_role' => 1, // Admin
            ],
            [
                'name' => 'Doctor Strange',
                'date' => '1985-05-05',
                'gender' => 'Male',
                'gmail' => 'doctor@gmail.com',
                'password' => Hash::make('123456'),
                'status' => true,
                'id_role' => 2, // Doctor
            ],
            [
                'name' => 'Dr. House',
                'date' => '1980-06-15',
                'gender' => 'Male',
                'gmail' => 'doctor1@gmail.com',
                'password' => Hash::make('123456'),
                'status' => true,
                'id_role' => 2,
            ],
            [
                'name' => 'Dr. Watson',
                'date' => '1982-09-20',
                'gender' => 'Male',
                'gmail' => 'doctor2@gmail.com',
                'password' => Hash::make('123456'),
                'status' => true,
                'id_role' => 2,
            ],
            [
                'name' => 'Dr. Who',
                'date' => '1975-03-10',
                'gender' => 'Male',
                'gmail' => 'doctor3@gmail.com',
                'password' => Hash::make('123456'),
                'status' => true,
                'id_role' => 2,
            ],
        ]);
    }
}

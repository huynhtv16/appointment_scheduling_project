<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('patients')->insert([
            [
                'name' => 'Nguyen Van A',
                'gender' => 'Male',
                'date' => '2000-01-01 00:00:00',
                'address' => 'Hanoi',
                'gmail' => 'patientA@gmail.com',
                'password' => Hash::make('123456'),
                'cccd' => '123456789',
                'bhyt' => 'BHYT001',
                'prehistoric' => 'No history',
            ],
            [
                'name' => 'Tran Thi B',
                'gender' => 'Female',
                'date' => '1998-03-15 00:00:00',
                'address' => 'Ho Chi Minh City',
                'gmail' => 'patientB@gmail.com',
                'password' => Hash::make('123456'),
                'cccd' => '987654321',
                'bhyt' => 'BHYT002',
                'prehistoric' => 'Allergy to penicillin',
            ],
            [
                'name' => 'Le Van C',
                'gender' => 'Male',
                'date' => '1995-07-20 00:00:00',
                'address' => 'Da Nang',
                'gmail' => 'patientC@gmail.com',
                'password' => Hash::make('123456'),
                'cccd' => '112233445',
                'bhyt' => 'BHYT003',
                'prehistoric' => 'Asthma',
            ],
            [
                'name' => 'Pham Thi D',
                'gender' => 'Female',
                'date' => '2001-11-05 00:00:00',
                'address' => 'Hai Phong',
                'gmail' => 'patientD@gmail.com',
                'password' => Hash::make('123456'),
                'cccd' => '556677889',
                'bhyt' => 'BHYT004',
                'prehistoric' => 'Diabetes type 2',
            ],
            [
                'name' => 'Do Van E',
                'gender' => 'Male',
                'date' => '1992-09-10 00:00:00',
                'address' => 'Can Tho',
                'gmail' => 'patientE@gmail.com',
                'password' => Hash::make('123456'),
                'cccd' => '667788990',
                'bhyt' => 'BHYT005',
                'prehistoric' => 'Hypertension',
            ],
        ]);
    }
}

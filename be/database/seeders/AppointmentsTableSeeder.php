<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('appointments')->insert([
            [
                'id_patient' => 1,
                'date' => now()->addDays(1),
                'symptom' => 'Headache, fever',
                'id_doctor' => 2, // Dr. Strange
                'id_service' => 1,
                'status' => 0,
            ],
            [
                'id_patient' => 2,
                'date' => now(), // hôm nay
                'symptom' => 'Stomach pain, nausea',
                'id_doctor' => 3, // Dr. House
                'id_service' => 2,
                'status' => 0,
            ],
            [
                'id_patient' => 3,
                'date' => now(), // hôm nay
                'symptom' => 'Flu symptoms, cough',
                'id_doctor' => 4, // Dr. Watson
                'id_service' => 1,
                'status' => 0,
            ],
            [
                'id_patient' => 4,
                'date' => now(), // hôm nay
                'symptom' => 'Chest pain',
                'id_doctor' => 5, // Dr. Who
                'id_service' => 3,
                'status' => 1, // đã xác nhận
            ],
        ]);
    }
}

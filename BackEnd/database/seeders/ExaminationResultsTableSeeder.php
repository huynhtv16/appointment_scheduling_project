<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExaminationResultsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('examination_results')->insert([
            [
                'id_appointment' => 1,
                'diagnosis' => 'Migraine',
                'examination_time' => now(),
            ],
        ]);
    }
}

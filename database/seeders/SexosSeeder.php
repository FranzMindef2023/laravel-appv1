<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SexosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sexos')->insert([
            [
                'sexo' => 'MASCULINO',
                'status' => true,
                'created_at' => now(),'updated_at' => now()
            ],
            [
                'sexo' => 'FEMENINO',
                'status' => true,
                'created_at' => now(),'updated_at' => now()
            ]
        ]);   
    }
}

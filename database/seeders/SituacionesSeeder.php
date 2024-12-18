<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SituacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('situaciones')->insert([
            'situacion' => 'SERVICIO ACTIVO',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

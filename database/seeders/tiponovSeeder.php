<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tiponovSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $novedades = [
            ['novedad' => 'COMISIÓN', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'VACACIÓN', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'PERMISO DECLARACIÓN JURADA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'PERMISO', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'PERMISO PATERNIDAD', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'PERMISO MATRIMONIO', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'PERMISO FALLECIMIENTO', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'BAJA MÉDICA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'INTERNADO COSMIL', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'BAJA PRENATAL', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'BAJA POST NATAL', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'GUARDIA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['novedad' => 'FALTA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('tiponovedad')->insert($novedades);
    }
}

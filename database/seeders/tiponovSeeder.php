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
            ['novedad' => 'Comisión', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Vacación', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Permiso Declaración Jurada', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Permiso', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Permiso Paternidad', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Permiso Matrimonio', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Permiso Fallecimiento', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Baja Médica', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Internado Cosmil', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Baja Prenatal', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Baja Post Natal', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Guardia', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['novedad' => 'Falta', 'status' => true,'created_at' => now(),'updated_at' => now()]
        ];

        DB::table('tiponovedad')->insert($novedades);
    }
}

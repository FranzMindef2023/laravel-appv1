<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PuestosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('puestos')->insert([
            ['nompuesto' => 'MINISTRO', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'VICEMINISTRO', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'DIRECTOR GENERAL', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'JEFE DE UNIDAD', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'RESPONSABLE DE SECCIÓN', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'TECNICO', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'SEGURIDAD', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'COMANDANTE', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'JEFE DE GABINETE', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['nompuesto' => 'AYUDANTE', 'status' => true,'created_at' => now(),'updated_at' => now()]
        ]);  
    }
}

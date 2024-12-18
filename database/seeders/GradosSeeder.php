<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('grados')->insert([
            ['grado' => 'General de Fuerza', 'abregrado' => 'GRAL.FZA.', 'categoria' => 'OG', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Almirante', 'abregrado' => 'ALMTE.', 'categoria' => 'OG', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'General de División', 'abregrado' => 'GRAL.DIV.', 'categoria' => 'OG', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Vicealmirante', 'abregrado' => 'V.ALMTE.', 'categoria' => 'OG', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'General de Brigada', 'abregrado' => 'GRAL.BRIG.', 'categoria' => 'OG', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Contralmirante', 'abregrado' => 'C.ALMTE.', 'categoria' => 'OG', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Coronel', 'abregrado' => 'CNL.', 'categoria' => 'OSP', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Capitán de Navío', 'abregrado' => 'CN', 'categoria' => 'OSP', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Teniente Coronel', 'abregrado' => 'TCNL.', 'categoria' => 'OSP', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Capitán de Fragata', 'abregrado' => 'CF', 'categoria' => 'OSP', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Mayor', 'abregrado' => 'MI.', 'categoria' => 'OSP', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Capitán de Corbeta', 'abregrado' => 'CC', 'categoria' => 'OSP', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Capitán', 'abregrado' => 'CAP.', 'categoria' => 'OSB', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Teniente de Navío', 'abregrado' => 'TN', 'categoria' => 'OSB', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Teniente', 'abregrado' => 'TTE.', 'categoria' => 'OSB', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Teniente de Fragata', 'abregrado' => 'TF', 'categoria' => 'OSB', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Subteniente', 'abregrado' => 'SBTE.', 'categoria' => 'OSB', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Alférez', 'abregrado' => 'ALF.', 'categoria' => 'OSB', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sof. Maestro', 'abregrado' => 'SOF.MTRE.', 'categoria' => 'SOF', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sof. Mayor', 'abregrado' => 'SOF.MI.', 'categoria' => 'SOF', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sof. 1ro', 'abregrado' => 'SOF.1RO.', 'categoria' => 'SOF', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sof. 2do', 'abregrado' => 'SOF.2DO.', 'categoria' => 'SOF', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sof. Inicial', 'abregrado' => 'SOF.INL.', 'categoria' => 'SOF', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sargento 1ro', 'abregrado' => 'SGTO.1RO.', 'categoria' => 'SGT', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sargento 2do', 'abregrado' => 'SGTO.2DO.', 'categoria' => 'SGT', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Sargento Inicial', 'abregrado' => 'SGTO.INL.', 'categoria' => 'SGT', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Profesor I', 'abregrado' => 'PROF. I', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Profesor II', 'abregrado' => 'PROF. II', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Profesor III', 'abregrado' => 'PROF. III', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Profesor IV', 'abregrado' => 'PROF. IV', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Profesor V', 'abregrado' => 'PROF. V', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Técnico I', 'abregrado' => 'TECN. I', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Técnico II', 'abregrado' => 'TECN. II', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Técnico III', 'abregrado' => 'TECN. III', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Técnico IV', 'abregrado' => 'TECN. IV', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['grado' => 'Técnico V', 'abregrado' => 'TECN.V', 'categoria' => 'CIV', 'status' => true,'created_at' => now(),'updated_at' => now()],
        ]);
    }
}

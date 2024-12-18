<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecialidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('especialidades')->insert([
            ['especialidad' => 'No aplica','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DAEN.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DEM.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DEMA.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DEMN.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DIM.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Av.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DA.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'AO.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGON.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGIM.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DEPSS.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DEPSSM.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DESA.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'DESN.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Mot.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'M.B.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'San.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Int.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Tgrafo.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Av.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Agr.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Mus.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'Tec. Av.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONIM.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONMQ.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONMC.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONEL.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONAD.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONIS.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONSAN.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONCO.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['especialidad' => 'CGONHD.','status' => true,'created_at' => now(),'updated_at' => now()],
            ]);
    }
}

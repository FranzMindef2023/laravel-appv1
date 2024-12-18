<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('armas')->insert([
            ['arma' => 'No aplica', 'abrearma' => 'No Corresp.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['arma' => 'Infantería', 'abrearma' => 'Inf.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['arma' => 'Artillería', 'abrearma' => 'Art.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['arma' => 'Comunicación', 'abrearma' => 'Com.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['arma' => 'Caballería', 'abrearma' => 'Cab.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['arma' => 'Logística', 'abrearma' => 'Log.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['arma' => 'Ingeniería', 'abrearma' => 'Ing.','status' => true,'created_at' => now(),'updated_at' => now()],
            ['arma' => 'Inteligencia', 'abrearma' => 'Icia.','status' => true,'created_at' => now(),'updated_at' => now()],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organizacion')->insert([
            'nomorg' => 'MINSITERIO',
            'sigla' => 'MINDEF',
            'idubigeo' => 54,
            'status' => true,
            'idpadre' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

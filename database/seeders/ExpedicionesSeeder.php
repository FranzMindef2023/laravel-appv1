<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpedicionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('expediciones')->insert([
            [
            'idexpedicion' => 1,
            'Departamento' => 'CH',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now()
            ],
            [
                'idexpedicion' => 2,
                'Departamento' => 'LP',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idexpedicion' => 3,
                'Departamento' => 'CB',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idexpedicion' => 4,
                'Departamento' => 'OR',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idexpedicion' => 6,
                'Departamento' => 'TJ',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idexpedicion' => 7,
                'Departamento' => 'SC',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idexpedicion' => 8,
                'Departamento' => 'BN',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idexpedicion' => 9,
                'Departamento' => 'PN',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]
        );
    }
}

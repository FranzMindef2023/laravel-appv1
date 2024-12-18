<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuerzasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
            'fuerza' => 'EJERCITO DE BOLIVIA',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now()
            ],
            [
                'fuerza' => 'FUERZA AEREA',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'fuerza' => 'ARMADA BOLIVIANA',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('fuerzas')->insert($statuses);
    }
}

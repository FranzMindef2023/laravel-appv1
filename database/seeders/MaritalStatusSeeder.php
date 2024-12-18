<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Soltero(a)', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['name' => 'Casado(a)', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['name' => 'Divorciado(a)', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['name' => 'Viudo(a)', 'status' => true,'created_at' => now(),'updated_at' => now()],
            ['name' => 'Separado(a)', 'status' => true,'created_at' => now(),'updated_at' => now()]
        ];

        DB::table('statuscv')->insert($statuses);
    }
}

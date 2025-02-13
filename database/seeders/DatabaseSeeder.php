<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ArmasSeeder::class,
            EspecialidadesSeeder::class,
            FuerzasSeeder::class,
            GradosSeeder::class,
            MaritalStatusSeeder::class,
            OrganizacionSeeder::class,
            PuestosSeeder::class,
            SexosSeeder::class,
            tiponovSeeder::class,
            ExpedicionesSeeder::class,
            SituacionesSeeder::class,
        ]);
    }
}

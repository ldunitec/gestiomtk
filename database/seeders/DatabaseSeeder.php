<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ConfigAsistencia;
use App\Models\Departamento;
use App\Models\Marca;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $this->call([RoleSeeder::class]);
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678')
        ])->assignRole('ADMINISTRADOR')

        ;
        Marca::create([
            'nombre' => 'MORAK',

        ]);
        Departamento::create([
            'nombre' => 'GERENCIA',

        ]);
        ConfigAsistencia::updateOrCreate(
            ['id' => 1],
            [
                'entrada_desde' => '08:00',
                'entrada_hasta' => '08:15',
                'salida_desde' => '17:00',
                'salida_hasta' => '17:30',
            ]
        );


    }
}

<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::updateOrCreate(
            ['email' => 'superadmin@taxidiamantes.com'],
            [
                'nombre' => 'Super',
                'apellidos' => 'Administrador',
                'email' => 'superadmin@taxidiamantes.com',
                'username' => 'superadmin',
                'password' => Hash::make('SuperAdmin2024*'),
                'telefono' => '0000000000',
                'rol' => 'superadmin',
                'estado' => 'activo',
                'es_protegido' => true,
            ]
        );
    }
}

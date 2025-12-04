<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminUsers = [
            [
                'nombre' => 'Marco',
                'apellido' => 'Reyes',
                'rut' => '172223334', // Cambiar por el rut correcto
                'email' => 'marco.reyes@phantom.cl',
                'password' => bcrypt('MarcoR#811'),
                'role' => 'super_admin',
            ],
            [
                'nombre' => 'Julio',
                'apellido' => 'Segovia',
                'rut' => '198187232',
                'email' => 'segoviaortizjulio@gmail.com',
                'password' => bcrypt('JulioS#811'),
                'role' => 'super_admin',
            ]
        ];

        foreach ($superAdminUsers as $userData) {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'nombre' => $userData['nombre'],
                    'apellidos' => $userData['apellido'],
                    'password' => $userData['password'],
                    'rut' => $userData['rut'],
                ]
            );

            // Asignar rol de super administrador
            $role = \App\Models\Role::where('slug', $userData['role'])->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}

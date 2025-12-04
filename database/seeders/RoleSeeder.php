<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Administrador', 'slug' => 'super_admin', 'description' => 'Usuario con todos los permisos del sistema'],
            ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Usuario con permisos administrativos'],
            ['name' => 'Abogado', 'slug' => 'abogado', 'description' => 'Usuario con permisos de abogado'],
            ['name' => 'Cliente', 'slug' => 'cliente', 'description' => 'Usuario con permisos de cliente'],
            ['name' => 'Secretaria', 'slug' => 'secretaria', 'description' => 'Usuario con permisos de secretaria'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}

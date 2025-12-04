<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'id' => 1,
                'name' => 'Gestión de Usuarios',
                'slug' => 'user.mantenedor',
                'description' => 'Configuraciones relacionadas con el mantenedor de usuarios',
                'active' => true
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['slug' => $setting['slug']],
                $setting
            );
        }
    }
}

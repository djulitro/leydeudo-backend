<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Configuración de módulos con sus permisos y roles
     * Para agregar un nuevo módulo, solo agrega una entrada aquí
     */
    private function getModulesConfig(): array
    {
        return [
            // Módulo: Gestión de Usuarios
            [
                'setting_slug' => 'user.mantenedor',
                'permissions' => [
                    ['name' => 'Ver Usuarios', 'slug' => 'users.view', 'description' => 'Permite ver listado de usuarios'],
                    ['name' => 'Crear Usuarios', 'slug' => 'users.create', 'description' => 'Permite crear nuevos usuarios'],
                    ['name' => 'Editar Usuarios', 'slug' => 'users.edit', 'description' => 'Permite editar usuarios existentes'],
                    ['name' => 'Eliminar Usuarios', 'slug' => 'users.delete', 'description' => 'Permite eliminar usuarios'],
                ],
                'role_permissions' => [
                    'super_admin' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                    'admin' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                    'abogado' => ['users.view'],
                    'secretaria' => [],
                    'cliente' => [],
                ]
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulesConfig = $this->getModulesConfig();

        foreach ($modulesConfig as $moduleConfig) {
            $this->seedModule($moduleConfig);
        }

        $this->command->info('✓ Todos los permisos y asignaciones creados exitosamente');
    }

    /**
     * Procesa un módulo completo: crea permisos y los asigna a roles
     */
    private function seedModule(array $moduleConfig): void
    {
        // Obtener el setting
        $setting = Setting::where('slug', $moduleConfig['setting_slug'])->first();

        if (!$setting) {
            $this->command->warn("⚠ Setting '{$moduleConfig['setting_slug']}' no encontrado. Saltando...");
            return;
        }

        // Crear permisos
        $createdPermissions = [];
        foreach ($moduleConfig['permissions'] as $permissionData) {
            $permission = Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                array_merge($permissionData, ['setting_id' => $setting->id])
            );
            $createdPermissions[$permission->slug] = $permission->id;
        }

        $this->command->info("  → Permisos creados para: {$setting->name}");

        // Asignar permisos a roles
        foreach ($moduleConfig['role_permissions'] as $roleSlug => $permissionSlugs) {
            $role = Role::where('slug', $roleSlug)->first();
            
            if (!$role) {
                $this->command->warn("    ⚠ Rol '{$roleSlug}' no encontrado");
                continue;
            }

            // Obtener IDs de permisos para este rol
            $permissionIds = collect($permissionSlugs)
                ->map(fn($slug) => $createdPermissions[$slug] ?? null)
                ->filter()
                ->values()
                ->toArray();

            // Obtener permisos actuales del rol (de otros módulos)
            $currentPermissions = $role->permissions()
                ->whereNotIn('setting_id', [$setting->id])
                ->pluck('permissions.id')
                ->toArray();

            // Combinar permisos actuales con los nuevos
            $role->permissions()->sync(array_merge($currentPermissions, $permissionIds));

            $permissionCount = count($permissionSlugs);
            $this->command->info("    ✓ {$role->name}: {$permissionCount} permisos");
        }
    }
}

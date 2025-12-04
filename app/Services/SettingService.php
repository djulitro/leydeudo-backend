<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

class SettingService
{
    /**
     * Obtiene la configuración completa del usuario
     * (Mismo formato que UserConfigController)
     */
    public function getUserConfig(User $user): array
    {
        // Cargar relaciones
        $user->load(['roles.permissions.setting']);

        // Obtener settings activos
        $activeSettings = Setting::where('active', true)
            ->with('permissions')
            ->get();

        // Obtener permisos del usuario
        $userPermissions = $this->getUserPermissions($user);

        return [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'nombre' => $user->nombre ?? $user->name,
                'apellidos' => $user->apellidos ?? null,
                'rut' => $user->rut ?? null,
            ],
            'roles' => $user->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ]),
            'settings' => $activeSettings->map(fn($setting) => [
                'id' => $setting->id,
                'name' => $setting->name,
                'slug' => $setting->slug,
                'description' => $setting->description,
                'active' => $setting->active,
            ]),
            'permissions' => $userPermissions,
        ];
    }

    /**
     * Obtiene los permisos del usuario organizados por setting
     */
    private function getUserPermissions(User $user): array
    {
        $permissions = [];

        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                // Solo incluir permisos de settings activos
                if ($permission->setting->active) {
                    $settingSlug = $permission->setting->slug;
                    
                    if (!isset($permissions[$settingSlug])) {
                        $permissions[$settingSlug] = [
                            'setting_name' => $permission->setting->name,
                            'permissions' => []
                        ];
                    }

                    // Evitar duplicados
                    if (!in_array($permission->slug, $permissions[$settingSlug]['permissions'])) {
                        $permissions[$settingSlug]['permissions'][] = $permission->slug;
                    }
                }
            }
        }

        return $permissions;
    }
}
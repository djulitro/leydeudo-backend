<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserConfigController extends Controller
{
    /**
     * Obtiene la configuración completa del usuario autenticado
     * Incluye: settings activos, roles y permisos del usuario
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Cargar roles con sus permisos y settings
        $user->load(['roles.permissions.setting']);

        // Obtener todos los permisos del usuario agrupados por setting
        $userPermissions = (new SettingService())->getUserConfig($user);

        return response()->json($userPermissions);
    }
}

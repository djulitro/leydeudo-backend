<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login de usuario
     * Retorna token + configuración completa (settings y permisos)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (is_null($user) || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => ['Las credenciales proporcionadas son incorrectas.'],
            ], 422);
        }

        // Verificar que el usuario esté activo
        if (!$user->estado) {
            return response()->json([
                'message' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ], 403);
        }

        // Generar token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Obtener configuración del usuario
        $config = (new SettingService())->getUserConfig($user);

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'token_type' => 'Bearer',
            ...$config
        ]);
    }

    /**
     * Logout de usuario
     */
    public function logout(Request $request): JsonResponse
    {
        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout exitoso',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Validar un token de cambio de contraseña
     */
    public function validateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:64',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Token inválido',
                'errors' => $validator->errors(),
            ], 422);
        }

        $resetToken = PasswordResetToken::where('token', $request->token)
            ->valid()
            ->first();

        if (!$resetToken) {
            return response()->json([
                'message' => 'El token es inválido o ha expirado',
            ], 400);
        }

        return response()->json([
            'message' => 'Token válido',
            'user' => [
                'email' => $resetToken->user->email,
                'nombre' => $resetToken->user->nombre,
                'apellidos' => $resetToken->user->apellidos,
            ],
        ]);
    }

    /**
     * Cambiar contraseña usando el token
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:64',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $resetToken = PasswordResetToken::where('token', $request->token)
            ->valid()
            ->first();

        if (!$resetToken) {
            return response()->json([
                'message' => 'El token es inválido o ha expirado',
            ], 400);
        }

        // Actualizar contraseña
        $resetToken->user->update([
            'password' => Hash::make($request->password),
        ]);

        // Marcar token como usado
        $resetToken->markAsUsed();

        // Invalidar otros tokens del usuario
        PasswordResetToken::where('user_id', $resetToken->user_id)
            ->where('id', '!=', $resetToken->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente',
        ]);
    }
}

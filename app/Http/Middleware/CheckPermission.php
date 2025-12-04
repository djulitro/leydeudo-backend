<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Verifica que:
     * 1. El usuario esté autenticado
     * 2. El setting del permiso esté activo
     * 3. El usuario tenga un rol con ese permiso
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  El slug del permiso requerido (ej: 'expedientes.view')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Verificar autenticación
        if (!$request->user()) {
            return response()->json([
                'message' => 'No autenticado.'
            ], 401);
        }

        // Verificar permiso
        if (!$request->user()->hasPermission($permission)) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción.',
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }
}

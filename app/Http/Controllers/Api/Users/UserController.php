<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Role;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function listUsers()
    {
        $users = (new UserService())->getAll();

        return response()->json([
            'users' => $users,
        ]);
    }

    public function getById(int $id_user)
    {
        $user = (new UserService())->getById($id_user);

        $user->load('roles');

        return response()->json([
            'message' => 'Usuario obtenido correctamente',
            'data' => $user,
        ]);
    }

    public function createUser(UserCreateRequest $request)
    {
        $data = $request->safe()->all();

        /** @var \App\Models\User $userAuthenticated */
        $userAuthenticated = Auth::user();

        if ($data['role_slug'] === 'super_admin' && !$userAuthenticated->hasRole('super_admin')) {
            return response()->json([
                'message' => 'No tienes permisos para asignar el rol de super admin.',
            ], 403);
        }

        $result = (new UserService())->createUser($data);

        $response = [
            'message' => 'Usuario creado exitosamente',
            'user' => $result['user'],
        ];

        // Agregar token si existe (usuario sin contraseña)
        if ($result['reset_token']) {
            $response['reset_token'] = $result['reset_token'];
            $response['reset_url'] = config('app.frontend_url') . '/reset-password/' . $result['reset_token'];
        }

        return response()->json($response, 201);
    }

    public function updateUser(int $id_user, UserUpdateRequest $request)
    {
        $data = $request->safe()->all();

        /** @var \App\Models\User $userAuthenticated */
        $userAuthenticated = Auth::user();

        if (isset($data['role_slug']) && $data['role_slug'] === 'super_admin' && !$userAuthenticated->hasRole('super_admin')) {
            return response()->json([
                'message' => 'No tienes permisos para asignar el rol de super admin.',
            ], 403);
        }

        $user = (new UserService())->updateUser($id_user, $data);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $user,
        ]);
    }

    public function disable(int $id_user)
    {
        (new UserService())->disable($id_user);

        return response()->json([
            'message' => 'Usuario desactivado exitosamente',
        ]);
    }

    public function activate(int $id_user)
    {
        (new UserService())->activate($id_user);

        return response()->json([
            'message' => 'Usuario activado exitosamente',
        ]);
    }

    public function getRoles()
    {
        $roles = Role::all();

        return response()->json([
            'message' => 'Roles obtenidos correctamente',
            'data' => $roles->toArray()
        ]);
    }
}

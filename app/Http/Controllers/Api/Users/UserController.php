<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
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

        $user = (new UserService())->createUser($data);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $user,
        ], 201);
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
}

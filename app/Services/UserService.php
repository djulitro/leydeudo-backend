<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAll(): Collection
    {
        return User::with('roles')->get();
    }

    public function getById(int $id): ?User
    {
        return User::find($id);
    }

    public function createUser(array $data): User
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'rut' => $data['rut'],
            'direccion' => $data['direccion'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'celular' => $data['celular'] ?? null,
        ]);

        $this->syncRole($user, $data['role_slug']);

        return $user;
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->getById($id);

        if (!$user) {
            throw new \Exception("Usuario no encontrado");
        }

        $user->update([
            'email' => $data['email'] ?? $user->email,
            'nombre' => $data['nombre'] ?? $user->nombre,
            'apellidos' => $data['apellidos'] ?? $user->apellidos,
            'rut' => $data['rut'] ?? $user->rut,
            'direccion' => $data['direccion'] ?? $user->direccion,
            'telefono' => $data['telefono'] ?? $user->telefono,
            'celular' => $data['celular'] ?? $user->celular,
        ]);

        if (isset($data['role_slug'])) {
            $this->syncRole($user, $data['role_slug']);
        }

        return $user;
    }

    public function disable(int $id): void
    {
        $user = $this->getById($id);

        if (!$user) {
            throw new \Exception("Usuario no encontrado");
        }

        $user->update(['estado' => 0]);
    }

    private function syncRole(User $user, string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        $user->roles()->sync([$role->id]);
    }
}
<?php

namespace App\Services;

use App\Mail\FirstLoginMail;
use App\Models\PasswordResetToken;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    public function createUser(array $data): array
    {
        $password = $data['password'] ?? Str::random(16);
        
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($password),
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'rut' => $data['rut'],
            'direccion' => $data['direccion'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'celular' => $data['celular'] ?? null,
        ]);

        $this->syncRole($user, $data['role_slug']);

        $resetToken = null;
        if (!isset($data['password'])) {
            $resetToken = $this->generateFirstLoginToken($user);
        }

        return [
            'user' => $user->load('roles'),
            'reset_token' => $resetToken ? $resetToken->token : null,
        ];
    }

    /**
     * Generar token de primer login para el usuario y enviar email
     */
    private function generateFirstLoginToken(User $user): PasswordResetToken
    {
        $token = PasswordResetToken::generateToken();
        
        $resetToken = PasswordResetToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'type' => 'first_login',
            'expires_at' => now()->addDays(7), // Token válido por 7 días
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Generar URL de reset
        $resetUrl = config('app.frontend_url') . '/reset-password/' . $resetToken->token;

        // Enviar email
        try {
            Mail::to($user->email)->send(new FirstLoginMail($user, $resetUrl, $resetToken->token));
        } catch (\Exception $e) {
            // Log del error pero no fallar la creación del usuario
            Log::error('Error al enviar email de primer login', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return $resetToken;
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

    public function activate(int $id): void
    {
        $user = $this->getById($id);

        if (!$user) {
            throw new \Exception("Usuario no encontrado");
        }

        $user->update(['estado' => 1]);
    }

    private function syncRole(User $user, string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        $user->roles()->sync([$role->id]);
    }
}
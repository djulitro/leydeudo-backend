<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'nombre',
        'apellidos',
        'rut',
        'id_cargo',
        'direccion',
        'telefono',
        'celular',
        'estado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Roles del usuario
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     * También verifica que el setting del permiso esté activo
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Obtener todos los roles del usuario con sus permisos
        foreach ($this->roles as $role) {
            // Buscar el permiso en el rol
            $permission = $role->permissions()
                ->with('setting')
                ->where('slug', $permissionSlug)
                ->first();

            if ($permission && $permission->setting->active) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }
}

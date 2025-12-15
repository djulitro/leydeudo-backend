<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PasswordResetToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'type',
        'expires_at',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generar un token único y seguro
     */
    public static function generateToken(): string
    {
        return hash('sha256', Str::random(60) . time());
    }

    /**
     * Verificar si el token ha expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Verificar si el token ya fue usado
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    /**
     * Verificar si el token es válido
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    /**
     * Marcar el token como usado
     */
    public function markAsUsed(): void
    {
        $this->update([
            'used_at' => now(),
        ]);
    }

    /**
     * Scope para tokens válidos
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->whereNull('used_at');
    }

    /**
     * Scope para tokens por tipo
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

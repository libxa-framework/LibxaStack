<?php

declare(strict_types=1);

namespace App\Models;

use Libxa\Atlas\Model;
use Libxa\Atlas\Attributes\Table;
use Libxa\Atlas\Attributes\Fillable;
use Libxa\Atlas\Attributes\Hidden;
use Libxa\Auth\Authenticatable;
use Libxa\Auth\HasApiTokens;

/**
 * User Model
 */
#[Table('users')]
class User extends Model implements Authenticatable
{
    use HasApiTokens;

    protected array $fillable = ['name', 'email', 'password', 'remember_token', 'email_verified_at'];
    protected array $guarded  = ['id'];
    protected array $hidden   = ['password', 'remember_token'];
    protected array $casts    = [
        'email_verified_at' => 'datetime',
    ];
    protected bool  $timestamps = true;

    // ─────────────────────────────────────────────────────────────────
    //  Authenticatable contract
    // ─────────────────────────────────────────────────────────────────

    public function getAuthIdentifier(): mixed
    {
        return $this->attributes['id'] ?? null;
    }

    public function getAuthPassword(): string
    {
        return $this->attributes['password'] ?? '';
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function getRememberToken(): ?string
    {
        return $this->attributes['remember_token'] ?? null;
    }

    public function setRememberToken(string $token): void
    {
        $this->attributes['remember_token'] = $token;
    }

    // ─────────────────────────────────────────────────────────────────
    //  Mutators
    // ─────────────────────────────────────────────────────────────────

    public function setPasswordAttribute(string $value): void
    {
        // Auto-hash plain-text passwords
        if (! str_starts_with($value, '$2y$') && ! str_starts_with($value, '$argon')) {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
        $this->attributes['password'] = $value;
    }
}

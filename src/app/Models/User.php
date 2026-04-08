<?php

declare(strict_types=1);

namespace App\Models;

use Libxa\Auth\Authenticatable;
use Libxa\Auth\HasApiTokens;

/**
 * User Model
 */
class User implements Authenticatable
{
    use HasApiTokens;

    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

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

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}

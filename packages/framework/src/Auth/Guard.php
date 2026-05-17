<?php

declare(strict_types=1);

namespace Gemriser\Auth;

use Illuminate\Database\Eloquent\Model;

class Guard
{
    private ?Model $user = null;
    private bool $loggedOut = false;

    public function __construct(private string $modelClass)
    {
    }

    public function check(): bool
    {
        return $this->id() !== null;
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): ?Model
    {
        if ($this->loggedOut) {
            return null;
        }

        if ($this->user !== null) {
            return $this->user;
        }

        $id = $_SESSION['auth_id'] ?? null;
        if ($id === null) {
            return null;
        }

        $this->user = $this->modelClass::find($id);
        return $this->user;
    }

    public function id(): mixed
    {
        return $_SESSION['auth_id'] ?? null;
    }

    public function attempt(array $credentials, bool $remember = false): bool
    {
        $user = $this->modelClass::where('email', $credentials['email'] ?? '')->first();

        if (!$user || !app('hash')->check($credentials['password'] ?? '', $user->getAuthPassword())) {
            return false;
        }

        $this->login($user, $remember);
        return true;
    }

    public function login(Model $user, bool $remember = false): void
    {
        session_regenerate_id(true);
        $_SESSION['auth_id'] = $user->getAuthIdentifier();
        $this->user = $user;
        $this->loggedOut = false;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->user = null;
        $this->loggedOut = true;
    }

    public function viaRemember(): bool
    {
        return false;
    }
}

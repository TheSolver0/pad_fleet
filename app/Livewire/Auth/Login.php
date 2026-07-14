<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuditLogger;
use App\Services\LoginRateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    public string $identifier = '';
    public string $password = '';
    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:80'],
            'password'   => ['required', 'string'],
            'remember'   => ['boolean'],
        ];
    }

    protected $messages = [
        'identifier.required' => 'Le matricule ou l\'email est requis.',
        'password.required'   => 'Le mot de passe est requis.',
    ];

    public function getRateLimiter(): LoginRateLimiter
    {
        return LoginRateLimiter::forIp(request()->ip());
    }

    public function getLockedUntilMessageProperty(): ?string
    {
        $limiter = $this->getRateLimiter();
        if (!$limiter->isLocked()) {
            return null;
        }
        return 'Trop de tentatives. Réessayez dans ' . $limiter->formatRemainingTime() . '.';
    }

    public function login(): mixed
    {
        $limiter = $this->getRateLimiter();

        if ($limiter->isLocked()) {
            $this->addError('identifier', $this->getLockedUntilMessageProperty());
            return null;
        }

        $this->validate();

        $user = User::where('matricule', $this->identifier)
            ->orWhere('email', $this->identifier)
            ->first();

        if ($user?->isLocked()) {
            $this->addError('identifier', 'Ce compte est temporairement suspendu. Contactez l\'administrateur.');
            return null;
        }

        if ($user && !$user->is_active) {
            $this->addError('identifier', 'Ce compte est désactivé. Contactez l\'administrateur.');
            return null;
        }

        if (!$user || !Hash::check($this->password, $user->password)) {
            $limiter->recordFailedAttempt();
            AuditLogger::logLoginFailed($this->identifier, 'Matricule/email ou mot de passe incorrect.');
            $this->addError('identifier', 'Matricule/email ou mot de passe incorrect.');
            return null;
        }

        $limiter->clear();
        Auth::login($user, $this->remember);
        AuditLogger::logLogin($user);

        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}

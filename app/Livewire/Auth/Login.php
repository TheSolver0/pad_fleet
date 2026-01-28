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
    public string $matricule = '';
    public string $password = '';
    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'matricule' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    protected $messages = [
        'matricule.required' => 'Le matricule est requis.',
        'password.required' => 'Le mot de passe est requis.',
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
            $this->addError('matricule', $this->getLockedUntilMessageProperty());
            return null;
        }

        $this->validate();

        $user = User::where('matricule', $this->matricule)->first();

        if ($user?->isLocked()) {
            $this->addError('matricule', 'Ce compte est temporairement suspendu. Contactez l\'administrateur.');
            return null;
        }

        if (!$user || !Hash::check($this->password, $user->password)) {
            $limiter->recordFailedAttempt();
            AuditLogger::logLoginFailed($this->matricule, 'Matricule ou mot de passe incorrect.');
            $this->addError('matricule', 'Matricule ou mot de passe incorrect.');
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

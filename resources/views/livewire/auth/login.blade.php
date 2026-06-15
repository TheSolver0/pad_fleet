<div class="login-block">
    <div class="login-left" style="background-image: url('{{ asset('img/banner.png') }}');">
        <div class="login-left-overlay"></div>
        <div class="login-left-content">
            <h2>Gestion de flotte</h2>
            <p>Port Autonome de Douala</p>
        </div>
    </div>
    <div class="login-right">
        <div class="login-form-wrap">
            <div class="login-logo">
                <img src="{{ asset('img/logo.png') }}" alt="PAD" class="login-logo-img">
                <h1>PAD Fleet</h1>
                <p class="sub">Connexion à votre espace</p>
            </div>

            @if ($this->locked_until_message)
                <div class="alert alert-warning mb-3 py-3" role="alert">
                    <i class="bi bi-clock-history me-2"></i>
                    {{ $this->locked_until_message }}
                </div>
            @endif

            <form wire:submit="login">
                <div class="mb-3">
                    <label class="form-label" for="identifier">Matricule ou Email</label>
                    <input type="text"
                           class="form-control @error('identifier') is-invalid @enderror"
                           id="identifier"
                           wire:model="identifier"
                           placeholder="Matricule ou adresse email"
                           autocomplete="username"
                           autofocus>
                    @error('identifier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" x-data="{ showPassword: false }">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="password-input-wrap">
                        <input :type="showPassword ? 'text' : 'password'"
                               class="form-control pe-5 @error('password') is-invalid @enderror"
                               id="password"
                               wire:model="password"
                               placeholder="••••••••"
                               autocomplete="current-password">
                        <button type="button"
                                class="password-toggle-btn"
                                tabindex="-1"
                                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                                @click="showPassword = !showPassword">
                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" wire:model="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
                <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="login">Se connecter</span>
                    <span wire:loading wire:target="login">Connexion…</span>
                </button>
            </form>
            <p class="login-footer-text">
                <a href="#">Mot de passe oublié ?</a>
            </p>
        </div>
    </div>
</div>

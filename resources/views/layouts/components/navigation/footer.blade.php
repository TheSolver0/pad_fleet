{{-- Pied de page optionnel pour la zone contenu --}}
<footer class="py-3 mt-auto border-top border-light text-muted small text-center d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
    <img src="{{ asset('img/logo.png') }}" alt="PAD" class="footer-logo">
    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
</footer>

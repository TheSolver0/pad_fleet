<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PAD Fleet') }} — Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pad-blue: #1a5490;
            --pad-cyan: #00b8d4;
            --pad-yellow: #b8d400;
            --page-bg: #f0f4f8;
            --card-bg: #ffffff;
            --text-primary: #2d3748;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            margin: 0;
            min-height: 100vh;
            background: var(--page-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .login-block {
            width: 80%;
            max-width: 960px;
            min-height: 75vh;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(26,84,144,0.08);
            overflow: hidden;
            display: flex;
        }
        @media (max-width: 768px) {
            .login-block { width: 95%; flex-direction: column; min-height: auto; }
        }
        .login-left {
            flex: 1;
            min-height: 400px;
            background: linear-gradient(145deg, var(--pad-blue) 0%, #0d3a66 50%, rgba(0,184,212,0.4) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
        }
        .login-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }
        .login-left-content { position: relative; z-index: 1; text-align: center; color: #fff; }
        .login-left-content .icon-wrap {
            width: 100px; height: 100px; margin: 0 auto 1.5rem;
            display: flex; align-items: center; justify-content: center;
        }
        .login-left-content .login-left-logo {
            max-width: 100%; max-height: 100%; object-fit: contain;
        }
        .login-left-content h2 { font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem; }
        .login-left-content p { font-size: 0.95rem; opacity: 0.9; }
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }
        .login-form-wrap { width: 100%; max-width: 340px; }
        .login-logo { text-align: center; margin-bottom: 2rem; }
        .login-logo img.login-logo-img { max-height: 72px; width: auto; }
        .login-logo-placeholder {
            width: 72px; height: 72px; margin: 0 auto;
            background: linear-gradient(145deg, var(--pad-cyan), var(--pad-blue));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff;
        }
        .login-logo h1 { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-top: 0.75rem; margin-bottom: 0.25rem; }
        .login-logo .sub { font-size: 0.85rem; color: var(--text-muted); }
        .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
        .form-control {
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: var(--pad-cyan);
            box-shadow: 0 0 0 3px rgba(0,184,212,0.15);
        }
        .form-control.is-invalid { border-color: #dc3545; }
        .btn-login {
            width: 100%;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 12px;
            background: linear-gradient(145deg, var(--pad-cyan), var(--pad-blue));
            border: none;
            color: #fff;
            transition: transform .2s, box-shadow .2s;
        }
        .btn-login:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,184,212,0.35); }
        .btn-login:disabled { opacity: 0.8; }
        .login-footer-text { text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 1.5rem; }
        .login-footer-text a { color: var(--pad-blue); text-decoration: none; font-weight: 500; }
        .invalid-feedback { font-size: 0.85rem; }
        .password-input-wrap { position: relative; }
        .password-toggle-btn {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.35rem 0.5rem;
            border: none;
            background: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.2rem;
            line-height: 1;
        }
        .password-toggle-btn:hover { color: var(--pad-cyan); }
    </style>
    @livewireStyles
</head>
<body>
    {{ $slot }}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>

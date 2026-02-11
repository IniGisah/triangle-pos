<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Login | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI / App CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        /* Inline styles to keep changes within Blade only */
        body.login-bg {
            background: linear-gradient(135deg,#0f172a 0%, #0b1220 50%, #081028 100%);
            color: #eef2ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-attachment: fixed;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255,255,255,0.04);
            box-shadow: 0 10px 30px rgba(2,6,23,0.6);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .login-card .card-body { padding: 2.25rem; }
        .brand-logo { width: 92px; height: 92px; object-fit: contain; }
        .brand-area { text-align: center; margin-bottom: 1rem; }
        .form-heading { color: #e6eef8; margin-bottom: .25rem; }
        .form-sub { color: rgba(230,238,248,0.7); margin-bottom: 1rem; }
        .social-btn { width: 48%; }
        @media (max-width: 576px) {
            .login-card { margin: 1rem; }
        }
    </style>
</head>

<body class="login-bg c-app">

<div class="login-card">
    <div class="card-body">
        <div class="brand-area">
            <img src="{{ asset('images/logo-dark.png') }}" alt="Logo" class="brand-logo mb-2">
            <div><strong>{{ config('app.name') }}</strong></div>
        </div>

        @if(Session::has('account_deactivated'))
            <div class="alert alert-danger" role="alert">
                {{ Session::get('account_deactivated') }}
            </div>
        @endif

        <h4 class="form-heading">{{ __('auth.login') }}</h4>
        <p class="form-sub">{{ __('auth.sign_in_to_continue_to') }} {{ config('app.name') }}</p>

        <form id="login" method="post" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label visually-hidden">{{ __('auth.email') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    </div>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus aria-label="Email">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label visually-hidden">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    </div>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" required aria-label="Password">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">{{ __('auth.remember_me') }}</label>
                </div>
                <div>
                    <a class="text-decoration-none text-light" href="{{ route('password.request') }}">{{ __('auth.forgot_your_password') }}</a>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <button id="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center" type="submit">
                    <span>Sign in</span>
                    <span id="spinner" class="spinner-border text-light ms-2" role="status" style="height: 20px;width: 20px;display: none;">
                        <span class="sr-only">Loading...</span>
                    </span>
                </button>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted"><a href="{{ route('register') }}" class="text-decoration-underline text-light">Belum punya akun ? Register</a></small>
                <!-- <div class="d-flex justify-content-center gap-2 mt-2">
                    <button type="button" class="btn btn-outline-light social-btn"><i class="bi bi-google"></i></button>
                    <button type="button" class="btn btn-outline-light social-btn"><i class="bi bi-github"></i></button>
                </div> -->
            </div>

        </form>

    </div>
</div>

<!-- CoreUI / App JS -->
<script src="{{ mix('js/app.js') }}" defer></script>
<script>
    // Preserve existing submit UX (disable & show spinner)
    document.addEventListener('DOMContentLoaded', function () {
        let login = document.getElementById('login');
        let submit = document.getElementById('submit');
        let email = document.getElementById('email');
        let password = document.getElementById('password');
        let spinner = document.getElementById('spinner');

        if (!login) return;

        login.addEventListener('submit', (e) => {
            submit.disabled = true;
            email.readOnly = true;
            password.readOnly = true;
            spinner.style.display = 'inline-block';
        });

        // Fallback in case login fails quickly
        setTimeout(() => {
            if (submit) submit.disabled = false;
            if (email) email.readOnly = false;
            if (password) password.readOnly = false;
            if (spinner) spinner.style.display = 'none';
        }, 3000);
    });
</script>

</body>
</html>

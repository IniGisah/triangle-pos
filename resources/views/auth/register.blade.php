<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Register | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI / App CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        /* Reuse login styles */
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
            max-width: 480px;
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

        <h4 class="form-heading">Create an account</h4>
        <p class="form-sub">Start your account with {{ config('app.name') }}</p>

        <form id="register" method="post" action="{{ url('/register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label visually-hidden">Nama Lengkap</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                    </div>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Full name" required autofocus aria-label="Full name">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label visually-hidden">Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    </div>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required aria-label="Email">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label visually-hidden">Kata Sandi</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    </div>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Kata Sandi" required aria-label="Kata Sandi">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label visually-hidden">Konfirmasi Kata Sandi</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    </div>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Confirm password" required aria-label="Confirm password">
                </div>
            </div>

            <div class="d-grid">
                <button id="submit" type="submit" class="btn btn-primary d-flex justify-content-center align-items-center">
                    <span>Buat akun</span>
                    <span id="spinner" class="spinner-border text-light ms-2" role="status" style="height: 20px;width: 20px;display: none;">
                        <span class="sr-only">Loading...</span>
                    </span>
                </button>
            </div>

            <!-- <div class="text-center mt-3">
                <small class="text-muted">or continue with</small>
                <div class="d-flex justify-content-center gap-2 mt-2">
                    <button type="button" class="btn btn-outline-light social-btn"><i class="bi bi-google"></i></button>
                    <button type="button" class="btn btn-outline-light social-btn"><i class="bi bi-github"></i></button>
                </div>
            </div> -->

            <div class="text-center mt-3">
                <small class="text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-underline text-light">Sign in</a></small>
            </div>

        </form>

    </div>
</div>

<!-- CoreUI / App JS -->
<script src="{{ mix('js/app.js') }}" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let form = document.getElementById('register');
        let submit = document.getElementById('submit');
        let name = document.getElementById('name');
        let email = document.getElementById('email');
        let password = document.getElementById('password');
        let spinner = document.getElementById('spinner');

        if (!form) return;

        form.addEventListener('submit', function () {
            if (submit) submit.disabled = true;
            if (name) name.readOnly = true;
            if (email) email.readOnly = true;
            if (password) password.readOnly = true;
            if (spinner) spinner.style.display = 'inline-block';
        });

        setTimeout(() => {
            if (submit) submit.disabled = false;
            if (name) name.readOnly = false;
            if (email) email.readOnly = false;
            if (password) password.readOnly = false;
            if (spinner) spinner.style.display = 'none';
        }, 3000);
    });
</script>

</body>
</html>

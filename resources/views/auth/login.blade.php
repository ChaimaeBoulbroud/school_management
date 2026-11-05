<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - {{ config('app.name', 'Institution Ettefl') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-image: url("{{ asset('images/background2.webp') }}");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            overflow-x: hidden;
        }

        .login-form-container {
            background-color: transparent;
            padding: 20px;
            max-width: 420px;
            width: 90%;
            text-align: center;
        }

        .institution-logo-area {
            margin-bottom: 1rem;
        }

        .institution-logo-img {
            width: 420px;
            height: auto;
            margin-bottom: 0.5rem;
        }

        .form-section {
            text-align: left;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #004A7F;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #004A7F;
            font-size: 0.85rem;
            display: block;
        }

        .form-control-custom {
            border-radius: 50rem;
            padding: 0.85rem 1.5rem;
            padding-right: 3.5rem;
            border: none;
            background-color: #EBF3FA;
            color: #2D3748;
            font-size: 0.95rem;
            width: 100%;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
        }

        .form-control-custom::placeholder {
            color: #5C8BAA;
        }

        .form-control-custom:focus {
            background-color: #E0ECF6;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.06), 0 0 0 0.2rem rgba(0, 74, 127, 0.25);
            outline: none;
        }

        .btn-login-custom {
            background-color: #004A7F;
            color: white;
            border-radius: 50rem;
            padding: 0.9rem 1.5rem;
            font-weight: 600;
            transition: background-color 0.2s;
            width: 100%;
            font-size: 1rem;
            border: none;
        }

        .btn-login-custom:hover {
            background-color: #003A65;
        }

        .invalid-feedback {
            color: #D93025;
            font-size: 0.875em;
            display: block;
            margin-top: 0.25rem;
            text-align: left;
        }

        .position-relative { position: relative; }
        .password-toggle-icon {
            position: absolute;
            top: 50%;
            right: 1.25rem;
            transform: translateY(-50%);
            cursor: pointer;
            margin-top: 0.1rem;
        }
        .password-toggle-icon .bi {
            font-size: 1.2rem;
            color: #004A7F;
        }

        @media (max-width: 480px) {
            .login-form-container {
                padding: 10px;
                max-width: 95%;
            }
            .institution-logo-img {
                width: 80px;
            }
            .form-title {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            .form-control-custom, .btn-login-custom {
                font-size: 0.9rem;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-form-container">
        <div class="institution-logo-area">
            <img src="{{ asset('images/ettefl_login_logo.png') }}" alt="Institution Ettefl Logo" class="institution-logo-img">
        </div>
        <div class="form-section">
            <h2 class="form-title">Connexion</h2>

            <form method="POST" action="{{ route('login.perform') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger text-center small p-2 mb-3" role="alert">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email"
                        class="form-control form-control-custom @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="admin@ofppt.ma">
                    @error('email')
                        {{-- Inline error removed; all errors shown above --}}
                    @enderror
                </div>

                <div class="mb-4 position-relative">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password"
                        class="form-control form-control-custom @error('password') is-invalid @enderror"
                        required autocomplete="current-password"
                        placeholder="••••••••">
                    <span class="password-toggle-icon" onclick="togglePassword()">
                        <i id="eyeIcon" class="bi bi-eye-slash"></i>
                    </span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-login-custom">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        }
    </script>
</body>
</html>
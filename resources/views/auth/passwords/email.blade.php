<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Contraseña - IPS Milagroz</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    
    <!-- Bootstrap (from AdminLTE) -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    <style>
        :root {
            --primary-orange: #e67e22;
            --primary-orange-hover: #d35400;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            animation: fadeInUp 0.5s ease-out forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
        }

        .card-header {
            background-color: transparent;
            border-bottom: none;
            text-align: center;
            padding: 30px 30px 10px;
        }

        .login-logo img {
            max-width: 120px;
            margin-bottom: 20px;
        }

        .login-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #102a43;
            margin-bottom: 5px;
        }

        .login-subtitle {
            font-size: 0.85rem;
            color: #627d98;
        }

        .card-body {
            padding: 20px 30px 30px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px 12px 45px;
            border: 1px solid #d9e2ec;
            background-color: #f0f4f8;
            height: auto;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(230, 126, 34, 0.15);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #829ab1;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .btn-modern {
            background-color: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn-modern:hover {
            background-color: var(--primary-orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
            color: white;
        }

        .back-to-login {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #627d98;
            text-decoration: none;
        }

        .back-to-login:hover {
            color: var(--primary-orange);
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="card">
        <div class="card-header">
            <div class="login-logo">
                <img src="{{ asset('images/logo_ips.jpg') }}" alt="Logo IPS Milagroz">
            </div>
            <h1 class="login-title">Recuperar Contraseña</h1>
            <p class="login-subtitle">Enviaremos un enlace de restauración a su correo</p>
        </div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group position-relative">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                           placeholder="Correo institucional">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-modern">
                    ENVIAR ENLACE
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-to-login">
                <i class="fas fa-arrow-left mr-1"></i> Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>

</body>
</html>
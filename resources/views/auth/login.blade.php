<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma de Gestión Humana - IPS</title>

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
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08), 0 4px 6px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background-color: #ffffff;
        }

        .card-header {
            background-color: transparent;
            border-bottom: none;
            text-align: center;
            padding: 30px 30px 10px;
        }

        .login-logo {
            margin-bottom: 20px;
        }

        .login-logo img {
            max-width: 140px;
            height: auto;
            border-radius: 8px; /* Si el logo tiene bordes, un sutil redondeo ayuda */
        }

        .login-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #102a43;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: #627d98;
            font-weight: 400;
        }

        .card-body {
            padding: 20px 30px 30px;
        }

        /* Modern Inputs */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px 12px 45px;
            border: 1px solid #d9e2ec;
            background-color: #f0f4f8;
            height: auto;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: #334e68;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(230, 126, 34, 0.15);
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #829ab1;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .form-control:focus + .input-icon, 
        .form-control:focus ~ .input-icon { /* Depending on DOM order */
            color: var(--primary-orange);
        }

        /* Button Modernization */
        .btn-modern {
            background-color: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-modern:hover {
            background-color: var(--primary-orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
            color: white;
        }

        .btn-modern:active {
            transform: translateY(0);
        }

        /* Checkbox & Links */
        .custom-control-label::before {
            border-radius: 4px;
            background-color: #f0f4f8;
            border: 1px solid #d9e2ec;
        }
        
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
        }

        .text-sm-link {
            font-size: 0.85rem;
            color: #627d98;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .text-sm-link:hover {
            color: var(--primary-orange);
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="card">
        
        <!-- Encabezado -->
        <div class="card-header">
            <div class="login-logo">
                <img src="{{ asset('images/logo_ips.jpg') }}" alt="Logo IPS Milagroz">
            </div>
            <h1 class="login-title">Plataforma de Gestión Humana</h1>
            <p class="login-subtitle">Ingrese sus credenciales para acceder</p>
        </div>

        <!-- Formulario -->
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Campo Email -->
                <div class="form-group">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Correo electrónico institucional">
                    <i class="fas fa-envelope input-icon"></i>
                    
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div class="form-group">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="Contraseña">
                    <i class="fas fa-lock input-icon"></i>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="remember" class="custom-control-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label text-sm-link" for="remember">Recuérdame</label>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        @if (Route::has('password.request'))
                            <a class="text-sm-link" href="{{ route('password.request') }}">
                                ¿Olvidó su contraseña?
                            </a>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-modern">
                    INGRESAR
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
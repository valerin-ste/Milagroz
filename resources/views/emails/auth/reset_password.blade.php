<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; margin-top: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #f0f0f0; }
        .header img { max-width: 150px; height: auto; }
        .content { padding: 40px; color: #4a5568; line-height: 1.6; }
        .content h1 { color: #1a202c; font-size: 22px; margin-bottom: 20px; text-align: center; }
        .content p { font-size: 16px; margin-bottom: 20px; }
        .btn-container { text-align: center; margin: 35px 0; }
        .btn { background-color: #e67e22; color: #ffffff !important; padding: 14px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block; }
        .footer { background-color: #f8fafc; padding: 25px; text-align: center; color: #718096; font-size: 13px; }
        .divider { height: 1px; background-color: #edf2f7; margin: 20px 0; }
        .small-text { font-size: 12px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Usamos una URL absoluta para el logo si está disponible públicamente, 
                 o confiamos en que el cliente de correo lo cargue si es relativo y se configura CID -->
            <img src="{{ asset('images/logo_ips.jpg') }}" alt="IPS Milagroz">
        </div>
        <div class="content">
            <h1>Restablecimiento de Contraseña</h1>
            <p>Hola, <strong>{{ $name }}</strong>.</p>
            <p>Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta en la <strong>Plataforma de Gestión Humana de IPS Milagroz</strong>.</p>
            
            <div class="btn-container">
                <a href="{{ $url }}" class="btn">Restablecer Contraseña</a>
            </div>

            <p>Este enlace de restablecimiento de contraseña expirará en 60 minutos.</p>
            <p>Si no realizaste esta solicitud, puedes ignorar este mensaje de forma segura; no se realizarán cambios en tu cuenta.</p>
            
            <div class="divider"></div>
            
            <p class="small-text">
                Si tienes problemas para hacer clic en el botón, copia y pega la siguiente URL en tu navegador: <br>
                <a href="{{ $url }}" style="color: #e67e22;">{{ $url }}</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} IPS Milagroz. Todos los derechos reservados.<br>
            Este es un mensaje automático, por favor no responda a este correo.
        </div>
    </div>
</body>
</html>

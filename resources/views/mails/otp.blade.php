<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código OTP</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>
    <p>Tu código OTP es: <strong>{{ $otp }}</strong></p>
    <p>Este código expirará en 5 minutos.</p>
</body>
</html>

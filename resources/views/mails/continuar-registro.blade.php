<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;">

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #edf2f7; margin: 0; padding: 0; width: 100%;">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td class="header" style="padding: 25px 0; text-align: center;">
                        <a href="{{ url('/') }}" style="color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none;">
                            Aula Virtual
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td class="body" width="100%" style="background-color: #edf2f7; border-top: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7;">
                        <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #ffffff; border: 1px solid #e8e5ef; border-radius: 2px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto;">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell" style="padding: 32px;">
                                    <h1 style="color: #3d4852; font-size: 18px; font-weight: bold;">Hola,</h1>
                                    <p style="font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 700; color: #ff5850;">Estudiante - {{ $usuario }}</p>
                                    <p style="font-size: 16px; line-height: 1.5em;">
                                        Recibes este correo porque iniciaste el proceso de registro en nuestro sistema, hemos verificada su matricula activa, pero aún no ha completado el registro.
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.5em;">
                                        Para continuar con tu registro, por favor haz clic en el botón a continuación:
                                    </p>

                                    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 30px auto; text-align: center;">
                                        <tr>
                                            <td align="center">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                    <tr>
                                                        <td style="background-color: #7367f0; border-radius: 4px;">
                                                            <a href="{{ $url }}" target="_blank" rel="noopener" style="display: block; padding: 16px 24px; font-weight: 600; font-size: 14px; color: #ffffff; text-decoration: none;">
                                                                Continuar Registro
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="font-size: 16px; line-height: 1.5em;">Este enlace expirará en {{ config('auth.verification.expire', 2) }} minutos.</p>
                                    <p style="font-size: 16px; line-height: 1.5em;">Si no solicitaste continuar con el registro, puedes ignorar este mensaje.</p>

                                    <p style="font-size: 16px; line-height: 1.5em;">Saludos,<br>{{ $app_name }}</p>

                                    <table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;">
                                        <tr>
                                            <td>
                                                <p style="font-size: 14px; line-height: 1.5em;">
                                                    Si tienes problemas para hacer clic en el botón "Continuar Registro", copia y pega la URL a continuación en tu navegador web:<br>
                                                    <a href="{{ $url }}" style="color: #3869d4;">{{ $url }}</a>
                                                </p>
                                                <p style="font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; color: #ff5850;">Por su seguridad, por favor no comparta este enlace con nadie.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td class="content-cell" align="center" style="padding: 32px;">
                                    <p style="color: #b0adc5; font-size: 12px;">© {{ date('Y') }} Todos los derechos reservados.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light" />
    <meta name="supported-color-schemes" content="light" />
    <title>Recupera tu contraseña - Nexa</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #F3F4F6; margin: 0; padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        @@media only screen and (max-width: 600px) {
            .wrapper { padding: 24px 12px !important; }
            .header { padding: 24px 20px !important; }
            .body-cell { padding: 28px 20px !important; }
            .footer { padding: 24px 20px !important; }
        }
    </style>
</head>
<body style="font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background-color:#F3F4F6;margin:0;padding:0;-webkit-font-smoothing:antialiased;">
    <table class="wrapper" role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F3F4F6;padding:48px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 4px 20px rgba(0,0,0,0.04);border-radius:20px;">

                    {{-- Header --}}
                    <tr>
                        <td class="header" style="background-color:#C92E4B;background-image:radial-gradient(circle at 20px 20px,rgba(255,255,255,0.08) 2px,transparent 2px),linear-gradient(135deg,#E8375A 0%,#C92E4B 100%);background-size:24px 24px,100%;padding:36px 40px;text-align:center;border-radius:20px 20px 0 0;">
                            <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa" style="width:120px;height:auto;">
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td class="body-cell" style="background:#FFFFFF;padding:40px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">

                                {{-- Lock icon --}}
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background:#FEF2F3;border-radius:50%;width:64px;height:64px;text-align:center;vertical-align:middle;line-height:64px;">
                                                    <span style="font-size:26px;line-height:1;">🔐</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- Title --}}
                                <tr>
                                    <td style="text-align:center;padding:0 10px 20px;">
                                        <h1 style="color:#111827;font-size:26px;font-weight:800;margin:0 0 10px;letter-spacing:-0.5px;">Recupera tu contraseña</h1>
                                        <p style="color:#6B7280;font-size:16px;line-height:1.7;margin:0;text-align:center;">
                                            Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong style="color:#111827;">Nexa</strong>. Haz clic en el botón de abajo para continuar.
                                        </p>
                                    </td>
                                </tr>

                                {{-- CTA Button --}}
                                <tr>
                                    <td align="center" style="padding:32px 0 24px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="border-radius:50px;background:linear-gradient(135deg,#E8375A 0%,#C92E4B 100%);box-shadow:0 4px 16px rgba(232,55,90,0.35);">
                                                    <a href="{{ $url }}" style="display:inline-block;padding:15px 38px;color:#FFFFFF !important;text-decoration:none;font-weight:700;font-size:15px;border-radius:50px;letter-spacing:0.3px;">Restablecer contraseña</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- Expiration --}}
                                <tr>
                                    <td align="center" style="padding-bottom:28px;">
                                        <p style="color:#9CA3AF;font-size:13px;margin:0;">⏳ Este enlace expirará en <strong style="color:#6B7280;">{{ $count }} minutos</strong></p>
                                    </td>
                                </tr>

                                {{-- Gradient divider --}}
                                <tr>
                                    <td style="padding-bottom:28px;">
                                        <div style="height:1px;background:linear-gradient(to right,transparent,#E5E7EB,transparent);"></div>
                                    </td>
                                </tr>

                                {{-- Security tip card --}}
                                <tr>
                                    <td style="padding-bottom:24px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#F9FAFB;border-radius:12px;">
                                            <tr>
                                                <td style="padding:20px;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                                        <tr>
                                                            <td width="32" valign="top" style="padding:0 12px 0 0;">
                                                                <span style="font-size:18px;">🛡️</span>
                                                            </td>
                                                            <td valign="top">
                                                                <p style="margin:0;color:#6B7280;font-size:13px;line-height:1.6;">
                                                                    <strong style="color:#374151;display:block;margin-bottom:2px;">¿No solicitaste este cambio?</strong>
                                                                    Si no fuiste tú, puedes ignorar este mensaje con tranquilidad. Tu contraseña actual sigue siendo segura.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- Fallback link --}}
                                <tr>
                                    <td align="center">
                                        <p style="color:#9CA3AF;font-size:12px;line-height:1.6;margin:0;">
                                            ¿El botón no funciona? Copia este enlace en tu navegador:<br>
                                            <a href="{{ $url }}" style="color:#E8375A;word-break:break-all;font-size:12px;">{{ $url }}</a>
                                        </p>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td class="footer" style="background:#FFFFFF;border-top:1px solid #F3F4F6;padding:28px 40px;text-align:center;border-radius:0 0 20px 20px;">
                            <p style="font-size:13px;color:#9CA3AF;margin:0 0 6px;">© {{ date('Y') }} Nexa. Todos los derechos reservados.</p>
                            <p style="font-size:13px;color:#9CA3AF;margin:0;">¿Necesitas ayuda? <a href="#" style="color:#E8375A;text-decoration:none;font-weight:600;">Contacta con soporte</a></p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

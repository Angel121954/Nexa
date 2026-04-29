@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        <tr>
            <td class="header" style="background: linear-gradient(135deg, #E8375A 0%, #C92E4B 100%); padding: 30px 40px; text-align: center; border-radius: 16px 16px 0 0;">
                <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa" style="width: 120px; height: auto;">
            </td>
        </tr>
    @endslot

    {{-- Body --}}
    <h1 style="color: #111827; font-size: 24px; font-weight: 700; margin-bottom: 16px;">
        Recupera tu contraseña
    </h1>

    <p style="margin-bottom: 16px; color: #374151; font-size: 16px; line-height: 1.6;">
        Hola, has solicitado restablecer tu contraseña en <strong>Nexa</strong>.
    </p>

    <p style="margin-bottom: 24px; color: #374151; font-size: 16px; line-height: 1.6;">
        Haz clic en el siguiente botón para crear una nueva contraseña:
    </p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="border-radius: 8px; background: linear-gradient(135deg, #E8375A 0%, #C92E4B 100%); box-shadow: 0 4px 12px rgba(232, 55, 90, 0.3);">
                            <a href="{{ $url }}" class="button" style="display: inline-block; padding: 14px 32px; color: #FFFFFF !important; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px;">
                                Restablecer contraseña
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin-top: 24px; margin-bottom: 16px; color: #6B7280; font-size: 14px; line-height: 1.6;">
        Este enlace expirará en <strong>{{ $count }} minutos</strong>.
    </p>

    <div class="divider" style="height: 1px; background: #E5E7EB; margin: 30px 0;"></div>

    <p style="margin-bottom: 16px; color: #6B7280; font-size: 14px; line-height: 1.6;">
        Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña seguirá siendo la misma.
    </p>

    <p style="margin-bottom: 0; color: #9CA3AF; font-size: 14px; line-height: 1.6;">
        Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
        <span style="color: #E8375A; word-break: break-all;">{{ $url }}</span>
    </p>

    {{-- Footer --}}
    @slot('footer')
        <tr>
            <td class="footer" style="background: #FFFFFF; border: 1px solid #E5E7EB; border-top: none; border-radius: 0 0 16px 16px; padding: 30px 40px; text-align: center;">
                <p style="font-size: 14px; color: #9CA3AF; margin-bottom: 8px;">
                    © {{ date('Y') }} Nexa. Todos los derechos reservados.
                </p>
                <p style="font-size: 14px; color: #9CA3AF; margin-bottom: 0;">
                    ¿Necesitas ayuda? <a href="#" style="color: #E8375A; text-decoration: none;">Contáctanos</a>
                </p>
            </td>
        </tr>
    @endslot
@endcomponent

<x-mail::message>
# Recuperacion de contrasena

Has solicitado restablecer tu contrasena. Usa el siguiente codigo para continuar:

<x-mail::panel>
# {{ $code }}
</x-mail::panel>

Este codigo expira en 15 minutos.

Si no solicitaste este cambio, puedes ignorar este correo.

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>

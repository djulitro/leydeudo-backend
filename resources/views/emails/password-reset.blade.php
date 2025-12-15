@extends('emails.layout')

@section('title', 'Restablecer Contraseña')

@section('header-subtitle', 'Recuperación de Cuenta')

@section('content')
    <h2>Hola, {{ $user->nombre }}</h2>

    <p>
        Recibimos una solicitud para restablecer la contraseña de tu cuenta en {{ config('app.name') }}.
    </p>

    <div class="info-box">
        <p><strong>Información de la solicitud:</strong></p>
        <p style="margin-top: 8px;">
            <strong>Cuenta:</strong> {{ $user->email }}<br>
            <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <p>
        Si solicitaste este cambio, haz clic en el botón de abajo para restablecer tu contraseña:
    </p>

    <div class="button-container">
        <a href="{{ $resetUrl }}" class="button">
            Restablecer mi contraseña
        </a>
    </div>

    <div class="link-box">
        <p><strong>¿No puedes hacer clic en el botón?</strong></p>
        <p>Copia y pega el siguiente enlace en tu navegador:</p>
        <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
    </div>

    <div class="warning">
        <p>
            <strong>⚠️ Importante:</strong><br>
            Este enlace expirará en <strong>1 hora</strong> por seguridad. 
            Si no solicitaste este cambio, puedes ignorar este correo de forma segura. 
            Tu contraseña permanecerá sin cambios.
        </p>
    </div>

    <p style="margin-top: 30px; color: #999999; font-size: 13px;">
        Si tienes problemas o no solicitaste este cambio, contacta inmediatamente con nuestro equipo de soporte.
    </p>
@endsection

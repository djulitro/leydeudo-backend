@extends('emails.layout')

@section('title', 'Bienvenido - Configura tu contraseña')

@section('header-subtitle', 'Configuración de Cuenta')

@section('content')
    <h2>¡Bienvenido, {{ $user->nombre }}!</h2>

    <p>
        Tu cuenta ha sido creada exitosamente en {{ config('app.name') }}. 
        Para comenzar a utilizar el sistema, necesitas configurar tu contraseña.
    </p>

    <div class="info-box">
        <p><strong>Información de tu cuenta:</strong></p>
        <p style="margin-top: 8px;">
            <strong>Nombre:</strong> {{ $user->nombre }} {{ $user->apellidos }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>RUT:</strong> {{ $user->rut }}
        </p>
    </div>

    <p>
        Haz clic en el botón de abajo para configurar tu contraseña y acceder al sistema:
    </p>

    <div class="button-container">
        <a href="{{ $resetUrl }}" class="button">
            Configurar mi contraseña
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
            Este enlace expirará en <strong>7 días</strong> y solo puede ser utilizado una vez. 
            Si no configuraste esta cuenta o no solicitaste este correo, por favor ignóralo.
        </p>
    </div>

    <p style="margin-top: 30px; color: #999999; font-size: 13px;">
        Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar con nuestro equipo de soporte.
    </p>
@endsection

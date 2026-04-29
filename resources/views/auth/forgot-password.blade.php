@extends('layouts.guest')

@section('title', 'Recuperar contraseña — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')

<div class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo">
            <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
        </div>

        {{-- Encabezado --}}
        <h1 class="auth-heading">Recuperar contraseña</h1>
        <p class="auth-subheading">
            Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        {{-- Status --}}
        @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            {{-- Email --}}
            <div class="field">
                <label for="email" class="field-label">Correo electrónico</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="1" y="3" width="14" height="10" rx="2" />
                        <path d="M1 5.5l7 4.5 7-4.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="field-input @error('email') is-invalid @enderror"
                        placeholder="tu@correo.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email" />
                </div>
                @error('email')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Enviar enlace de recuperación
            </button>
        </form>

        {{-- Volver al login --}}
        <p class="auth-footer">
            ¿Recordaste tu contraseña? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </p>

        <p class="auth-copyright">© {{ date('Y') }} Nexa. Todos los derechos reservados.</p>
    </div>
</div>

@endsection

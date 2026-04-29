@extends('layouts.guest')

@section('title', 'Restablecer contraseña — Nexa')

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
        <h1 class="auth-heading">Restablecer contraseña</h1>
        <p class="auth-subheading">
            Ingresa tu nueva contraseña para continuar.
        </p>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            {{-- Token oculto --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email (solo lectura) --}}
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
                        value="{{ old('email', $request->query('email')) }}"
                        required
                        autofocus
                        autocomplete="username"
                        readonly>
                </div>
                @error('email')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Nueva Contraseña --}}
            <div class="field">
                <label for="password" class="field-label">Nueva contraseña</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="7" width="10" height="7" rx="1.5" />
                        <path d="M5 7V5a3 3 0 016 0v2" stroke-linecap="round" />
                    </svg>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="field-input @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password" />
                </div>
                @error('password')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Confirmar Contraseña --}}
            <div class="field">
                <label for="password_confirmation" class="field-label">Confirmar contraseña</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="7" width="10" height="7" rx="1.5" />
                        <path d="M5 7V5a3 3 0 016 0v2" stroke-linecap="round" />
                    </svg>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="field-input"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password" />
                </div>
                @error('password_confirmation')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Restablecer contraseña
            </button>
        </form>

        <p class="auth-footer">
            ¿Recordaste tu contraseña? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </p>

        <p class="auth-copyright">© {{ date('Y') }} Nexa. Todos los derechos reservados.</p>
    </div>
</div>

@endsection

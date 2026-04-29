@extends('layouts.guest')

@section('title', 'Crear cuenta — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo" style="margin-bottom: 1.5rem;">
            <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
        </div>

        <h1 class="auth-heading">Crea tu cuenta ♡</h1>
        <p class="auth-subheading">Únete y conecta con personas nuevas.</p>

        {{-- OAuth --}}
        <a href="{{ route('google.redirect') }}" class="btn btn-social" style="margin-bottom: 0.625rem;">
            <svg width="16" height="16" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
            </svg>
            Continuar con Google
        </a>

        <!-- <a href="{{ route('facebook.redirect') }}" class="btn btn-social btn-facebook">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
            </svg>
            Continuar con Facebook
        </a> -->

        <div class="auth-divider"><span>o continúa con</span></div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="field">
                <label for="name" class="field-label">Nombre completo</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="8" cy="5" r="3" />
                        <path d="M2 14c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke-linecap="round" />
                    </svg>
                    <input type="text" id="name" name="name"
                        class="field-input @error('name') is-invalid @enderror"
                        placeholder="Tu nombre completo"
                        value="{{ old('name') }}" required autofocus autocomplete="name" />
                </div>
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="email" class="field-label">Correo electrónico</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="1" y="3" width="14" height="10" rx="2" />
                        <path d="M1 5.5l7 4.5 7-4.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <input type="email" id="email" name="email"
                        class="field-input @error('email') is-invalid @enderror"
                        placeholder="tu@correo.com"
                        value="{{ old('email') }}" required autocomplete="username" />
                </div>
                @error('email')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="password" class="field-label">Contraseña</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="7" width="10" height="7" rx="1.5" />
                        <path d="M5 7V5a3 3 0 016 0v2" stroke-linecap="round" />
                    </svg>
                    <input type="password" id="password" name="password"
                        class="field-input @error('password') is-invalid @enderror"
                        placeholder="Crea una contraseña" required autocomplete="new-password" />
                    <button type="button" class="pwd-toggle" onclick="togglePwd('password', 'eye1')" aria-label="Mostrar">
                        <svg id="eye1" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" />
                            <circle cx="8" cy="8" r="2" />
                        </svg>
                    </button>
                </div>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="password_confirmation" class="field-label">Confirmar contraseña</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="7" width="10" height="7" rx="1.5" />
                        <path d="M5 7V5a3 3 0 016 0v2" stroke-linecap="round" />
                    </svg>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="field-input @error('password_confirmation') is-invalid @enderror"
                        placeholder="Confirma tu contraseña" required autocomplete="new-password" />
                    <button type="button" class="pwd-toggle" onclick="togglePwd('password_confirmation', 'eye2')" aria-label="Mostrar">
                        <svg id="eye2" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" />
                            <circle cx="8" cy="8" r="2" />
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 0.25rem;">
                Crear cuenta
            </button>
        </form>

        <p class="auth-footer">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
        </p>

        <p class="auth-copyright" style="margin-top: 1rem;">
            Al registrarte aceptas nuestros
            <a href="#" style="color: var(--pink); text-decoration: none; font-weight: 500;">Términos</a>
            y
            <a href="#" style="color: var(--pink); text-decoration: none; font-weight: 500;">Política de Privacidad</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/auth.js') }}"></script>
@endpush
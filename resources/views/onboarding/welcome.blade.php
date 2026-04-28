@extends('layouts.guest')

@section('title', '¡Bienvenido! — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/onboarding/welcome.css') }}">
@endpush

@section('content')
<div class="welcome-page">
    <div class="welcome-card">

        <div class="auth-logo" style="margin-bottom: 1.5rem;">
            <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
        </div>

        <div class="welcome-illustration">🎉</div>

        <h1 class="welcome-title">¡Bienvenido a Nexa!</h1>
        <p class="welcome-text">
            Tu cuenta ha sido creada correctamente.<br>
            Ya puedes explorar la comunidad y conectar con personas.
        </p>

        <a href="{{ route('explore.index') }}" class="btn btn-primary">
            Ir al inicio
        </a>

        <a href="{{ route('explore.index') }}" class="welcome-skip">
            Completar perfil más tarde
        </a>
    </div>
</div>
@endsection
@extends('layouts.guest')

@section('title', 'Verificación en dos pasos — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<style>
    .tab-selector { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
    .tab-selector button {
        flex: 1; padding: 0.625rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 500;
        border: 2px solid #e5e7eb; background: white; cursor: pointer; transition: all 0.2s; color: #6b7280;
    }
    .tab-selector button.active { border-color: #ec4899; color: #ec4899; background: #fdf2f8; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
        </div>

        <h1 class="auth-heading">Verificación en dos pasos</h1>
        <p class="auth-subheading">Ingresa el código de tu aplicación autenticadora o un código de recuperación.</p>

        @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('two-factor.challenge.store') }}">
            @csrf

            <div id="codeTab" class="tab-content-challenge">
                <div class="field">
                    <label for="code" class="field-label">Código de verificación</label>
                    <div class="field-input-wrap">
                        <input type="text" id="code" name="code" class="field-input @error('code') is-invalid @enderror"
                            placeholder="000 000" inputmode="numeric" autocomplete="one-time-code" autofocus>
                    </div>
                    @error('code')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div id="recoveryTab" class="tab-content-challenge" style="display:none">
                <div class="field">
                    <label for="recovery_code" class="field-label">Código de recuperación</label>
                    <div class="field-input-wrap">
                        <input type="text" id="recovery_code" name="recovery_code"
                            class="field-input @error('recovery_code') is-invalid @enderror"
                            placeholder="XXXXX-XXXXX" autocomplete="off">
                    </div>
                    @error('recovery_code')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="tab-selector">
                <button type="button" class="active" data-tab="code" onclick="switchChallengeTab('code')">
                    Código 2FA
                </button>
                <button type="button" data-tab="recovery" onclick="switchChallengeTab('recovery')">
                    Código de recuperación
                </button>
            </div>

            <button type="submit" class="btn btn-primary">
                Verificar
            </button>
        </form>

        <p class="auth-footer" style="margin-top:1.5rem">
            <a href="{{ route('login') }}">Volver al inicio de sesión</a>
        </p>

        <p class="auth-copyright">© {{ date('Y') }} Nexa. Todos los derechos reservados.</p>
    </div>
</div>

@push('scripts')
<script>
function switchChallengeTab(tab) {
    document.querySelectorAll('.tab-content-challenge').forEach(el => el.style.display = 'none');
    document.getElementById(tab + 'Tab').style.display = 'block';
    document.querySelectorAll('.tab-selector button').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    document.getElementById(tab === 'code' ? 'code' : 'recovery_code')?.focus();
}
</script>
@endpush
@endsection

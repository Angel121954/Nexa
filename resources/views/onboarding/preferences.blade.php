@extends('layouts.guest')

@section('title', 'Preferencias — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/onboarding/preferences.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card onboarding-card">

        <div class="auth-logo" style="margin-bottom: 1.25rem;">
            <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
        </div>

        {{-- Stepper --}}
        <div class="stepper">
            @foreach([['1','Cuenta'],['2','Perfil básico'],['3','Foto y galería'],['4','Preferencias']] as $i => $step)
            <div class="step-item {{ $i < 3 ? 'completed' : 'active' }}" style="width: 80px;">
                <div class="step-bubble">
                    @if($i < 3)
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 6l3 3 5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @else
                        {{ $step[0] }}
                        @endif
                </div>
                <span class="step-label">{{ $step[1] }}</span>
            </div>
            @endforeach
        </div>

        <h1 class="auth-heading">Cuéntanos tus preferencias ♡</h1>
        <p class="auth-subheading">Esto nos ayuda a mostrarte contenido relevante.</p>

        <form method="POST" action="{{ route('onboarding.preferences.store') }}">
            @csrf

            {{-- Qué buscas --}}
            <p class="field-label" style="margin-bottom: 0.25rem;">¿Qué estás buscando?</p>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.75rem;">
                Selecciona una o más opciones
            </p>

            <div class="content-options">
                @foreach([
                ['friends',    'Hacer amigos',          'Conocer personas con quienes compartir',  'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
                ['dating',     'Conocer pareja',         'Encontrar a alguien especial',            'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ['networking', 'Ampliar mi círculo',     'Conectar con gente nueva cerca de mí',   'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064'],
                ['activities', 'Planes y actividades',   'Encontrar con quién salir y explorar',   'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
                ] as [$value, $label, $sub, $icon])
                <label class="content-option" id="opt-{{ $value }}" onclick="toggleOption(this, '{{ $value }}')">
                    <div class="content-option-left">
                        <div class="content-option-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="{{ $icon }}" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div>
                            <p class="content-option-text">{{ $label }}</p>
                            <p class="content-option-sub">{{ $sub }}</p>
                        </div>
                    </div>
                    <div class="content-option-check" id="check-{{ $value }}">
                        <svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="white" stroke-width="2" style="display:none;" id="checkmark-{{ $value }}">
                            <path d="M2 6l3 3 5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <input type="checkbox" name="looking_for[]" value="{{ $value }}" style="display:none;" id="input-{{ $value }}">
                </label>
                @endforeach
            </div>

            <div class="onboarding-actions">
                <a href="{{ route('onboarding.photos') }}" class="btn btn-outline">Atrás</a>
                <button type="submit" class="btn btn-primary btn-main">Crear cuenta</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleTag(checkbox) {
        checkbox.closest('.interest-tag').classList.toggle('selected', checkbox.checked);
    }

    function toggleOption(label, value) {
        const input = document.getElementById('input-' + value);
        const check = document.getElementById('check-' + value);
        const checkmark = document.getElementById('checkmark-' + value);
        const selected = !input.checked;

        input.checked = selected;
        label.classList.toggle('selected', selected);
        check.style.background = selected ? 'var(--pink)' : '';
        check.style.borderColor = selected ? 'var(--pink)' : '';
        checkmark.style.display = selected ? 'block' : 'none';
    }
</script>
@endpush
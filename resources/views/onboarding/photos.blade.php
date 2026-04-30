@extends('layouts.guest')

@section('title', 'Foto y galería — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/onboarding/photos.css') }}">
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
            <div class="step-item {{ $i < 2 ? 'completed' : ($i === 2 ? 'active' : '') }}" style="width: 80px;">
                <div class="step-bubble">
                    @if($i < 2)
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

        <h1 class="auth-heading">Agrega tu foto principal ♡</h1>
        <p class="auth-subheading">Esta será tu foto de perfil visible para todos.</p>

        <form method="POST" action="{{ route('onboarding.photos.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Avatar --}}
            <div class="avatar-upload-wrap">
                <div class="avatar-preview">
                    @if(auth()->user()->avatar && !str_starts_with(auth()->user()->avatar, 'http'))
                    <img id="avatar-img" src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar">
                    @elseif(auth()->user()->avatar)
                    <img id="avatar-img" src="{{ auth()->user()->avatar }}" alt="Avatar">
                    @else
                    <div class="avatar-placeholder" id="avatar-placeholder">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke-linecap="round" />
                        </svg>
                    </div>
                    <img id="avatar-img" src="" alt="Avatar" style="display: none; width: 110px; height: 110px; border-radius: 50%; object-fit: cover;">
                    @endif
                    <label for="avatar" class="avatar-edit-btn" title="Cambiar foto">
                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 2l3 3-9 9H2v-3L11 2z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </label>
                </div>
                <input type="file" id="avatar" name="avatar" accept="image/jpg,image/jpeg,image/png"
                    class="@error('avatar') is-invalid @enderror" style="display:none;" required />
                <span class="avatar-hint">Formatos: JPG, PNG. Máx. 5MB</span>
                @error('avatar')<span class="field-error" style="text-align:center;">{{ $message }}</span>@enderror
            </div>

            {{-- Galería --}}
            <p class="gallery-section-title">Galería <span style="font-weight:400;color:var(--text-muted);">(opcional)</span></p>
            <p class="gallery-section-sub">Muestra más de ti. Puedes agregar varias fotos.</p>

            <label for="gallery" class="gallery-dropzone">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="3" />
                    <path d="M12 8v8M8 12h8" stroke-linecap="round" />
                </svg>
                <span>Agregar fotos</span>
                <small>o arrastra y suelta aquí</small>
            </label>
            <input type="file" id="gallery" name="gallery[]" accept="image/jpg,image/jpeg,image/png"
                multiple style="display:none;" onchange="previewGallery(this)" />

            {{-- Fotos existentes --}}
            @if($photos->isNotEmpty())
            <div class="gallery-grid" id="gallery-grid">
                @foreach($photos as $photo)
                <div class="gallery-item" id="photo-{{ $photo->id }}">
                    <img src="{{ str_starts_with($photo->path, 'http') ? $photo->path : Storage::url($photo->path) }}" alt="Foto">
                    <form method="POST" action="{{ route('onboarding.photos.delete', $photo) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="gallery-item-remove" title="Eliminar">×</button>
                    </form>
                </div>
                @endforeach
            </div>
            @else
            <div class="gallery-grid" id="gallery-grid"></div>
            @endif

            <div class="onboarding-actions">
                <a href="{{ route('onboarding.basic') }}" class="btn btn-outline">Atrás</a>
                <button type="submit" class="btn btn-primary btn-main">Continuar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/onboarding.js') }}"></script>
@endpush
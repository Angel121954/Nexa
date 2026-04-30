@extends('layouts.app')

@section('title', 'Mi Perfil — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/explore.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile-redesign.css') }}">
@endpush

@section('content')

<x-topbar />

<div class="prf-page">

    {{-- ══════════ HERO BANNER ══════════ --}}
    <div class="prf-hero">
        @if($profile?->banner)
            <div class="prf-banner" style="background-image:url('{{ $profile->banner }}'); background-size:cover; background-position:center;"></div>
        @else
            <div class="prf-banner"></div>
        @endif

        <div class="prf-hero-content">
            {{-- Avatar --}}
            <div class="prf-avatar-wrap">
                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff&size=300&bold=true' }}"
                     alt="{{ $user->name }}" class="prf-avatar">
                <div class="prf-avatar-cam">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                </div>
            </div>

            {{-- Nombre e info básica --}}
            <div class="prf-identity">
                <h1 class="prf-name">{{ $user->name }}</h1>
                @if($profile?->city || $profile?->age)
                    <div class="prf-meta-row">
                        @if($profile?->city)
                            <span class="prf-meta-item">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke-linecap="round"/>
                                </svg>
                                {{ $profile->city }}
                            </span>
                        @endif
                        @if($profile?->age)
                            <span class="prf-meta-sep">·</span>
                            <span class="prf-meta-item">{{ $profile->age }} años</span>
                        @endif
                        @if($profile?->gender)
                            <span class="prf-meta-sep">·</span>
                            <span class="prf-meta-item prf-gender-pill">
                                {{ match($profile->gender) {
                                    'male'       => '♂ Masculino',
                                    'female'     => '♀ Femenino',
                                    'non_binary' => '⚧ No binario',
                                    default      => $profile->gender
                                } }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Botón editar --}}
            <button onclick="window.openModal && window.openModal()" type="button" class="prf-edit-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                    <path d="M18.5 2.5l3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Editar perfil
            </button>
        </div>
    </div>

    {{-- ══════════ MAIN CONTENT ══════════ --}}
    <div class="prf-body">

        {{-- Columna izquierda --}}
        <aside class="prf-sidebar">

            {{-- Sobre mí --}}
            <div class="prf-card">
                <div class="prf-card-header">
                    <svg width="16" height="16" fill="none" stroke="var(--pink)" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                    <h2 class="prf-card-title">Sobre mí</h2>
                </div>
                <p class="prf-bio">{{ $profile?->bio ?: 'Aún no has escrito una biografía. ¡Cuéntanos sobre ti!' }}</p>

                @if($profile?->pronouns)
                    <span class="prf-pronoun-badge">{{ $profile->pronouns }}</span>
                @endif

                @if($profile?->looking_for && count((array)$profile->looking_for))
                    <div class="prf-looking-wrap">
                        <p class="prf-looking-label">Busca:</p>
                        <div class="prf-looking-tags">
                            @foreach((array)$profile->looking_for as $lf)
                                <span class="prf-looking-tag">
                                    {{ match($lf) {
                                        'friends'    => '👫 Amistades',
                                        'dating'     => '❤️ Pareja',
                                        'networking' => '🤝 Networking',
                                        'activities' => '🎯 Actividades',
                                        default      => $lf
                                    } }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Mis intereses --}}
            @if($user->interests->count())
            <div class="prf-card">
                <div class="prf-card-header">
                    <svg width="16" height="16" fill="none" stroke="var(--pink)" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke-linecap="round"/>
                    </svg>
                    <h2 class="prf-card-title">Mis intereses</h2>
                </div>
                <div class="prf-interests">
                    @foreach($user->interests as $interest)
                        <span class="prf-interest-tag">{{ $interest->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Stats --}}
            <div class="prf-stats-card">
                <div class="prf-stat">
                    <span class="prf-stat-num">{{ $likedUsers->count() }}</span>
                    <span class="prf-stat-label">Conexiones</span>
                </div>
                <div class="prf-stat-divider"></div>
                <div class="prf-stat">
                    <span class="prf-stat-num">{{ $admirers->count() }}</span>
                    <span class="prf-stat-label">Admiradores</span>
                </div>
                <div class="prf-stat-divider"></div>
                <div class="prf-stat">
                    <span class="prf-stat-num">{{ $user->photos->count() }}</span>
                    <span class="prf-stat-label">Fotos</span>
                </div>
            </div>

        </aside>

        {{-- Columna derecha --}}
        <main class="prf-main">

            @if(session('error'))
                <div class="prf-alert prf-alert-error">{{ session('error') }}</div>
            @endif

            {{-- Galería --}}
            <div class="prf-card">
                <div class="prf-card-header">
                    <svg width="16" height="16" fill="none" stroke="var(--pink)" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
                    </svg>
                    <h2 class="prf-card-title">Galería</h2>
                    <span class="prf-card-count">{{ $user->photos->count() }}/6</span>
                </div>

                {{-- Subir foto --}}
                <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="prf-upload">
                    @csrf
                    <label class="prf-upload-label">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V4M8 8l4-4 4 4" stroke-linecap="round"/>
                        </svg>
                        <span>Agregar foto</span>
                        <input type="file" name="photo" class="hidden" accept="image/*" onchange="this.form.submit()">
                    </label>
                </form>

                {{-- Grid de fotos --}}
                <div class="prf-gallery-grid">
                    @forelse($user->photos as $photo)
                        <div class="prf-gallery-item">
                            <img src="{{ str_starts_with($photo->path, 'http') ? $photo->path : Storage::url($photo->path) }}"
                                 alt="Foto de galería">
                            <div class="prf-gallery-overlay"></div>
                            <form action="{{ route('profile.photo.delete', $photo->id) }}" method="POST" class="prf-gallery-del">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Eliminar foto">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="prf-gallery-empty">
                            <svg width="36" height="36" fill="none" stroke="var(--pink)" stroke-width="1.4" viewBox="0 0 24 24" style="opacity:.4">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
                            </svg>
                            <p>Aún no tienes fotos en tu galería</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Mis conexiones (a quienes di like) --}}
            <div class="prf-card">
                <div class="prf-card-header">
                    <svg width="16" height="16" fill="none" stroke="var(--pink)" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" fill="var(--pink-light)" stroke-linecap="round"/>
                    </svg>
                    <h2 class="prf-card-title">Mis conexiones</h2>
                    <span class="prf-card-badge">{{ $likedUsers->count() }}</span>
                </div>
                <p class="prf-card-desc">Personas a las que les diste like</p>

                @if($likedUsers->count())
                    <div class="prf-people-grid">
                        @foreach($likedUsers as $person)
                            <div class="prf-person-chip">
                                <img src="{{ $person->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($person->name).'&background=FDE8EE&color=E8375A&size=80&bold=true' }}"
                                     alt="{{ $person->name }}">
                                <div class="prf-person-info">
                                    <span class="prf-person-name">{{ $person->name }}</span>
                                    @if($person->profile?->city)
                                        <span class="prf-person-city">{{ $person->profile->city }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="prf-people-empty">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" style="opacity:.3">
                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                        </svg>
                        <p>Aún no has dado like a nadie. <a href="{{ route('explore.index') }}">¡Empieza a explorar!</a></p>
                    </div>
                @endif
            </div>

            {{-- Admiradores (quienes me dieron like) --}}
            <div class="prf-card">
                <div class="prf-card-header">
                    <svg width="16" height="16" fill="none" stroke="var(--pink)" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h2 class="prf-card-title">Admiradores</h2>
                    <span class="prf-card-badge prf-card-badge--gold">{{ $admirers->count() }}</span>
                </div>
                <p class="prf-card-desc">Personas que les gustó tu perfil</p>

                @if($admirers->count())
                    <div class="prf-people-grid">
                        @foreach($admirers as $person)
                            <div class="prf-person-chip">
                                <img src="{{ $person->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($person->name).'&background=FDE8EE&color=E8375A&size=80&bold=true' }}"
                                     alt="{{ $person->name }}">
                                <div class="prf-person-info">
                                    <span class="prf-person-name">{{ $person->name }}</span>
                                    @if($person->profile?->city)
                                        <span class="prf-person-city">{{ $person->profile->city }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="prf-people-empty">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" style="opacity:.3">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <p>Aún nadie ha dado like a tu perfil. ¡Completa tu información!</p>
                    </div>
                @endif
            </div>

        </main>
    </div>
</div>

{{-- MODAL EDITAR PERFIL --}}
<div id="profileModal" class="modal-overlay">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>Editar perfil</h2>
            <button id="closeModalBtn" type="button" class="modal-close-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            @include('profile.partials.update-profile-form')
        </div>
    </div>
</div>

<script src="{{ asset('js/profile/modal.js') }}"></script>
<script src="{{ asset('js/profile/tabs.js') }}"></script>
<script src="{{ asset('js/profile/bioCounter.js') }}"></script>
<script src="{{ asset('js/profile/successMessage.js') }}"></script>
<script src="{{ asset('js/profile/index.js') }}"></script>

@endsection
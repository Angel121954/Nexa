@extends('layouts.app')

@section('title', 'Explorar — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/explore.css') }}">
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
@endpush

@section('content')

<x-topbar />

{{-- ═══ PAGE ═══ --}}
<div class="explore-page">

    {{-- Hero --}}
    <div class="explore-hero">
        <h1>Descubre <span>personas increíbles</span></h1>
        <p>Conecta con personas afines a ti y crea relaciones auténticas.</p>
    </div>

    {{-- Filters bar --}}
    <form id="filter-form" method="GET" action="{{ route('explore.index') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="filters-bar">
            <div class="search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="8" />
                    <path d="M21 21l-4.35-4.35" stroke-linecap="round" />
                </svg>
                <input type="text" name="q" id="q" class="search-input"
                    placeholder="Buscar por nombre, intereses o ciudad..."
                    value="{{ request('q') }}"
                    autocomplete="off">
            </div>

            <select name="city" class="filter-select" onchange="this.form.submit()">
                <option value="">Ubicación</option>
                @foreach($users->pluck('profile.city')->filter()->unique()->sort() as $city)
                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>

            <select name="gender" class="filter-select" onchange="this.form.submit()">
                <option value="">Género</option>
                <option value="male" {{ request('gender') === 'male'       ? 'selected' : '' }}>Masculino</option>
                <option value="female" {{ request('gender') === 'female'     ? 'selected' : '' }}>Femenino</option>
                <option value="non_binary" {{ request('gender') === 'non_binary' ? 'selected' : '' }}>No binario</option>
                <option value="other" {{ request('gender') === 'other'      ? 'selected' : '' }}>Otro</option>
            </select>

            <select name="age_range" id="age-range-select" class="filter-select">
                <option value="">Edad</option>
                <option value="18-24" {{ (request('age_min')=='18' && request('age_max')=='24') ? 'selected' : '' }}>18 – 24</option>
                <option value="25-30" {{ (request('age_min')=='25' && request('age_max')=='30') ? 'selected' : '' }}>25 – 30</option>
                <option value="31-40" {{ (request('age_min')=='31' && request('age_max')=='40') ? 'selected' : '' }}>31 – 40</option>
                <option value="41-99" {{ (request('age_min')=='41') ? 'selected' : '' }}>41+</option>
            </select>
            <input type="hidden" name="age_min" id="age-min" value="{{ request('age_min') }}">
            <input type="hidden" name="age_max" id="age-max" value="{{ request('age_max') }}">

            <button type="button" class="btn-filters" id="toggle-adv">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="4" y1="6" x2="20" y2="6" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                    <line x1="11" y1="18" x2="13" y2="18" />
                </svg>
                Filtros
            </button>
        </div>

        {{-- Advanced filters panel --}}
        <div class="adv-panel {{ request()->hasAny(['age_min','age_max','interests']) ? 'open' : '' }}" id="adv-panel">

            <div class="adv-interest-wrap">
                <label style="font-size:.8125rem;font-weight:600;color:var(--text-primary);">Intereses</label>
                <div class="interest-checkboxes">
                    @foreach($interests as $interest)
                    @php $checked = in_array($interest->id, (array) request('interests', [])); @endphp
                    <input type="checkbox" class="interest-chk"
                        name="interests[]"
                        value="{{ $interest->id }}"
                        id="int-{{ $interest->id }}"
                        {{ $checked ? 'checked' : '' }}>
                    <label class="interest-chk-label" for="int-{{ $interest->id }}">
                        {{ $interest->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="adv-actions">
                <a href="{{ route('explore.index', ['tab' => $tab]) }}" class="btn-clear">Limpiar</a>
                <button type="submit" class="btn-apply">Aplicar filtros</button>
            </div>
        </div>

    </form>

    {{-- Quick tabs --}}
    <div class="quick-tabs">
        <a href="{{ route('explore.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}"
            class="qtab {{ $tab === 'all' ? 'active' : '' }}">
            Todos
        </a>
        <a href="{{ route('explore.index', array_merge(request()->except('page'), ['nearby' => 1])) }}"
            class="qtab {{ request('nearby') ? 'active' : '' }}">

            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke-linecap="round" />
                <circle cx="12" cy="9" r="2.5" />
            </svg>

            Cerca de ti
        </a>
        <a href="{{ route('explore.index', array_merge(request()->except('tab', 'page'), ['tab' => 'new'])) }}"
            class="qtab {{ $tab === 'new' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke-linecap="round" />
            </svg>
            Nuevos
        </a>
        <a href="{{ route('explore.index', array_merge(request()->except('tab', 'page'), ['tab' => 'liked_me'])) }}"
            class="qtab {{ $tab === 'liked_me' ? 'active' : '' }}">
            <span class="online-dot"></span>
            En línea ahora
        </a>
        <a href="{{ route('explore.index', array_merge(request()->except('tab', 'page'), ['tab' => 'interests'])) }}"
            class="qtab {{ $tab === 'interests' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke-linecap="round" />
            </svg>
            Mismos intereses
        </a>
    </div>

    {{-- Cards grid --}}
    <div class="cards-grid">
        @forelse($users as $person)
        @php
        $liked = in_array($person->id, $likedIds);
        $matched = in_array($person->id, $matchIds);
        $photo = $person->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($person->name).'&background=FDE8EE&color=E8375A&size=300';
        $age = $person->profile?->age;
        $city = $person->profile?->city;
        $bio = $person->profile?->bio;
        $tags = $person->interests->take(3);
        @endphp

        <article class="user-card" id="card-{{ $person->id }}">
            <a href="{{ route('profile.show', $person->id) }}" class="card-link">
                <div class="card-photo">
                    <img src="{{ $photo }}" alt="{{ $person->name }}" loading="lazy">

                    {{-- Random online dot for demo --}}
                    @if($person->id % 3 === 0)
                    <span class="card-online" title="En línea"></span>
                    @endif
                </div>

                <div class="card-body">
                    <p class="card-name">
                        {{ $person->name }}{{ $age ? ', '.$age : '' }}
                        @if($matched)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--pink)">
                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                        </svg>
                        @else
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--pink)">
                            <path d="M9 12l2 2 4-4M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2s10 4.48 10 10z" />
                        </svg>
                        @endif
                    </p>

                    @if($bio)
                    <p class="card-bio">{{ $bio }}</p>
                    @endif

                    @if($tags->count())
                    <div class="card-tags">
                        @foreach($tags as $tag)
                        <span class="card-tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    @endif

                    @if($city)
                    <p class="card-location">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke-linecap="round" />
                            <circle cx="12" cy="9" r="2.5" />
                        </svg>
                        {{ $city }}
                    </p>
                    @endif
                </div>
            </a>

            <button class="card-like-btn {{ $liked ? 'liked' : '' }}"
                id="like-btn-{{ $person->id }}"
                data-user="{{ $person->id }}"
                data-liked="{{ $liked ? '1' : '0' }}"
                data-name="{{ $person->name }}"
                title="{{ $liked ? 'Quitar like' : 'Dar like' }}"
                aria-label="{{ $liked ? 'Quitar like a '.$person->name : 'Dar like a '.$person->name }}">
                <svg width="16" height="16" viewBox="0 0 24 24"
                    fill="{{ $liked ? 'currentColor' : 'none' }}"
                    stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </article>
        @empty
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" stroke-linecap="round" />
            </svg>
            <h3>Sin resultados</h3>
            <p>Intenta ajustar los filtros o busca con otros términos.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="pagination-wrap">
        {{ $users->onEachSide(1)->links('vendor.pagination.simple-nexa') }}
    </div>
    @endif

</div>

{{-- Match toast --}}
<div class="match-toast" id="match-toast" role="alert" aria-live="polite">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
    </svg>
    <span id="match-toast-text">¡Es un match!</span>
</div>

{{-- ═══ PREMIUM MODAL ═══ --}}
<div id="premium-modal" class="pm-overlay" role="dialog" aria-modal="true" aria-label="Nexa Premium" hidden>
    <div class="pm-dialog">

        {{-- LEFT: visual --}}
        <div class="pm-visual">
            <div class="pm-visual-inner">
                <div class="pm-logo-badge">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                        <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z" />
                    </svg>
                </div>
                <h2 class="pm-visual-title">Nexa <span>Premium</span></h2>
                <p class="pm-visual-sub">Conecta más rápido, sin límites</p>

                <ul class="pm-visual-perks">
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Likes ilimitados cada día
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Ve quién te dio like
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Filtros avanzados de búsqueda
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Perfil destacado en el feed
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Mensajes antes del match
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Modo incógnito
                    </li>
                </ul>

                <div class="pm-visual-avatars">
                    <img src="https://ui-avatars.com/api/?name=A&background=fff3&color=fff&size=40" alt="">
                    <img src="https://ui-avatars.com/api/?name=B&background=fff3&color=fff&size=40" alt="">
                    <img src="https://ui-avatars.com/api/?name=C&background=fff3&color=fff&size=40" alt="">
                    <span>+2.4k activos hoy</span>
                </div>
            </div>
        </div>

        {{-- RIGHT: plans --}}
        <div class="pm-plans">
            <button class="pm-close" id="close-premium-modal" aria-label="Cerrar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18" stroke-linecap="round" />
                    <line x1="6" y1="6" x2="18" y2="18" stroke-linecap="round" />
                </svg>
            </button>

            <div class="pm-plans-head">
                <p class="pm-eyebrow">Elige tu plan</p>
                <h3 class="pm-plans-title">Empieza hoy</h3>

                {{-- Billing toggle --}}
                <div class="pm-toggle" id="billing-toggle">
                    <button class="pm-toggle-btn active" data-period="monthly">Mensual</button>
                    <button class="pm-toggle-btn" data-period="annual">
                        Anual
                        <span class="pm-badge">-40%</span>
                    </button>
                </div>
            </div>

            <div class="pm-cards">
                {{-- PLUS --}}
                <div class="pm-card">
                    <div class="pm-card-head">
                        <span class="pm-card-name">Plus</span>
                    </div>
                    <div class="pm-card-price">
                        <span class="pm-price" data-monthly="$9.99" data-annual="$5.99">$9.99</span>
                        <span class="pm-period">/mes</span>
                    </div>
                    <ul class="pm-card-features">
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> 50 likes diarios</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> Ve quién te gustó</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> Filtros avanzados</li>
                        <li class="pm-feat-no"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18" stroke-linecap="round" />
                                <line x1="6" y1="6" x2="18" y2="18" stroke-linecap="round" />
                            </svg> Modo incógnito</li>
                        <li class="pm-feat-no"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18" stroke-linecap="round" />
                                <line x1="6" y1="6" x2="18" y2="18" stroke-linecap="round" />
                            </svg> Perfil destacado</li>
                    </ul>
                    <button class="pm-btn-plan pm-btn-outline">Elegir Plus</button>
                </div>

                {{-- GOLD (destacado) --}}
                <div class="pm-card pm-card-gold">
                    <div class="pm-card-badge">Más popular</div>
                    <div class="pm-card-head">
                        <span class="pm-card-name">Gold</span>
                    </div>
                    <div class="pm-card-price">
                        <span class="pm-price" data-monthly="$19.99" data-annual="$11.99">$19.99</span>
                        <span class="pm-period">/mes</span>
                    </div>
                    <ul class="pm-card-features">
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> Likes ilimitados</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> Ve quién te gustó</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> Filtros avanzados</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> Modo incógnito</li>
                        <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5" stroke-linecap="round" />
                            </svg> Perfil destacado</li>
                    </ul>
                    <button class="pm-btn-plan pm-btn-gold">Elegir Gold</button>
                </div>
            </div>

            <p class="pm-disclaimer">Cancela cuando quieras. Sin compromisos.</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/explore/app.js') }}" type="module"></script>
@endpush
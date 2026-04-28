@extends('layouts.app')

@section('title', 'Explorar — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/explore.css') }}">
@endpush

@section('content')

{{-- ═══ NAVBAR ═══ --}}
<nav class="explore-nav">
    <a href="{{ route('explore.index') }}" class="nav-logo">
        <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
    </a>

    <div class="nav-links">
        <a href="{{ route('explore.index', ['tab' => 'all']) }}"
           class="nav-link {{ request('tab', 'all') === 'all' ? 'active' : '' }}">
            Descubrir
        </a>
        <a href="{{ route('explore.index', ['tab' => 'liked_me']) }}"
           class="nav-link {{ request('tab') === 'liked_me' ? 'active' : '' }}">
            Personas que te gustaron
        </a>
        <a href="{{ route('explore.index', ['tab' => 'interests']) }}"
           class="nav-link {{ request('tab') === 'interests' ? 'active' : '' }}">
            Mismos intereses
        </a>
        <a href="#" class="nav-link">Mensajes</a>
    </div>

    <div class="nav-right">
        <a href="#" class="btn-premium">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
            </svg>
            Suscríbete a Nexa Premium
        </a>

        <a href="#" class="nav-icon-btn" title="Notificaciones">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="nav-notif-dot"></span>
        </a>

        <a href="{{ route('profile.edit') }}" class="nav-avatar">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff' }}"
                 alt="{{ auth()->user()->name }}">
            <span>{{ Str::words(auth()->user()->name, 1, '') }}</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</nav>

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
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
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
                <option value="male"       {{ request('gender') === 'male'       ? 'selected' : '' }}>Masculino</option>
                <option value="female"     {{ request('gender') === 'female'     ? 'selected' : '' }}>Femenino</option>
                <option value="non_binary" {{ request('gender') === 'non_binary' ? 'selected' : '' }}>No binario</option>
                <option value="other"      {{ request('gender') === 'other'      ? 'selected' : '' }}>Otro</option>
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
                    <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/>
                </svg>
                Filtros
            </button>
        </div>

        {{-- Advanced filters panel --}}
        <div class="adv-panel {{ request()->hasAny(['age_min','age_max','interests']) ? 'open' : '' }}" id="adv-panel">
            <div>
                <label>Intereses</label>
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
        <a href="{{ route('explore.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all', 'nearby' => 1])) }}"
           class="qtab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke-linecap="round"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
            Cerca de ti
        </a>
        <a href="{{ route('explore.index', array_merge(request()->except('tab', 'page'), ['tab' => 'new'])) }}"
           class="qtab {{ $tab === 'new' ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke-linecap="round"/>
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
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke-linecap="round"/>
            </svg>
            Mismos intereses
        </a>
    </div>

    {{-- Cards grid --}}
    <div class="cards-grid">
        @forelse($users as $person)
            @php
                $liked   = in_array($person->id, $likedIds);
                $matched = in_array($person->id, $matchIds);
                $photo   = $person->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($person->name).'&background=FDE8EE&color=E8375A&size=300';
                $age     = $person->profile?->age;
                $city    = $person->profile?->city;
                $bio     = $person->profile?->bio;
                $tags    = $person->interests->take(3);
            @endphp

            <article class="user-card" id="card-{{ $person->id }}">
                <div class="card-photo">
                    <img src="{{ $photo }}" alt="{{ $person->name }}" loading="lazy">

                    {{-- Random online dot for demo --}}
                    @if($person->id % 3 === 0)
                        <span class="card-online" title="En línea"></span>
                    @endif

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
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="card-body">
                    <p class="card-name">
                        {{ $person->name }}{{ $age ? ', '.$age : '' }}
                        @if($matched)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--pink)">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                            </svg>
                        @else
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--pink)">
                                <path d="M9 12l2 2 4-4M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2s10 4.48 10 10z"/>
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
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke-linecap="round"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                            {{ $city }}
                        </p>
                    @endif
                </div>
            </article>
        @empty
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
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
        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
    </svg>
    <span id="match-toast-text">¡Es un match!</span>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // ── Like buttons ─────────────────────────────
    document.querySelectorAll('.card-like-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.stopPropagation();
            const userId = btn.dataset.user;
            btn.disabled = true;

            try {
                const res = await fetch(`/explore/like/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Server error');
                const data = await res.json();

                const svg = btn.querySelector('svg');

                if (data.liked) {
                    btn.classList.add('liked');
                    btn.dataset.liked = '1';
                    svg.setAttribute('fill', 'currentColor');
                    btn.title = 'Quitar like';
                    animateLike(btn);
                } else {
                    btn.classList.remove('liked');
                    btn.dataset.liked = '0';
                    svg.setAttribute('fill', 'none');
                    btn.title = 'Dar like';
                }

                if (data.match) {
                    showMatchToast(data.matchName);
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.disabled = false;
            }
        });
    });

    // ── Like animation ───────────────────────────
    function animateLike(btn) {
        btn.style.transform = 'scale(1.35)';
        setTimeout(() => btn.style.transform = '', 300);
    }

    // ── Match toast ──────────────────────────────
    function showMatchToast(name) {
        const toast = document.getElementById('match-toast');
        document.getElementById('match-toast-text').textContent =
            `¡Es un match con ${name}! 🎉`;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }

    // ── Advanced filters panel ───────────────────
    const toggleBtn = document.getElementById('toggle-adv');
    const panel     = document.getElementById('adv-panel');
    if (toggleBtn && panel) {
        toggleBtn.addEventListener('click', () => {
            panel.classList.toggle('open');
        });
    }

    // ── Age range select ─────────────────────────
    const ageSelect = document.getElementById('age-range-select');
    if (ageSelect) {
        ageSelect.addEventListener('change', () => {
            const val = ageSelect.value;
            const [min, max] = val ? val.split('-') : ['', ''];
            document.getElementById('age-min').value = min || '';
            document.getElementById('age-max').value = max || '';
        });
    }

    // ── Search debounce ──────────────────────────
    const searchInput = document.getElementById('q');
    if (searchInput) {
        let timer;
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                document.getElementById('filter-form').submit();
            }, 600);
        });
    }
});
</script>
@endpush
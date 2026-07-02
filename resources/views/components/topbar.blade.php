{{-- ═══ NAVBAR ═══ --}}
@php
$onlyLogoAvatar = $onlyLogoAvatar ?? false;
@endphp

<nav class="explore-nav">
    <a href="{{ route('explore.index') }}" class="nav-logo">
        <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
    </a>

    @if(!$onlyLogoAvatar)
    <button type="button" class="nav-hamburger" id="nav-hamburger" aria-label="Toggle menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>

    <div class="nav-links" id="nav-links">
        <a href="{{ route('explore.index') }}"
            class="nav-link {{ request()->routeIs('explore.index') && request('tab', 'all') === 'all' ? 'active' : '' }}">
            Descubrir
        </a>
        <a href="{{ route('explore.index', ['tab' => 'liked_me']) }}"
            class="nav-link {{ request()->routeIs('explore.index') && request('tab') === 'liked_me' ? 'active' : '' }}">
            Personas que te gustaron
        </a>
        <a href="{{ route('explore.index', ['tab' => 'interests']) }}"
            class="nav-link {{ request()->routeIs('explore.index') && request('tab') === 'interests' ? 'active' : '' }}">
            Mismos intereses
        </a>
        <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            Mensajes
            <span class="nav-link-badge" id="nav-unread-badge" style="display:none;"></span>
        </a>
        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            Notificaciones
            <span class="nav-link-badge" id="nav-notif-badge" style="display:none;"></span>
        </a>
        @auth
            @if(auth()->user()->isAdmin())
            <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                Panel
            </a>
            @endif
        @endauth
    </div>
    @endif

    <div class="nav-right">
        @auth
        <a href="{{ route('profile.index') }}" class="nav-avatar">
            <img id="topbar-avatar" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff' }}"
                alt="{{ auth()->user()->name }}">

            @if(!$onlyLogoAvatar)
            <span>{{ Str::words(auth()->user()->name, 1, '') }}</span>

            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            @endif
        </a>
        @endauth

        @if(!$onlyLogoAvatar)
        <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
            @csrf
            <button type="submit" class="nav-logout-btn" title="Cerrar sesión">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke-linecap="round" stroke-linejoin="round" />
                    <polyline points="16 17 21 12 16 7" stroke-linecap="round" stroke-linejoin="round" />
                    <line x1="21" y1="12" x2="9" y2="12" stroke-linecap="round" />
                </svg>
            </button>
        </form>
        @endif
    </div>
</nav>
@if(!$onlyLogoAvatar)
<div class="nav-overlay" id="nav-overlay"></div>

{{-- ═══ BOTTOM NAV — MÓVIL ═══ --}}
<nav class="bottom-nav" id="bottom-nav">
    <a href="{{ route('explore.index') }}"
        class="bottom-nav-item {{ request()->routeIs('explore.index') && request('tab', 'all') === 'all' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
        </svg>
        <span>Descubrir</span>
        <span class="bottom-nav-badge" style="display:none"></span>
    </a>
    <a href="{{ route('explore.index', ['tab' => 'liked_me']) }}"
        class="bottom-nav-item {{ request()->routeIs('explore.index') && request('tab') === 'liked_me' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span>Me gustaron</span>
        <span class="bottom-nav-badge" style="display:none"></span>
    </a>
    <a href="{{ route('messages.index') }}"
        class="bottom-nav-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span>Mensajes</span>
        <span class="bottom-nav-badge" id="bottom-nav-unread-badge" style="display:none"></span>
    </a>
    <a href="{{ route('notifications.index') }}"
        class="bottom-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span>Notificaciones</span>
        <span class="bottom-nav-badge" id="bottom-nav-notif-badge" style="display:none"></span>
    </a>
    <a href="{{ route('profile.index') }}"
        class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        <span>Perfil</span>
        <span class="bottom-nav-badge" style="display:none"></span>
    </a>
    @auth
        @if(auth()->user()->isAdmin())
        <a href="{{ route('dashboard.index') }}"
            class="bottom-nav-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            <span>Panel</span>
            <span class="bottom-nav-badge" style="display:none"></span>
        </a>
        @endif
    @endauth
</nav>
@endif

@push('scripts')
<script src="{{ asset('js/messages/unread-badge.js') }}"></script>
<script src="{{ asset('js/notifications/badge.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
@endpush
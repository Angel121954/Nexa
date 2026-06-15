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
@endif

@push('scripts')
<script src="{{ asset('js/messages/unread-badge.js') }}"></script>
<script src="{{ asset('js/notifications/badge.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
@endpush
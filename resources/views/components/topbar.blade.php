{{-- ═══ NAVBAR ═══ --}}
<nav class="explore-nav">
    <a href="{{ route('explore.index') }}" class="nav-logo">
        <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
    </a>

    <button type="button" class="nav-hamburger" id="nav-hamburger" aria-label="Toggle menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>

    <div class="nav-links" id="nav-links">
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
        <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">Mensajes</a>
    </div>

    <div class="nav-right">
        <!-- <button type="button" class="btn-premium" id="open-premium-modal" aria-haspopup="dialog">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z" />
            </svg>
            Suscríbete a Nexa Premium
        </button> -->

        <a href="#" class="nav-icon-btn" title="Notificaciones">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="nav-notif-dot"></span>
        </a>

        <a href="{{ route('profile.index') }}" class="nav-avatar">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff' }}"
                alt="{{ auth()->user()->name }}">
            <span>{{ Str::words(auth()->user()->name, 1, '') }}</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>

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
    </div>
</nav>
<div class="nav-overlay" id="nav-overlay"></div>
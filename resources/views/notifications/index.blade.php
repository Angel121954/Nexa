@extends('layouts.app')

@section('title', 'Notificaciones — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush

@section('content')

<x-topbar />

{{-- ═══ NOTIFICATIONS PAGE ═══ --}}
<div class="notif-page">

    {{-- ─── HEADER ─── --}}
    <div class="notif-header">
        <div class="notif-header-left">
            <h1>Notificaciones</h1>
            @if(($unreadCount ?? 0) > 0)
            <span class="notif-count-badge">{{ $unreadCount }}</span>
            @endif
        </div>
        <div class="notif-header-actions">
            @if(($unreadCount ?? 0) > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}" class="notif-readall-form">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-readall">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M27 6L16 17" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Marcar todo como leído
                </button>
            </form>
            @endif
            <button type="button" class="btn-notif-settings" title="Configurar notificaciones" id="notif-settings-btn">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" stroke-linecap="round" />
                </svg>
            </button>
        </div>
    </div>

    {{-- ─── FILTER TABS ─── --}}
    <div class="notif-tabs" id="notif-tabs">
        <button class="notif-tab active" data-filter="all" type="button">
            Todas
            @if(($totalCount ?? 0) > 0)
            <span class="tab-count">{{ $totalCount }}</span>
            @endif
        </button>
        <button class="notif-tab" data-filter="unread" type="button">
            No leídas
            @if(($unreadCount ?? 0) > 0)
            <span class="tab-count tab-count-pink">{{ $unreadCount }}</span>
            @endif
        </button>
        <button class="notif-tab" data-filter="match" type="button">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
            </svg>
            Matches
        </button>
        <button class="notif-tab" data-filter="like" type="button">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Likes
        </button>
        <button class="notif-tab" data-filter="message" type="button">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Mensajes
        </button>
    </div>

    {{-- ─── NOTIFICATIONS LIST ─── --}}
    <div class="notif-list" id="notif-list">

        @forelse($notifications ?? [] as $group => $items)

        {{-- Date group header --}}
        <div class="notif-group-label" data-group="{{ $group }}">{{ $group }}</div>

        @foreach($items as $notification)
        @php
        $isUnread = is_null($notification->read_at);
        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
        $type = $notification->type;
        @endphp

        <div class="notif-item {{ $isUnread ? 'unread' : '' }}"
            data-id="{{ $notification->id }}"
            data-type="{{ $type }}"
            data-filter="{{ $type }}">

            {{-- Avatar + type icon --}}
            <div class="notif-avatar-wrap">
                <img class="notif-avatar"
                    src="{{ $data['actor_avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode($data['actor_name'] ?? 'U').'&background=E8375A&color=fff' }}"
                    alt="{{ $data['actor_name'] ?? '' }}">
                <div class="notif-type-icon notif-icon-{{ $type }}">
                    @if($type === 'match')
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                    </svg>
                    @elseif($type === 'like')
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    @elseif($type === 'message')
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    @elseif($type === 'profile_view')
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-linecap="round" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    @else
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round" />
                        <line x1="12" y1="16" x2="12.01" y2="16" stroke-linecap="round" />
                    </svg>
                    @endif
                </div>
            </div>

            {{-- Content --}}
            <div class="notif-content">
                <p class="notif-text">
                    <span class="notif-actor">{{ $data['actor_name'] ?? 'Alguien' }}</span>
                    {{ $data['message'] ?? '' }}
                </p>
                @if(!empty($data['preview']))
                <p class="notif-preview">"{{ Str::limit($data['preview'], 80) }}"</p>
                @endif
                <span class="notif-time" data-timestamp="{{ $notification->created_at->timestamp }}">{{ $notification->created_at->diffForHumans() }}</span>
            </div>

            {{-- Actions --}}
            <div class="notif-actions">
                @if($isUnread)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="notif-mark-form">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="notif-mark-btn" title="Marcar como leída">
                        <span class="unread-dot"></span>
                    </button>
                </form>
                @endif

                @if(!empty($data['action_url']))
                <a href="{{ $data['action_url'] }}" class="notif-action-link">
                    @if($type === 'match')
                    Enviar mensaje
                    @elseif($type === 'message')
                    Ver mensaje
                    @elseif($type === 'like')
                    Ver perfil
                    @else
                    Ver
                    @endif
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
                @endif
            </div>
        </div>

        @endforeach

        @empty

        {{-- ─── EMPTY STATE ─── --}}
        <div class="notif-empty" id="notif-empty">
            <div class="notif-empty-illustration">
                <div class="empty-ring empty-ring-1"></div>
                <div class="empty-ring empty-ring-2"></div>
                <div class="empty-icon-wrap">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.73 21a2 2 0 01-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
            <h3>Sin notificaciones aún</h3>
            <p>Cuando alguien te dé like, haga match contigo o te escriba, aparecerá aquí.</p>
            <a href="{{ route('explore.index') }}" class="btn-empty-cta">
                Explorar personas
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>

        @endforelse

    </div>

    {{-- ─── LOAD MORE ─── --}}
    @if(($paginator ?? collect())->hasMorePages())
    <div class="notif-load-more">
        <a href="{{ ($paginator ?? collect())->nextPageUrl() }}" class="btn-load-more" id="btn-load-more">
            Cargar más notificaciones
        </a>
    </div>
    @endif

</div>

{{-- ─── SETTINGS PANEL (slide-in) ─── --}}
<div class="notif-settings-overlay" id="notif-settings-overlay"></div>
<aside class="notif-settings-panel" id="notif-settings-panel" aria-hidden="true">
    <div class="nsp-header">
        <h3>Preferencias de notificaciones</h3>
        <button type="button" class="nsp-close" id="nsp-close" aria-label="Cerrar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18" stroke-linecap="round" />
                <line x1="6" y1="6" x2="18" y2="18" stroke-linecap="round" />
            </svg>
        </button>
    </div>
    <form method="POST" action="{{ route('notifications.preferences') }}" class="nsp-form">
        @csrf
        @method('PATCH')
        <div class="nsp-section">
            <p class="nsp-section-label">Actividad</p>
            <label class="nsp-toggle-row">
                <div class="nsp-toggle-info">
                    <span class="nsp-toggle-title">Matches</span>
                    <span class="nsp-toggle-sub">Cuando alguien acepta tu like</span>
                </div>
                <div class="toggle-switch">
                    <input type="checkbox" name="notify_match" id="notify_match" checked>
                    <span class="toggle-track"></span>
                </div>
            </label>
            <label class="nsp-toggle-row">
                <div class="nsp-toggle-info">
                    <span class="nsp-toggle-title">Likes</span>
                    <span class="nsp-toggle-sub">Cuando alguien te da like</span>
                </div>
                <div class="toggle-switch">
                    <input type="checkbox" name="notify_like" id="notify_like" checked>
                    <span class="toggle-track"></span>
                </div>
            </label>
            <label class="nsp-toggle-row">
                <div class="nsp-toggle-info">
                    <span class="nsp-toggle-title">Mensajes nuevos</span>
                    <span class="nsp-toggle-sub">Cuando recibes un mensaje</span>
                </div>
                <div class="toggle-switch">
                    <input type="checkbox" name="notify_message" id="notify_message" checked>
                    <span class="toggle-track"></span>
                </div>
            </label>
            <label class="nsp-toggle-row">
                <div class="nsp-toggle-info">
                    <span class="nsp-toggle-title">Visitas al perfil</span>
                    <span class="nsp-toggle-sub">Cuando alguien visita tu perfil</span>
                </div>
                <div class="toggle-switch">
                    <input type="checkbox" name="notify_profile_view" id="notify_profile_view">
                    <span class="toggle-track"></span>
                </div>
            </label>
        </div>
        <div class="nsp-footer">
            <button type="submit" class="btn-nsp-save">Guardar preferencias</button>
        </div>
    </form>
</aside>

@endsection

@push('scripts')
<script src="{{ asset('js/notifications.js') }}" defer></script>
<script src="{{ asset('js/notifications/helpers.js') }}" defer></script>
<script src="{{ asset('js/notifications/filters.js') }}" defer></script>
<script src="{{ asset('js/notifications/read.js') }}" defer></script>
<script src="{{ asset('js/notifications/settings.js') }}" defer></script>
<script src="{{ asset('js/notifications/realtime.js') }}" defer></script>
@endpush
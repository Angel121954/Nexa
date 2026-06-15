@extends('dashboard.layout', ['pageTitle' => 'Actividad — Panel — Nexa'])

@section('panel-content')

<div class="dash-topbar">
    <h1 class="dash-page-title">Actividad</h1>
    <div class="dash-topbar-right">
        <a href="{{ route('notifications.index') }}" class="dash-icon-btn" title="Notificaciones">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M13.73 21a2 2 0 01-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            @if(($unreadNotifications ?? 0) > 0)
            <span class="dash-icon-dot"></span>
            @endif
        </a>
        <a href="{{ route('profile.index') }}" class="dash-icon-btn" title="Perfil">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="8" r="4" stroke-linecap="round" />
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    </div>
</div>

<div class="dash-section" id="sec-activity">

    <div class="dash-metrics">
        <div class="dash-metric-card">
            <p class="dash-metric-label">Likes hoy</p>
            <span class="dash-metric-val">{{ number_format($likesToday ?? 0) }}</span>
        </div>
        <div class="dash-metric-card">
            <p class="dash-metric-label">Historias publicadas</p>
            <span class="dash-metric-val">{{ number_format($storiesToday ?? 0) }}</span>
        </div>
        <div class="dash-metric-card dash-metric-accent">
            <p class="dash-metric-label">Usuarios online ahora</p>
            <span class="dash-metric-val">{{ number_format($onlineNow ?? 0) }}</span>
        </div>
    </div>

    <div class="dash-card">
        <div class="dash-card-head">
            <h3 class="dash-card-title">Actividad reciente</h3>
            <span class="dash-card-meta">Últimas 24h</span>
        </div>
        <div class="dash-activity-feed" id="dash-activity-feed">
            @forelse($recentActivity ?? [] as $event)
            <div class="dash-activity-item">
                <div class="dash-activity-dot dash-dot-{{ $event['type'] ?? 'default' }}"></div>
                <p class="dash-activity-text">{{ $event['text'] }}</p>
                <span class="dash-activity-time">{{ $event['time'] }}</span>
            </div>
            @empty
            <div class="dash-activity-item">
                <div class="dash-activity-dot dash-dot-match"></div>
                <p class="dash-activity-text">Sofía C. y Luis M. hicieron match</p>
                <span class="dash-activity-time">hace 5 min</span>
            </div>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dash-dot-register"></div>
                <p class="dash-activity-text">Karen R. se registró en Nexa</p>
                <span class="dash-activity-time">hace 22 min</span>
            </div>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dash-dot-premium"></div>
                <p class="dash-activity-text">Jorge P. actualizó a plan Premium</p>
                <span class="dash-activity-time">hace 1h</span>
            </div>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dash-dot-match"></div>
                <p class="dash-activity-text">Andrea M. y Carlos V. hicieron match</p>
                <span class="dash-activity-time">hace 2h</span>
            </div>
            <div class="dash-activity-item">
                <div class="dash-activity-dot dash-dot-register"></div>
                <p class="dash-activity-text">Miguel A. completó su onboarding</p>
                <span class="dash-activity-time">hace 3h</span>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@extends('dashboard.layout', ['pageTitle' => 'Resumen — Panel — Nexa'])

@section('panel-content')

<div class="dash-topbar">
    <h1 class="dash-page-title">Resumen</h1>
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

<div class="dash-section" id="sec-overview">

    <div class="dash-metrics">

        <div class="dash-metric-card dash-metric-accent">
            <p class="dash-metric-label">Usuarios activos</p>
            <span class="dash-metric-val">{{ number_format($activeUsers ?? 0) }}</span>
            <p class="dash-metric-trend dash-trend-up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke-linecap="round" stroke-linejoin="round" />
                    <polyline points="17 6 23 6 23 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                +12% este mes
            </p>
        </div>

        <div class="dash-metric-card">
            <p class="dash-metric-label">Matches hoy</p>
            <span class="dash-metric-val">{{ number_format($matchesToday ?? 0) }}</span>
            <p class="dash-metric-trend dash-trend-up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke-linecap="round" stroke-linejoin="round" />
                    <polyline points="17 6 23 6 23 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                +8% vs ayer
            </p>
        </div>

        <div class="dash-metric-card">
            <p class="dash-metric-label">Mensajes enviados</p>
            <span class="dash-metric-val">{{ number_format($messagesToday ?? 0) }}</span>
            <p class="dash-metric-trend dash-trend-up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke-linecap="round" stroke-linejoin="round" />
                    <polyline points="17 6 23 6 23 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                +23% hoy
            </p>
        </div>

        <div class="dash-metric-card">
            <p class="dash-metric-label">Bajas del mes</p>
            <span class="dash-metric-val">{{ number_format($churnsMonth ?? 0) }}</span>
            <p class="dash-metric-trend dash-trend-down">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" stroke-linecap="round" stroke-linejoin="round" />
                    <polyline points="17 18 23 18 23 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                -3% vs mes anterior
            </p>
        </div>

    </div>

    <div class="dash-charts-row">

        <div class="dash-card">
            <div class="dash-card-head">
                <h3 class="dash-card-title">Nuevos registros</h3>
                <span class="dash-card-meta">Últimos 7 días</span>
            </div>
            <div class="dash-bar-chart" id="dash-bar-chart"
                data-values="{{ json_encode($registrationsWeek ?? [42,67,55,89,73,110,95]) }}"
                data-labels="{{ json_encode($registrationsLabels ?? ['L','M','M','J','V','S','D']) }}">
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-head">
                <h3 class="dash-card-title">Distribución</h3>
            </div>
            <canvas id="dash-donut" width="120" height="120"></canvas>
            <div class="dash-donut-legend">
                <div class="dash-leg-item">
                    <span class="dash-leg-dot" style="background: var(--pink)"></span>
                    Premium
                    <span class="dash-leg-val">{{ $premiumPct ?? 34 }}%</span>
                </div>
                <div class="dash-leg-item">
                    <span class="dash-leg-dot" style="background: #F0997B"></span>
                    Gratis
                    <span class="dash-leg-val">{{ $freePct ?? 52 }}%</span>
                </div>
                <div class="dash-leg-item">
                    <span class="dash-leg-dot" style="background: var(--border)"></span>
                    Inactivos
                    <span class="dash-leg-val">{{ $inactivePct ?? 14 }}%</span>
                </div>
            </div>
        </div>

    </div>

    <div class="dash-table-card">
        <div class="dash-table-head">
            <h3 class="dash-card-title">Usuarios recientes</h3>
            <a href="{{ route('dashboard.users') }}" class="dash-link-action">Ver todos →</a>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Ciudad</th>
                        <th>Plan</th>
                        <th>Estado</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers ?? [] as $user)
                    <tr>
                        <td>
                            <div class="dash-user-cell">
                                <img
                                    src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E8375A&color=fff&size=32' }}"
                                    alt="{{ $user->name }}"
                                    class="dash-cell-avatar">
                                {{ Str::words($user->name, 2, '') }}
                            </div>
                        </td>
                        <td>{{ $user->city ?? '—' }}</td>
                        <td>
                            @if($user->is_premium)
                            <span class="dash-badge dash-badge-pink">Premium</span>
                            @else
                            <span class="dash-badge dash-badge-gray">Gratis</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_online)
                            <span class="dash-badge dash-badge-green">Activo</span>
                            @else
                            <span class="dash-badge dash-badge-gray">Inactivo</span>
                            @endif
                        </td>
                        <td class="dash-muted">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td>
                            <div class="dash-user-cell">
                                <img src="https://ui-avatars.com/api/?name=Sofia+C&background=E8375A&color=fff&size=32" alt="Sofía C." class="dash-cell-avatar">
                                Sofía C.
                            </div>
                        </td>
                        <td>Bogotá</td>
                        <td><span class="dash-badge dash-badge-pink">Premium</span></td>
                        <td><span class="dash-badge dash-badge-green">Activo</span></td>
                        <td class="dash-muted">hace 2h</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="dash-user-cell">
                                <img src="https://ui-avatars.com/api/?name=Luis+M&background=E8375A&color=fff&size=32" alt="Luis M." class="dash-cell-avatar">
                                Luis M.
                            </div>
                        </td>
                        <td>Medellín</td>
                        <td><span class="dash-badge dash-badge-gray">Gratis</span></td>
                        <td><span class="dash-badge dash-badge-green">Activo</span></td>
                        <td class="dash-muted">hace 5h</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="dash-user-cell">
                                <img src="https://ui-avatars.com/api/?name=Karen+R&background=E8375A&color=fff&size=32" alt="Karen R." class="dash-cell-avatar">
                                Karen R.
                            </div>
                        </td>
                        <td>Cali</td>
                        <td><span class="dash-badge dash-badge-pink">Premium</span></td>
                        <td><span class="dash-badge dash-badge-green">Activo</span></td>
                        <td class="dash-muted">ayer</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="dash-user-cell">
                                <img src="https://ui-avatars.com/api/?name=Jorge+P&background=E8375A&color=fff&size=32" alt="Jorge P." class="dash-cell-avatar">
                                Jorge P.
                            </div>
                        </td>
                        <td>Bucaramanga</td>
                        <td><span class="dash-badge dash-badge-gray">Gratis</span></td>
                        <td><span class="dash-badge dash-badge-gray">Inactivo</span></td>
                        <td class="dash-muted">hace 3d</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

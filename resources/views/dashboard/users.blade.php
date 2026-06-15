@extends('dashboard.layout', ['pageTitle' => 'Usuarios — Panel — Nexa'])

@section('panel-content')

<div class="dash-topbar">
    <h1 class="dash-page-title">Usuarios</h1>
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

<div class="dash-section" id="sec-users">

    <div class="dash-table-card">
        <div class="dash-table-head">
            <h3 class="dash-card-title">Todos los usuarios</h3>
            <div class="dash-search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="8" />
                    <path d="M21 21l-4.35-4.35" stroke-linecap="round" />
                </svg>
                <input type="text" id="dash-user-search" placeholder="Buscar usuario..." autocomplete="off">
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table" id="dash-users-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Ciudad</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allUsers ?? [] as $user)
                    <tr data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                        <td>
                            <div class="dash-user-cell">
                                <img
                                    src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E8375A&color=fff&size=32' }}"
                                    alt="{{ $user->name }}"
                                    class="dash-cell-avatar">
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="dash-muted">{{ $user->email }}</td>
                        <td>{{ $user->city ?? '—' }}</td>
                        <td>
                            @if($user->role === 'admin')
                            <span class="dash-badge dash-badge-pink">Admin</span>
                            @else
                            <span class="dash-badge dash-badge-gray">Usuario</span>
                            @endif
                        </td>
                        <td>
                            @if($user->isBlocked())
                            <span class="dash-badge" style="background:#dc2626;color:#fff;">Bloqueado</span>
                            @elseif($user->is_online)
                            <span class="dash-badge dash-badge-green">Activo</span>
                            @else
                            <span class="dash-badge dash-badge-gray">Inactivo</span>
                            @endif
                        </td>
                        <td class="dash-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="dash-actions">
                                <form action="{{ route('dashboard.toggle-block', $user) }}" method="POST" class="dash-inline-form">
                                    @csrf
                                    <button type="submit" class="dash-action-btn {{ $user->isBlocked() ? 'dash-action-unblock' : 'dash-action-block' }}"
                                        title="{{ $user->isBlocked() ? 'Desbloquear' : 'Bloquear' }}">
                                        @if($user->isBlocked())
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-linecap="round"/>
                                            <circle cx="12" cy="12" r="3" stroke-linecap="round"/>
                                        </svg>
                                        @else
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" stroke-linecap="round"/>
                                            <path d="M7 11V7a5 5 0 0110 0v4" stroke-linecap="round"/>
                                        </svg>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('dashboard.toggle-admin', $user) }}" method="POST" class="dash-inline-form">
                                    @csrf
                                    <button type="submit" class="dash-action-btn {{ $user->role === 'admin' ? 'dash-action-demote' : 'dash-action-promote' }}"
                                        title="{{ $user->role === 'admin' ? 'Quitar admin' : 'Hacer admin' }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="dash-empty-cell">No hay usuarios registrados aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('js/dashboard/search.js') }}"></script>
@endpush

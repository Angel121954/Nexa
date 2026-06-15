@extends('layouts.app')

@section('title', $pageTitle ?? 'Panel — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')

<x-topbar />

<div class="dashboard-page">

    <aside class="dash-sidebar" id="dash-sidebar">

        <div class="dash-sidebar-header">
            <h2>Panel</h2>
        </div>

        <nav class="dash-nav" id="dash-nav">
            <a href="{{ route('dashboard.index') }}"
                class="dash-nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round" />
                    <rect x="14" y="3" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round" />
                    <rect x="3" y="14" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round" />
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Resumen</span>
            </a>

            <a href="{{ route('dashboard.users') }}"
                class="dash-nav-item {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="9" cy="7" r="4" stroke-linecap="round" />
                    <path d="M23 21v-2a4 4 0 00-3-3.87" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M16 3.13a4 4 0 010 7.75" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Usuarios</span>
            </a>

            <a href="{{ route('dashboard.activity') }}"
                class="dash-nav-item {{ request()->routeIs('dashboard.activity') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Actividad</span>
            </a>
        </nav>

        <div class="dash-sidebar-footer">
            <a href="{{ route('profile.index') }}" class="dash-user-row">
                <img
                    src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff' }}"
                    alt="{{ auth()->user()->name }}"
                    class="dash-user-avatar">
                <div class="dash-user-info">
                    <p>{{ Str::words(auth()->user()->name, 2, '') }}</p>
                    <span>Admin</span>
                </div>
            </a>
        </div>

    </aside>

    <main class="dash-main" id="dash-main">

        <x-toast />
        @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));</script>
        @endif
        @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));</script>
        @endif

        @yield('panel-content')

    </main>

</div>

@endsection

@push('scripts')
<script src="{{ asset('js/dashboard/index.js') }}"></script>
@endpush

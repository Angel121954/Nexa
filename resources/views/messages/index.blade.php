@extends('layouts.app')

@section('title', 'Mensajes — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/explore.css') }}">
<style>
    .messages-page {
        min-height: calc(100vh - var(--nav-h, 64px));
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: var(--bg, #F9FAFB);
        font-family: 'Inter', sans-serif;
    }
    .messages-empty {
        text-align: center;
        max-width: 400px;
    }
    .messages-empty-icon {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: var(--pink-light, #FDE8EE);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.5rem;
        color: var(--pink, #E8375A);
    }
    .messages-empty h2 {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--text-primary, #111827);
        margin-bottom: .5rem;
    }
    .messages-empty p {
        font-size: .9375rem;
        color: var(--text-secondary, #6B7280);
        line-height: 1.6;
        margin-bottom: 1.75rem;
    }
    .messages-empty a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 24px;
        background: var(--pink, #E8375A);
        color: #fff;
        font-size: .875rem;
        font-weight: 600;
        border-radius: 24px;
        text-decoration: none;
        transition: background 150ms ease, transform 150ms ease;
    }
    .messages-empty a:hover {
        background: var(--pink-dark, #C92E4B);
        transform: translateY(-1px);
    }
    .messages-empty .tip {
        margin-top: 1.25rem;
        font-size: .8125rem;
        color: var(--text-muted, #9CA3AF);
    }
    .messages-empty .tip span {
        color: var(--pink, #E8375A);
        font-weight: 600;
    }
</style>
@endpush

@section('content')

{{-- Navbar reutilizado del explore --}}
<nav class="explore-nav">
    <a href="{{ route('explore.index') }}" class="nav-logo">
        <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
    </a>
    <div class="nav-links">
        <a href="{{ route('explore.index', ['tab' => 'all']) }}" class="nav-link">Descubrir</a>
        <a href="{{ route('explore.index', ['tab' => 'liked_me']) }}" class="nav-link">Personas que te gustaron</a>
        <a href="{{ route('explore.index', ['tab' => 'interests']) }}" class="nav-link">Mismos intereses</a>
        <a href="{{ route('messages.index') }}" class="nav-link active">Mensajes</a>
    </div>
    <div class="nav-right">
        <a href="#" class="btn-premium">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
            </svg>
            Suscríbete a Nexa Premium
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

<div class="messages-page">
    <div class="messages-empty">
        <div class="messages-empty-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h2>Aún no tienes mensajes</h2>
        <p>
            Para chatear con alguien primero deben hacerse <strong>match</strong>.
            Explora personas, da likes y cuando sea mutuo podrán escribirse.
        </p>
        <a href="{{ route('explore.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
            </svg>
            Explorar personas
        </a>
        <p class="tip">Consejo: activa la vista <span>Mismos intereses</span> para conectar más fácil.</p>
    </div>
</div>

@endsection

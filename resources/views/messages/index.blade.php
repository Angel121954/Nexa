@extends('layouts.app')

@section('title', 'Mensajes — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/messages.css') }}">
@endpush

@section('content')

<x-topbar />

{{-- ═══ MESSAGES PAGE ═══ --}}
<div class="messages-page" id="messages-page">

    {{-- ─── SIDEBAR ─── --}}
    <aside class="msg-sidebar" id="msg-sidebar">

        {{-- Cabecera --}}
        <div class="msg-sidebar-header">
            <h2>Mensajes</h2>
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div class="msg-search-wrap" style="flex:1;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" stroke-linecap="round" />
                    </svg>
                    <input type="text" id="msg-search-input" placeholder="Buscar mensajes..." autocomplete="off">
                </div>
                <button class="msg-filter-btn" title="Filtrar" type="button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="msg-tabs" id="msg-tabs">
            <button class="msg-tab active" data-tab="all" type="button">Todas</button>
            <button class="msg-tab" data-tab="matches" type="button">Coincidencias</button>
            <button class="msg-tab" data-tab="unread" type="button">No leídos</button>
        </div>

        {{-- Lista de conversaciones --}}
        <div class="msg-conv-list" id="msg-conv-list">

            @forelse($conversations ?? [] as $conv)
            <div class="msg-conv-item {{ $loop->first ? 'active' : '' }}"
                data-conv-id="{{ $conv->id }}"
                data-user-id="{{ $conv->otherUser->id }}"
                data-user-name="{{ $conv->otherUser->name }}"
                data-user-avatar="{{ $conv->otherUser->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($conv->otherUser->name).'&background=E8375A&color=fff' }}"
                data-online="{{ $conv->otherUser->is_online ? 'true' : 'false' }}"
                data-tab-all="true"
                data-tab-matches="{{ ($conv->is_match ?? false) ? 'true' : 'false' }}"
                data-tab-unread="{{ ($conv->unread_count ?? 0) > 0 ? 'true' : 'false' }}">

                <div class="msg-conv-avatar">
                    <img src="{{ $conv->otherUser->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($conv->otherUser->name).'&background=E8375A&color=fff' }}"
                        alt="{{ $conv->otherUser->name }}">
                    @if($conv->otherUser->is_online ?? false)
                    <span class="msg-status-dot"></span>
                    @endif
                </div>

                <div class="msg-conv-info">
                    <div class="msg-conv-top">
                        <span class="msg-conv-name">{{ Str::words($conv->otherUser->name, 1, '') }}</span>
                        <span class="msg-conv-time">{{ $conv->lastMessage?->created_at?->diffForHumans(null, true) ?? '' }}</span>
                    </div>
                    <div class="msg-conv-bottom">
                        <span class="msg-conv-preview {{ ($conv->unread_count ?? 0) > 0 ? 'unread' : '' }}">
                            {{ Str::limit($conv->lastMessage?->body ?? '…', 35) }}
                        </span>
                        @if(($conv->unread_count ?? 0) > 0)
                        <span class="msg-unread-badge">{{ $conv->unread_count }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="msg-empty-state">
                <div class="msg-empty-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <p class="msg-empty-title">Aún no hay mensajes</p>
                <p class="msg-empty-sub">Cuando hagas match con alguien podrán escribirse aquí.</p>
                <a href="{{ route('explore.index') }}" class="msg-empty-cta">Explorar personas</a>
            </div>
            @endforelse

        </div>
    </aside>

    {{-- ─── CHAT PANEL ─── --}}
    <section class="msg-chat-panel hidden-mobile" id="msg-chat-panel">

        {{-- Placeholder: sin conversación seleccionada --}}
        <div class="msg-chat-placeholder" id="msg-chat-placeholder">
            <div class="msg-chat-placeholder-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <p class="msg-chat-placeholder-title">Tus mensajes</p>
            <p class="msg-chat-placeholder-sub">Selecciona una conversación para empezar a chatear.</p>
        </div>

        {{-- Chat activo --}}
        <div id="msg-active-chat" style="display:none; flex-direction:column; height:100%;">

            {{-- Header --}}
            <div class="msg-chat-header">
                <div class="msg-chat-user">
                    <button class="msg-back-btn" id="msg-back-btn" type="button" aria-label="Volver">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="msg-chat-user-avatar">
                        <img id="chat-header-avatar" src="" alt="">
                        <span class="msg-status-dot" id="chat-header-dot" style="display:none;"></span>
                    </div>
                    <div class="msg-chat-user-info">
                        <div class="msg-chat-user-name">
                            <span id="chat-header-name"></span>
                            <span class="msg-online-badge" id="chat-header-badge" style="display:none;"></span>
                        </div>
                        <div class="msg-chat-user-status" id="chat-header-status"></div>
                    </div>
                </div>

                <!-- <div class="msg-chat-actions">
                    <button class="msg-action-btn" title="Llamada de voz" type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.63 19.79 19.79 0 01.12 2.18 2 2 0 012.1 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button class="msg-action-btn" title="Videollamada" type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <polygon points="23 7 16 12 23 17 23 7" />
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
                        </svg>
                    </button>
                    <button class="msg-action-btn" title="Información" type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                    </button>
                </div> -->
            </div>

            {{-- Cuerpo mensajes --}}
            <div class="msg-chat-body" id="msg-chat-body">
                {{-- Mensajes se inyectan vía JS --}}
            </div>

            {{-- Footer / Input --}}
            <div class="msg-chat-footer">
                    <div class="msg-input-wrap">
                        <button class="msg-attach-btn" type="button" aria-label="Adjuntar">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.19 9.19a2 2 0 01-2.83-2.83l8.49-8.48" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                        <input type="text" id="msg-text-input" class="msg-text-input" placeholder="Escribe un mensaje...">
                        <button class="msg-send-btn" id="msg-send-btn" disabled type="button" aria-label="Enviar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </div>

                <div class="msg-privacy-note">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0110 0v4" />
                    </svg>
                    Tus conversaciones están protegidas y son privadas.
                </div>
            </div>

        </div>
    </section>

</div>

@endsection

@push('scripts')
<script src="{{ asset('js/messages/state.js') }}"></script>
<script src="{{ asset('js/messages/ui.js') }}"></script>
<script src="{{ asset('js/messages/api.js') }}"></script>
<script src="{{ asset('js/messages/websocket.js') }}"></script>
<script src="{{ asset('js/messages/events.js') }}"></script>
<script src="{{ asset('js/messages/index.js') }}"></script>
@endpush
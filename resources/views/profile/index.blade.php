@extends('layouts.app')

@section('title', 'Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/explore.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')

@include('components.topbar')

<div class="bg-gray-50 min-h-screen pb-16">

    <!-- HEADER -->
    <div class="profile-header">

        <!-- Banner -->
        <div class="profile-banner"
            style="background-image: url('{{ asset('img/fondo.png') }}');">
        </div>

        <!-- Avatar -->
        <div class="profile-avatar-container">
            <div class="relative">
                <img
                    src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff' }}"
                    class="profile-avatar">

                <!-- cámara -->
                <div class="profile-avatar-camera">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M4 7h3l2-3h6l2 3h3v12H4V7z" />
                        <circle cx="12" cy="13" r="3" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- BOTÓN EDITAR -->
        <div class="profile-edit-btn">
            <button
                onclick="window.openModal && window.openModal()"
                type="button"
                class="bg-white px-4 py-2 rounded-full shadow text-sm hover:bg-gray-100 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                    <path d="M18.5 2.5l3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Editar
            </button>
        </div>

    </div>

    <!-- INFO -->
    <div class="profile-info">
        <div class="profile-info-card">

            <h2 class="profile-name">
                {{ $user->name }}
            </h2>

            <p class="profile-username">
                {{ '@' . ($user->name ?? 'usuario') }}
            </p>

            <p class="profile-bio">
                {{ $profile->bio ?? 'Sin biografía' }}
            </p>

            <!-- ICONOS INFO -->
            <div class="profile-info-icons">

                <!-- ciudad -->
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    {{ $profile->city ?? 'Sin ciudad' }}
                </span>

                <!-- edad -->
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 6v6l4 2" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                    {{ $profile && $profile->age ? $profile->age.' años' : 'Edad no definida' }}
                </span>

                <!-- género -->
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="7" r="4" />
                        <path d="M12 11v10" />
                    </svg>
                    <p>{{ $profile->gender == 'female' ? 'Femenino' : ($profile->gender == 'male' ? 'Masculino' : 'No especificado') }}</p>
                </span>

            </div>

            @if($profile && $profile->pronouns)
            <p class="profile-pronouns">
                {{ $profile->pronouns }}
            </p>
            @endif

        </div>
    </div>
    @if(session('error'))
    <div class="error-message mx-6">
        {{ session('error') }}
    </div>
    @endif
    <!-- GALERÍA -->
    <div class="profile-gallery">

        <h3 class="gallery-title">Galería</h3>

        <!-- SUBIR FOTO -->
        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="gallery-upload">
            @csrf

            <label class="flex items-center justify-center w-full h-20 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition gap-2">

                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1" />
                    <path d="M12 12V4" />
                    <path d="M8 8l4-4 4 4" />
                </svg>

                <span class="text-gray-500 text-sm">Agregar foto</span>

                <input type="file" name="photo" class="hidden" accept="image/*" onchange="this.form.submit()">
            </label>
        </form>

        <!-- GRID -->
        <div class="gallery-grid">

            @forelse($user->photos as $photo)

            <div class="gallery-item">

                <img
                    src="{{ str_starts_with($photo->path, 'http') ? $photo->path : Storage::url($photo->path) }}">

                <!-- overlay -->
                <div class="gallery-item-overlay"></div>

                <!-- eliminar -->
                <form action="{{ route('profile.photo.delete', $photo->id) }}" method="POST"
                    class="gallery-item-delete">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="bg-white/90 hover:bg-white p-2 rounded-full shadow border-0">

                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>

                    </button>

                </form>

            </div>

            @empty

            <div class="gallery-empty">
                No hay fotos aún 📸
            </div>

            @endforelse

        </div>

    </div>

    <!-- MODAL EDITAR PERFIL -->
    <div id="profileModal" class="modal-overlay">
        <div class="modal-backdrop"></div>

        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h2>Editar perfil</h2>
                <button id="closeModalBtn" type="button"
                    class="modal-close-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="modal-body">
                @include('profile.partials.update-profile-form')
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/profile/modal.js') }}"></script>
<script src="{{ asset('js/profile/tabs.js') }}"></script>
<script src="{{ asset('js/profile/bioCounter.js') }}"></script>
<script src="{{ asset('js/profile/successMessage.js') }}"></script>
<script src="{{ asset('js/profile/index.js') }}"></script>

@endsection
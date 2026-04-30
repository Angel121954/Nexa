@extends('layouts.app')

@section('title', $user->name . ' — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
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
            <img
                src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E8375A&color=fff' }}"
                class="profile-avatar">
        </div>

    </div>

    <!-- INFO -->
    <div class="profile-info">
        <div class="profile-info-card">

            <h2 class="profile-name">
                {{ $user->name }}
            </h2>

            <p class="profile-bio">
                {{ $profile?->bio ?? 'Sin biografía' }}
            </p>

            <!-- ICONOS INFO -->
            <div class="profile-info-icons">

                <!-- ciudad -->
                @if($profile?->city)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    {{ $profile->city }}
                </span>
                @endif

                <!-- edad -->
                @if($profile?->birth_date)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M12 6v6l4 2" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                    {{ $profile->age }} años
                </span>
                @endif

                <!-- género -->
                @if($profile?->gender)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="7" r="4" />
                        <path d="M12 11v10" />
                    </svg>
                    <p>{{ $profile->gender == 'female' ? 'Femenino' : ($profile->gender == 'male' ? 'Masculino' : 'No especificado') }}</p>
                </span>
                @endif

            </div>

            @if($profile?->pronouns)
            <p class="profile-pronouns">
                {{ $profile->pronouns }}
            </p>
            @endif

        </div>
    </div>

    <!-- GALERÍA -->
    @if($user->photos->count())
    <div class="profile-gallery">

        <h3 class="gallery-title">Galería</h3>

        <div class="gallery-grid">
            @foreach($user->photos as $photo)
            <div class="gallery-item">
                <img src="{{ str_starts_with($photo->path, 'http') ? $photo->path : Storage::url($photo->path) }}">
            </div>
            @endforeach
        </div>

    </div>
    @endif

</div>

@endsection

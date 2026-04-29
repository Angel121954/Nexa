@extends('layouts.app')

@section('title', 'Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/explore.css') }}">
@endpush

@section('content')

@include('components.topbar', [
'onlyLogoAvatar' => true
])

<div class="bg-gray-50 min-h-screen pb-16">

    <!-- HEADER -->
    <div class="relative mb-24">

        <!-- Banner -->
        <div class="bg-gradient-to-r from-pink-500 via-rose-400 to-pink-400 h-[260px] rounded-b-[30px] shadow-lg"></div>

        <!-- Avatar -->
        <div class="absolute left-1/2 -translate-x-1/2 bottom-0 translate-y-1/2">
            <div class="relative">
                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff' }}"
                    alt="{{ auth()->user()->name }}"
                    class="w-36 h-36 rounded-full border-[5px] border-white object-cover shadow-xl">

                <!-- Icono cámara -->
                <div class="absolute bottom-2 right-2 bg-pink-500 p-2 rounded-full border-2 border-white shadow-md cursor-pointer hover:scale-110 transition">
                    📷
                </div>
            </div>
        </div>

        <!-- Botón editar -->
        <div class="absolute right-6 top-4">
            <a href="{{ route('profile.edit') }}"
                class="bg-white px-4 py-2 rounded-full shadow text-sm hover:bg-gray-100">
                Editar
            </a>
        </div>

    </div>

    <!-- INFO -->
    <div class="px-6">

        <div class="bg-white rounded-2xl shadow-md p-6 text-center">

            <h2 class="text-2xl font-bold text-gray-800">
                {{ $user->name }}
            </h2>

            <p class="text-gray-400 text-sm mt-1">
                {{ '@' . ($user->username ?? 'usuario') }}
            </p>

            <p class="text-gray-600 mt-4 text-sm leading-relaxed">
                {{ $profile->bio ?? 'Sin biografía' }}
            </p>

            <div class="flex justify-center gap-6 mt-5 text-sm text-gray-500">

                <span>📍 {{ $profile->city ?? 'Sin ciudad' }}</span>

                <span>
                    🎂 {{ $profile && $profile->age 
                        ? $profile->age . ' años' 
                        : 'Edad no definida' 
                    }}
                </span>

                <span>👤 {{ $profile->gender ?? 'No especificado' }}</span>

            </div>

            @if($profile && $profile->pronouns)
            <p class="mt-3 text-xs text-gray-400">
                {{ $profile->pronouns }}
            </p>
            @endif

        </div>

    </div>

    <!-- GALERÍA -->
    <div class="px-6 mt-6">

        <h3 class="font-semibold text-sm mb-3 text-gray-700">
            Galería
        </h3>

        <div class="grid grid-cols-3 gap-3">

            {{-- FOTOS REALES --}}
            @forelse($user->photos as $photo)
            <div class="relative group">

                <img
                    src="{{ asset('storage/'.$photo->path) }}"
                    class="w-full h-28 object-cover rounded-xl shadow-sm">

                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-xl"></div>

                <!-- Botón eliminar -->
                <button
                    class="absolute top-2 right-2 bg-white text-gray-700 text-xs rounded-full p-1 shadow opacity-0 group-hover:opacity-100 transition">
                    ✕
                </button>

            </div>

            {{-- FOTOS FICTICIAS --}}
            @empty
            @for($i = 1; $i <= 6; $i++)
                <img
                src="https://picsum.photos/300?random={{ $i }}"
                class="w-full h-28 object-cover rounded-xl shadow-sm">
                @endfor
                @endforelse

        </div>

    </div>

</div>

@endsection
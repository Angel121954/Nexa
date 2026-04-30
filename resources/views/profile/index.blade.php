@extends('layouts.app')

@section('title', 'Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/explore.css') }}">
@endpush

@section('content')

@include('components.topbar')

<div class="bg-gray-50 min-h-screen pb-16">

    <!-- HEADER -->
    <div class="relative mb-24">

        <!-- Banner -->
        <div class="h-[260px] rounded-b-[30px] shadow-lg bg-cover bg-center"
            style="background-image: url('{{ asset('img/fondo.png') }}');">
        </div>

        <!-- Avatar -->
        <div class="absolute left-1/2 -translate-x-1/2 bottom-0 translate-y-1/2">
            <div class="relative">
                <img
                    src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff' }}"
                    class="w-36 h-36 rounded-full border-[5px] border-white object-cover shadow-xl">

                <!-- cámara -->
                <div class="absolute bottom-2 right-2 bg-pink-500 p-2 rounded-full border-2 border-white shadow-md cursor-pointer hover:scale-110 transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M4 7h3l2-3h6l2 3h3v12H4V7z" />
                        <circle cx="12" cy="13" r="3" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- BOTÓN EDITAR -->
        <div class="absolute right-6 top-4">
            <button onclick="openModal()"
                class="bg-white px-4 py-2 rounded-full shadow text-sm hover:bg-gray-100 flex items-center gap-2">

                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                    <path d="M18.5 2.5l3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>

                Editar
            </button>
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

            <p class="text-gray-600 mt-4 text-sm">
                {{ $profile->bio ?? 'Sin biografía' }}
            </p>

            <!-- ICONOS INFO -->
            <div class="flex justify-center gap-6 mt-5 text-sm text-gray-500">

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
                    {{ $profile->gender ?? 'No especificado' }}
                </span>

            </div>

            @if($profile && $profile->pronouns)
            <p class="mt-3 text-xs text-gray-400">
                {{ $profile->pronouns }}
            </p>
            @endif

        </div>
    </div>
    @if(session('error'))
    <div class="mb-3 p-3 bg-red-100 text-red-600 text-sm rounded-xl">
        {{ session('error') }}
    </div>
    @endif
    <!-- GALERÍA -->
    <div class="px-6 mt-6 bg-white rounded-2xl shadow-md p-4">

        <h3 class="font-semibold text-sm mb-3 text-gray-700">Galería</h3>

        <!-- SUBIR FOTO -->
        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="mb-4">
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
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">

            @forelse($user->photos as $photo)

            <div class="relative group overflow-hidden rounded-2xl shadow-sm bg-gray-100">

                <img
                    src="{{ str_starts_with($photo->path, 'http') ? $photo->path : Storage::url($photo->path) }}"
                    class="w-full aspect-square object-cover transition duration-300 group-hover:scale-110">

                <!-- overlay -->
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition"></div>

                <!-- eliminar -->
                <form action="{{ route('profile.photo.delete', $photo->id) }}" method="POST"
                    class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="bg-white/90 hover:bg-white p-2 rounded-full shadow">

                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>

                    </button>

                </form>

            </div>

            @empty

            <div class="col-span-5 text-center text-gray-400 py-6">
                No hay fotos aún 📸
            </div>

            @endforelse

        </div>

    </div>

    <!-- MODAL -->
    <div id="profileModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-lg p-6 relative">

            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500">
                ✕
            </button>

            <h2 class="text-xl font-bold mb-4">Editar perfil</h2>

            <div class="space-y-6 max-h-[70vh] overflow-y-auto">

                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')

            </div>

        </div>

    </div>

</div>

<script src="{{ asset('js/profile.js') }}"></script>

@endsection
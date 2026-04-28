@extends('layouts.guest')

@section('title', 'Completa tu perfil — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/onboarding/basic.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card onboarding-card">

        <div class="auth-logo" style="margin-bottom: 1.25rem;">
            <img src="{{ asset('img/logoNexa.png') }}" alt="Nexa">
        </div>

        {{-- Stepper --}}
        <div class="stepper">
            @foreach([['1','Cuenta'],['2','Perfil básico'],['3','Foto y galería'],['4','Preferencias']] as $i => $step)
            <div class="step-item {{ $i === 0 ? 'completed' : ($i === 1 ? 'active' : '') }}" style="width: 80px;">
                <div class="step-bubble">
                    @if($i === 0)
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 6l3 3 5-5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    @else
                    {{ $step[0] }}
                    @endif
                </div>
                <span class="step-label">{{ $step[1] }}</span>
            </div>
            @endforeach
        </div>

        <h1 class="auth-heading">Completa tu perfil ♡</h1>
        <p class="auth-subheading">Cuéntanos un poco más sobre ti.</p>

        <form method="POST" action="{{ route('onboarding.basic.store') }}">
            @csrf

            {{-- Bio --}}
            <div class="field">
                <label for="bio" class="field-label">Bio</label>
                <textarea id="bio" name="bio" rows="3"
                    class="field-textarea @error('bio') is-invalid @enderror"
                    placeholder="Escribe una breve presentación sobre ti..."
                    maxlength="160"
                    oninput="document.getElementById('bio-count').textContent = this.value.length">{{ old('bio', $profile->bio ?? '') }}</textarea>
                <div class="char-count"><span id="bio-count">{{ strlen(old('bio', $profile->bio ?? '')) }}</span>/160</div>
                @error('bio')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            {{-- Ciudad --}}
            <div class="field">
                <label for="city" class="field-label">Ciudad</label>
                <div class="field-input-wrap">
                    <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 1.5a5 5 0 100 10A5 5 0 008 1.5z" stroke-linecap="round" />
                        <path d="M8 6.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" fill="currentColor" stroke="none" />
                        <path d="M8 11.5v3" stroke-linecap="round" />
                    </svg>
                    <input type="text" id="city" name="city"
                        class="field-input @error('city') is-invalid @enderror"
                        placeholder="Ej: Bogotá, Colombia"
                        value="{{ old('city', $profile->city ?? '') }}" required />
                </div>
                @error('city')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            {{-- Fecha de nacimiento + Edad --}}
            <div class="field-grid-2">
                <div class="field">
                    <label for="birth_date" class="field-label">Fecha de nacimiento</label>
                    <input type="date" id="birth_date" name="birth_date"
                        class="field-input @error('birth_date') is-invalid @enderror"
                        style="padding-left: 12px;"
                        value="{{ old('birth_date', $profile->birth_date?->format('Y-m-d')) }}"
                        max="{{ now()->subYears(18)->format('Y-m-d') }}" required />
                    @error('birth_date')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Edad</label>
                    <input type="text" id="age-display" class="field-input"
                        placeholder="Ej: 24" readonly
                        style="padding-left: 12px; background: var(--bg); cursor: default;"
                        value="{{ $profile->age ?? '' }}" />
                </div>
            </div>

            {{-- Género --}}
            <div class="field">
                <label for="gender" class="field-label">Género</label>
                <select id="gender" name="gender"
                    class="field-select @error('gender') is-invalid @enderror" required>
                    <option value="" disabled {{ old('gender', $profile->gender ?? '') ? '' : 'selected' }}>Selecciona tu género</option>
                    <option value="male" {{ old('gender', $profile->gender) === 'male'       ? 'selected' : '' }}>Masculino</option>
                    <option value="female" {{ old('gender', $profile->gender) === 'female'     ? 'selected' : '' }}>Femenino</option>
                    <option value="non_binary" {{ old('gender', $profile->gender) === 'non_binary' ? 'selected' : '' }}>No binario</option>
                    <option value="other" {{ old('gender', $profile->gender) === 'other'      ? 'selected' : '' }}>Prefiero no decir</option>
                </select>
                @error('gender')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            {{-- Pronombres --}}
            <div class="field">
                <label for="pronouns" class="field-label">
                    Pronombres <span style="font-weight: 400; color: var(--text-muted);">(opcional)</span>
                </label>
                <select id="pronouns" name="pronouns" class="field-select">
                    <option value="">Selecciona tus pronombres</option>
                    <option value="he/him" {{ old('pronouns', $profile->pronouns ?? '') === 'he/him'   ? 'selected' : '' }}>Él / Him</option>
                    <option value="she/her" {{ old('pronouns', $profile->pronouns ?? '') === 'she/her'  ? 'selected' : '' }}>Ella / Her</option>
                    <option value="they/them" {{ old('pronouns', $profile->pronouns ?? '') === 'they/them'? 'selected' : '' }}>Elle / They</option>
                    <option value="any" {{ old('pronouns', $profile->pronouns ?? '') === 'any'      ? 'selected' : '' }}>Cualquiera</option>
                </select>
            </div>

            <div class="onboarding-actions">
                <a href="{{ route('login') }}" class="btn btn-outline">Atrás</a>
                <button type="submit" class="btn btn-primary btn-main">Continuar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('birth_date').addEventListener('change', function() {
        const birth = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        document.getElementById('age-display').value = isNaN(age) || age < 0 ? '' : age;
    });
</script>
@endpush
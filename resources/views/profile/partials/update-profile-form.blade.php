<section id="profileEditForm">
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Tabs -->
    <div class="profile-tabs" id="profileTabs">
        <button type="button" data-tab="info"
            class="profile-tab-btn">
            Información
        </button>
        <button type="button" data-tab="about"
            class="profile-tab-btn">
            Sobre mí
        </button>
        <button type="button" data-tab="prefs"
            class="profile-tab-btn">
            Preferencias
        </button>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Tab: Información básica -->
        <div id="tab-info" class="tab-content active">

            <!-- Nombre -->
            <div class="form-group">
                <label for="name" class="form-label">Nombre completo</label>
                <input id="name" name="name" type="text" required
                    class="form-input"
                    value="{{ old('name', $user->name) }}"
                    autocomplete="name"
                    placeholder="Tu nombre completo">
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>

            <!-- Correo -->
            <div class="form-group">
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" name="email" type="email" required
                    class="form-input"
                    value="{{ old('email', $user->email) }}"
                    autocomplete="username"
                    placeholder="tu@correo.com">
                <x-input-error class="mt-1" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="text-sm mt-2 text-gray-600">
                        Tu correo no está verificado.
                        <button form="send-verification"
                            class="underline text-sm text-pink-600 hover:text-pink-700 font-medium">
                            Reenviar verificación
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            Se envió un nuevo enlace de verificación.
                        </p>
                    @endif
                @endif
            </div>

            <!-- Ciudad -->
            <div class="form-group">
                <label for="city" class="form-label">Ciudad</label>
                <input id="city" name="city" type="text" maxlength="100"
                    class="form-input"
                    value="{{ old('city', $user->city ?? ($profile->city ?? '')) }}"
                    placeholder="Ej. Madrid, Barcelona, Ciudad de México...">
                <x-input-error class="mt-1" :messages="$errors->get('city')" />
            </div>

            <!-- Fecha de nacimiento -->
            <div class="form-group">
                <label for="birth_date" class="form-label">Fecha de nacimiento</label>
                <input id="birth_date" name="birth_date" type="date"
                    class="form-input"
                    value="{{ old('birth_date', ($user->birth_date ?? ($profile->birth_date ?? null)) ? date('Y-m-d', strtotime($user->birth_date ?? $profile->birth_date)) : '') }}">
                <p class="form-hint">Tu edad se calculará automáticamente</p>
                <x-input-error class="mt-1" :messages="$errors->get('birth_date')" />
            </div>
        </div>

        <!-- Tab: Sobre mí -->
        <div id="tab-about" class="tab-content">

            <!-- Género -->
            <div class="form-group">
                <label class="form-label">Género</label>
                <div class="gender-grid">
                    @php $currentGender = old('gender', $user->gender ?? ($profile->gender ?? '')); @endphp
                    <div class="gender-option">
                        <input type="radio" name="gender" value="male" {{ $currentGender == 'male' ? 'checked' : '' }}>
                        <div class="gender-label">
                            <span>Masculino</span>
                        </div>
                    </div>
                    <div class="gender-option">
                        <input type="radio" name="gender" value="female" {{ $currentGender == 'female' ? 'checked' : '' }}>
                        <div class="gender-label">
                            <span>Femenino</span>
                        </div>
                    </div>
                    <div class="gender-option">
                        <input type="radio" name="gender" value="non_binary" {{ $currentGender == 'non_binary' ? 'checked' : '' }}>
                        <div class="gender-label">
                            <span>No binario</span>
                        </div>
                    </div>
                    <div class="gender-option">
                        <input type="radio" name="gender" value="" {{ $currentGender == '' || $currentGender == 'other' ? 'checked' : '' }}>
                        <div class="gender-label">
                            <span>Prefiero no decirlo</span>
                        </div>
                    </div>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('gender')" />
            </div>

            <!-- Pronombres -->
            <div class="form-group mt-4">
                <label for="pronouns" class="form-label">Pronombres</label>
                <input id="pronouns" name="pronouns" type="text" maxlength="50"
                    class="form-input"
                    placeholder="Ej. él, ella, elle, le..."
                    value="{{ old('pronouns', $profile->pronouns ?? '') }}">
                <p class="form-hint">Opcional - Ayuda a otros a dirigirse a ti correctamente</p>
                <x-input-error class="mt-1" :messages="$errors->get('pronouns')" />
            </div>

            <!-- Biografía -->
            <div class="form-group mt-4">
                <label for="bio" class="flex items-center justify-between form-label">
                    <span>Biografía</span>
                    <span class="text-gray-400 font-normal text-xs" id="bioCounter">0/500</span>
                </label>
                <textarea id="bio" name="bio" rows="3" maxlength="500"
                    class="form-textarea"
                    placeholder="Cuéntanos un poco sobre ti, tus intereses, qué te gusta hacer...">{{ old('bio', $user->bio ?? ($profile->bio ?? '')) }}</textarea>
                <p class="form-hint">Máximo 500 caracteres</p>
                <x-input-error class="mt-1" :messages="$errors->get('bio')" />
            </div>
        </div>

        <!-- Tab: Preferencias -->
        <div id="tab-prefs" class="tab-content">

            <div class="form-group">
                <label class="form-label">Busco conectar con</label>
                <p class="form-hint mb-3">Selecciona una o varias opciones</p>
                @php
                    $lookingFor = old('looking_for', $user->looking_for ?? ($profile->looking_for ?? []));
                    if (is_string($lookingFor)) $lookingFor = json_decode($lookingFor, true) ?? [];
                @endphp
                <div class="space-y-2">
                    @foreach(['male' => 'Masculino', 'female' => 'Femenino', 'non_binary' => 'No binario', 'other' => 'Otro / Prefiero no decirlo'] as $value => $label)
                        <div class="checkbox-option">
                            <input type="checkbox" name="looking_for[]" value="{{ $value }}"
                                {{ in_array($value, $lookingFor) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('looking_for')" />
            </div>
        </div>

        <!-- Botón guardar -->
        <div class="form-actions">
            <button type="button" id="cancelBtn"
                class="btn-cancel">
                Cancelar
            </button>
            <button type="submit"
                class="btn-save">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Guardar cambios
            </button>
        </div>

        @if (session('status') === 'profile-updated')
            <p class="success-message" id="successMessage">
                Cambios guardados correctamente.
            </p>
        @endif
    </form>
</section>

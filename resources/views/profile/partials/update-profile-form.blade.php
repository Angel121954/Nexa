@php
    use App\Models\Interest;
    $allInterests    = Interest::orderBy('name')->get();
    $myInterestIds   = $user->interests->pluck('id')->toArray();
@endphp

<section id="profileEditForm">

    {{-- Verificación email (oculta) --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    {{-- ══ TABS ══ --}}
    <div class="pedit-tabs" id="profileTabs">
        <button type="button" data-tab="photos" class="pedit-tab active">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
            </svg>
            Fotos
        </button>
        <button type="button" data-tab="info" class="pedit-tab">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
            </svg>
            Información
        </button>
        <button type="button" data-tab="about" class="pedit-tab">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 20h9M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4L16.5 3.5z" stroke-linecap="round"/>
            </svg>
            Sobre mí
        </button>
        <button type="button" data-tab="interests" class="pedit-tab">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke-linecap="round"/>
            </svg>
            Intereses
        </button>
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- TAB: FOTOS (avatar + banner)           --}}
    {{-- ══════════════════════════════════════ --}}
    <div id="tab-photos" class="pedit-content active">

        {{-- Banner --}}
        <div class="pedit-section">
            <p class="pedit-section-label">Foto de portada</p>
            <div class="pedit-banner-preview" id="bannerPreview"
                 style="background-image:url('{{ $profile?->banner ?? asset('img/fondo.png') }}')">
                <label class="pedit-media-overlay" for="bannerInput">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    <span>Cambiar portada</span>
                </label>
            </div>
            <form action="{{ route('profile.banner') }}" method="POST" enctype="multipart/form-data" id="bannerForm">
                @csrf
                <input type="file" id="bannerInput" name="banner" accept="image/*" class="hidden"
                       onchange="document.getElementById('bannerForm').submit()">
            </form>
            <p class="pedit-hint">Recomendado: 1200×400 px · JPG o PNG · Máx 6 MB</p>
        </div>

        <div class="pedit-divider"></div>

        {{-- Avatar --}}
        <div class="pedit-section">
            <p class="pedit-section-label">Foto de perfil</p>
            <div class="pedit-avatar-row">
                <div class="pedit-avatar-wrap">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=E8375A&color=fff&size=200&bold=true' }}"
                         alt="Avatar" class="pedit-avatar" id="avatarPreviewImg">
                    <label class="pedit-avatar-overlay" for="avatarInput">
                        <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                            <circle cx="12" cy="13" r="3"/>
                        </svg>
                    </label>
                </div>
                <div class="pedit-avatar-info">
                    <p class="pedit-avatar-name">{{ $user->name }}</p>
                    <p class="pedit-hint">JPG, PNG o WebP · Máx 4 MB<br>Se recortará en círculo</p>
                    <label for="avatarInput" class="pedit-btn-outline">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke-linecap="round"/>
                        </svg>
                        Subir foto
                    </label>
                </div>
            </div>
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                @csrf
                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden"
                       onchange="previewAndSubmitAvatar(this)">
            </form>
        </div>

        @if(session('status') === 'avatar-updated')
            <div class="pedit-success">✓ Foto de perfil actualizada</div>
        @endif
        @if(session('status') === 'banner-updated')
            <div class="pedit-success">✓ Portada actualizada</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- TAB: INFORMACIÓN BÁSICA               --}}
    {{-- ══════════════════════════════════════ --}}
    <div id="tab-info" class="pedit-content">
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="pedit-field">
                <label for="name" class="pedit-label">Nombre completo</label>
                <input id="name" name="name" type="text" required class="pedit-input"
                       value="{{ old('name', $user->name) }}" placeholder="Tu nombre completo">
                <x-input-error class="pedit-error" :messages="$errors->get('name')"/>
            </div>

            <div class="pedit-field">
                <label for="email" class="pedit-label">Correo electrónico</label>
                <input id="email" name="email" type="email" required class="pedit-input"
                       value="{{ old('email', $user->email) }}" placeholder="tu@correo.com">
                <x-input-error class="pedit-error" :messages="$errors->get('email')"/>
            </div>

            <div class="pedit-row">
                <div class="pedit-field">
                    <label for="city" class="pedit-label">Ciudad</label>
                    <input id="city" name="city" type="text" class="pedit-input"
                           value="{{ old('city', $profile?->city ?? '') }}" placeholder="Madrid, Bogotá...">
                </div>
                <div class="pedit-field">
                    <label for="birth_date" class="pedit-label">Fecha de nacimiento</label>
                    <input id="birth_date" name="birth_date" type="date" class="pedit-input"
                           value="{{ old('birth_date', $profile?->birth_date ? $profile->birth_date->format('Y-m-d') : '') }}">
                </div>
            </div>

            <div class="pedit-field">
                <label class="pedit-label">Género</label>
                @php $cg = old('gender', $profile?->gender ?? ''); @endphp
                <div class="pedit-gender-grid">
                    @foreach(['male'=>'♂ Masculino','female'=>'♀ Femenino','non_binary'=>'⚧ No binario',''=>'Prefiero no decirlo'] as $val=>$lbl)
                    <label class="pedit-gender-option {{ $cg === $val ? 'selected' : '' }}">
                        <input type="radio" name="gender" value="{{ $val }}" {{ $cg === $val ? 'checked' : '' }} style="display:none">
                        {{ $lbl }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pedit-actions">
                <button type="button" id="cancelBtn" class="pedit-btn-ghost">Cancelar</button>
                <button type="submit" class="pedit-btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round"/>
                    </svg>
                    Guardar
                </button>
            </div>
            @if(session('status') === 'profile-updated')
                <div class="pedit-success">✓ Información actualizada</div>
            @endif
        </form>
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- TAB: SOBRE MÍ                          --}}
    {{-- ══════════════════════════════════════ --}}
    <div id="tab-about" class="pedit-content">
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="pedit-field">
                <label for="bio" class="pedit-label">
                    Biografía
                    <span class="pedit-counter" id="bioCounter">{{ strlen($profile?->bio ?? '') }}/500</span>
                </label>
                <textarea id="bio" name="bio" rows="4" maxlength="500" class="pedit-input pedit-textarea"
                          placeholder="Cuéntanos sobre ti, tus hobbies, qué te apasiona...">{{ old('bio', $profile?->bio ?? '') }}</textarea>
                <p class="pedit-hint">Una buena bio atrae más conexiones 😊</p>
            </div>

            <div class="pedit-field">
                <label for="pronouns" class="pedit-label">Pronombres <span class="pedit-optional">Opcional</span></label>
                <input id="pronouns" name="pronouns" type="text" maxlength="50" class="pedit-input"
                       value="{{ old('pronouns', $profile?->pronouns ?? '') }}" placeholder="él/ella/elle...">
            </div>

            <div class="pedit-field">
                <label class="pedit-label">Busco conectar con</label>
                @php
                    $lf = old('looking_for', $profile?->looking_for ?? []);
                    if (is_string($lf)) $lf = json_decode($lf, true) ?? [];
                @endphp
                <div class="pedit-looking-opts">
                    @foreach(['friends'=>'👫 Amistades','dating'=>'❤️ Pareja','networking'=>'🤝 Networking','activities'=>'🎯 Actividades'] as $val=>$lbl)
                    <label class="pedit-looking-opt {{ in_array($val, $lf) ? 'selected' : '' }}">
                        <input type="checkbox" name="looking_for[]" value="{{ $val }}"
                               {{ in_array($val, $lf) ? 'checked' : '' }} style="display:none">
                        {{ $lbl }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pedit-actions">
                <button type="button" id="cancelBtn2" class="pedit-btn-ghost">Cancelar</button>
                <button type="submit" class="pedit-btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round"/>
                    </svg>
                    Guardar
                </button>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- TAB: INTERESES                         --}}
    {{-- ══════════════════════════════════════ --}}
    <div id="tab-interests" class="pedit-content">
        <p class="pedit-section-label" style="margin-bottom:.75rem">
            Seleccionados: <strong id="interestCount">{{ count($myInterestIds) }}</strong>
        </p>

        <form method="post" action="{{ route('profile.interests') }}" id="interestsForm">
            @csrf
            <div class="pedit-interests-grid">
                @foreach($allInterests as $interest)
                    @php $sel = in_array($interest->id, $myInterestIds); @endphp
                    <label class="pedit-interest-pill {{ $sel ? 'selected' : '' }}"
                           for="int_{{ $interest->id }}">
                        <input type="checkbox" name="interests[]" value="{{ $interest->id }}"
                               id="int_{{ $interest->id }}" {{ $sel ? 'checked' : '' }}
                               style="display:none" onchange="updateInterestCount()">
                        {{ $interest->name }}
                    </label>
                @endforeach
            </div>

            <div class="pedit-actions">
                <p class="pedit-hint" style="flex:1">Elige los que mejor te representen</p>
                <button type="submit" class="pedit-btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round"/>
                    </svg>
                    Guardar intereses
                </button>
            </div>
        </form>

        @if(session('status') === 'interests-updated')
            <div class="pedit-success">✓ Intereses actualizados</div>
        @endif
    </div>

</section>

<style>
/* ═══ EDIT MODAL TABS ═══ */
.pedit-tabs {
    display: flex;
    gap: 2px;
    background: var(--bg);
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 1.25rem;
    overflow-x: auto;
    scrollbar-width: none;
}
.pedit-tabs::-webkit-scrollbar { display: none; }

.pedit-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 8px 10px;
    border-radius: 9px;
    border: none;
    background: none;
    font-size: .8125rem;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    white-space: nowrap;
    transition: all 180ms ease;
}
.pedit-tab:hover { color: var(--text-primary); background: rgba(0,0,0,.04); }
.pedit-tab.active { background: var(--white); color: var(--pink); font-weight: 700;
    box-shadow: 0 1px 4px rgba(0,0,0,.08); }

/* Content panels */
.pedit-content { display: none; }
.pedit-content.active { display: block; }

/* ═══ BANNER PREVIEW ═══ */
.pedit-banner-preview {
    width: 100%; height: 110px;
    border-radius: 12px;
    background-size: cover; background-position: center;
    position: relative; overflow: hidden;
    border: 2px dashed var(--border);
    cursor: pointer;
    margin-bottom: .5rem;
    transition: border-color var(--transition);
}
.pedit-banner-preview:hover { border-color: var(--pink); }

.pedit-media-overlay {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 5px;
    background: rgba(0,0,0,.35);
    color: white; font-size: .8125rem; font-weight: 600;
    cursor: pointer; opacity: 0;
    transition: opacity 200ms ease;
}
.pedit-banner-preview:hover .pedit-media-overlay { opacity: 1; }

/* ═══ AVATAR ROW ═══ */
.pedit-avatar-row { display: flex; align-items: center; gap: 1rem; }
.pedit-avatar-wrap { position: relative; flex-shrink: 0; }
.pedit-avatar {
    width: 72px; height: 72px; border-radius: 50%;
    object-fit: cover; border: 3px solid var(--white);
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
}
.pedit-avatar-overlay {
    position: absolute; inset: 0; border-radius: 50%;
    background: rgba(0,0,0,.4);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; cursor: pointer; transition: opacity 200ms ease;
}
.pedit-avatar-wrap:hover .pedit-avatar-overlay { opacity: 1; }
.pedit-avatar-info { flex: 1; }
.pedit-avatar-name { font-size: .9375rem; font-weight: 700; color: var(--text-primary); margin-bottom: .25rem; }

/* ═══ FIELDS ═══ */
.pedit-section { margin-bottom: 1rem; }
.pedit-section-label {
    font-size: .75rem; font-weight: 700;
    color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: .5rem;
}
.pedit-divider { height: 1px; background: var(--border); margin: 1rem 0; }
.pedit-field { margin-bottom: .875rem; }
.pedit-row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
@media(max-width:480px){ .pedit-row { grid-template-columns: 1fr; } }

.pedit-label {
    display: flex; align-items: center; justify-content: space-between;
    font-size: .8125rem; font-weight: 600; color: var(--text-primary);
    margin-bottom: .35rem;
}
.pedit-optional { font-size: .7rem; font-weight: 400; color: var(--text-muted); }
.pedit-counter { font-size: .7rem; font-weight: 400; color: var(--text-muted); }
.pedit-hint { font-size: .75rem; color: var(--text-muted); margin-top: .25rem; line-height: 1.4; }
.pedit-error { font-size: .75rem; color: #DC2626; margin-top: .25rem; }

.pedit-input {
    width: 100%; padding: 9px 12px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: .875rem;
    font-family: inherit;
    color: var(--text-primary);
    background: var(--white);
    transition: border-color 150ms ease, box-shadow 150ms ease;
    outline: none;
}
.pedit-input:focus { border-color: var(--pink); box-shadow: 0 0 0 3px rgba(232,55,90,.1); }
.pedit-textarea { resize: vertical; min-height: 90px; }

/* Gender grid */
.pedit-gender-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: .4rem;
}
.pedit-gender-option {
    padding: 9px 12px; border-radius: 10px;
    border: 1.5px solid var(--border);
    font-size: .8125rem; font-weight: 500;
    cursor: pointer; text-align: center;
    transition: all 150ms ease; color: var(--text-secondary);
}
.pedit-gender-option:hover { border-color: rgba(232,55,90,.4); color: var(--pink); }
.pedit-gender-option.selected { border-color: var(--pink); background: var(--pink-light); color: var(--pink); font-weight: 700; }

/* Looking for */
.pedit-looking-opts {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: .4rem;
}
.pedit-looking-opt {
    padding: 9px 12px; border-radius: 10px;
    border: 1.5px solid var(--border);
    font-size: .8125rem; font-weight: 500;
    cursor: pointer; text-align: center;
    transition: all 150ms ease; color: var(--text-secondary);
}
.pedit-looking-opt:hover { border-color: rgba(232,55,90,.4); color: var(--pink); }
.pedit-looking-opt.selected { border-color: var(--pink); background: var(--pink-light); color: var(--pink); font-weight: 700; }

/* Interests grid */
.pedit-interests-grid {
    display: flex; flex-wrap: wrap; gap: .4rem;
    margin-bottom: 1rem;
    max-height: 260px; overflow-y: auto;
    padding-right: 4px;
}
.pedit-interest-pill {
    display: inline-flex; align-items: center;
    padding: 6px 14px;
    border-radius: 20px;
    border: 1.5px solid var(--border);
    font-size: .8125rem; font-weight: 500;
    cursor: pointer; color: var(--text-secondary);
    transition: all 150ms ease;
    user-select: none;
}
.pedit-interest-pill:hover { border-color: var(--pink); color: var(--pink); }
.pedit-interest-pill.selected {
    background: var(--pink); border-color: var(--pink);
    color: var(--white); font-weight: 700;
}

/* Actions row */
.pedit-actions {
    display: flex; align-items: center; gap: .75rem;
    padding-top: .875rem;
    border-top: 1px solid var(--border);
    margin-top: .75rem;
}
.pedit-btn-primary {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 20px;
    background: var(--pink); color: var(--white);
    border: none; border-radius: 999px;
    font-size: .875rem; font-weight: 600;
    cursor: pointer;
    transition: all 150ms ease;
}
.pedit-btn-primary:hover { background: var(--pink-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(232,55,90,.4); }
.pedit-btn-ghost {
    padding: 9px 16px;
    background: none; border: 1.5px solid var(--border);
    border-radius: 999px;
    font-size: .875rem; font-weight: 500; color: var(--text-secondary);
    cursor: pointer; transition: all 150ms ease;
}
.pedit-btn-ghost:hover { border-color: var(--text-primary); color: var(--text-primary); }
.pedit-btn-outline {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px;
    border: 1.5px solid var(--border); border-radius: 999px;
    font-size: .8125rem; font-weight: 600; color: var(--text-primary);
    cursor: pointer; transition: all 150ms ease;
    background: var(--white); text-decoration: none;
}
.pedit-btn-outline:hover { border-color: var(--pink); color: var(--pink); }
.hidden { display: none; }

/* Success message */
.pedit-success {
    margin-top: .75rem; padding: .625rem 1rem;
    background: #DCFCE7; color: #16A34A;
    border-radius: 8px; font-size: .8125rem; font-weight: 600;
    border: 1px solid #BBF7D0;
}
</style>

<script>
// ── Tab switching ──────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const tabs    = document.querySelectorAll('.pedit-tab');
    const panels  = document.querySelectorAll('.pedit-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('tab-' + tab.dataset.tab)?.classList.add('active');
        });
    });

    // Gender options toggle
    document.querySelectorAll('.pedit-gender-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.pedit-gender-option').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');
        });
    });

    // Looking for toggle
    document.querySelectorAll('.pedit-looking-opt').forEach(opt => {
        opt.addEventListener('click', () => opt.classList.toggle('selected'));
    });

    // Interest pills toggle
    document.querySelectorAll('.pedit-interest-pill').forEach(pill => {
        pill.addEventListener('click', () => pill.classList.toggle('selected'));
    });

    // Bio counter
    const bioEl   = document.getElementById('bio');
    const counter = document.getElementById('bioCounter');
    if (bioEl && counter) {
        bioEl.addEventListener('input', () => {
            counter.textContent = bioEl.value.length + '/500';
        });
    }

    // Cancel buttons
    document.getElementById('cancelBtn')?.addEventListener('click', () => window.closeModal?.());
    document.getElementById('cancelBtn2')?.addEventListener('click', () => window.closeModal?.());
});

function updateInterestCount() {
    const checked = document.querySelectorAll('#interestsForm input[type=checkbox]:checked').length;
    const el = document.getElementById('interestCount');
    if (el) el.textContent = checked;
}

function previewAndSubmitAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarPreviewImg').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('avatarForm').submit();
    }
}
</script>

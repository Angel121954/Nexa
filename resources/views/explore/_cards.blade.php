{{-- Cards grid --}}
<div class="cards-grid" id="cards-grid">
    @forelse($users as $person)
    @php
    $liked = in_array($person->id, $likedIds);
    $matched = in_array($person->id, $matchIds);
    $photo = (!empty($person->avatar) && filter_var($person->avatar, FILTER_VALIDATE_URL))
    ? $person->avatar
    : 'https://ui-avatars.com/api/?name=' . urlencode($person->name) . '&background=FDE8EE&color=E8375A&size=300';
    $age = $person->profile?->age;
    $city = $person->profile?->city;
    $bio = $person->profile?->bio;
    $tags = $person->interests->take(3);
    @endphp

    <article class="user-card" id="card-{{ $person->id }}">
        <a href="{{ route('profile.show', $person->id) }}" class="card-link">
            <div class="card-photo">
                <img src="{{ $photo }}" alt="{{ $person->name }}" loading="lazy">

                @if($person->id % 3 === 0)
                <span class="card-online" title="En línea"></span>
                @endif
            </div>

            <div class="card-body">
                <p class="card-name">
                    {{ $person->name }}{{ $age ? ', '.$age : '' }}
                    @if($matched)
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--pink)">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                    </svg>
                    @else
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--pink)">
                        <path d="M9 12l2 2 4-4M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2s10 4.48 10 10z" />
                    </svg>
                    @endif
                </p>

                @if($bio)
                <p class="card-bio">{{ $bio }}</p>
                @endif

                @if($tags->count())
                <div class="card-tags">
                    @foreach($tags as $tag)
                    <span class="card-tag">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif

                @if($city)
                <p class="card-location">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke-linecap="round" />
                        <circle cx="12" cy="9" r="2.5" />
                    </svg>
                    {{ $city }}
                </p>
                @endif
            </div>
        </a>

        <button class="card-like-btn {{ $liked ? 'liked' : '' }}"
            id="like-btn-{{ $person->id }}"
            data-user="{{ $person->id }}"
            data-liked="{{ $liked ? '1' : '0' }}"
            data-name="{{ $person->name }}"
            title="{{ $liked ? 'Quitar like' : 'Dar like' }}"
            aria-label="{{ $liked ? 'Quitar like a '.$person->name : 'Dar like a '.$person->name }}">
            <svg width="16" height="16" viewBox="0 0 24 24"
                fill="{{ $liked ? 'currentColor' : 'none' }}"
                stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </article>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.3-4.3"/>
                <line x1="8" y1="11" x2="14" y2="11"/>
            </svg>
        </div>
        <p class="empty-state-title">Sin resultados</p>
        <p class="empty-state-desc">Intenta con otros filtros o términos de búsqueda.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($users->hasPages())
<div class="pagination-wrap" id="pagination-wrap">
    {{ $users->onEachSide(1)->links('vendor.pagination.simple-nexa') }}
</div>
@endif

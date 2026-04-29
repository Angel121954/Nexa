{{-- ═══ PREMIUM MODAL COMPONENT ═══ --}}
<div id="premium-modal" class="pm-overlay" role="dialog" aria-modal="true" aria-label="Nexa Premium" hidden>
    <div class="pm-dialog">

        {{-- LEFT: visual --}}
        <div class="pm-visual">
            <div class="pm-visual-inner">
                <div class="pm-logo-badge">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                        <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z" />
                    </svg>
                </div>
                <h2 class="pm-visual-title">Nexa <span>Premium</span></h2>
                <p class="pm-visual-sub">Conecta más rápido, sin límites</p>

                <ul class="pm-visual-perks">
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Likes ilimitados cada día
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Ve quién te dio like
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Filtros avanzados de búsqueda
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Perfil destacado en el feed
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Mensajes antes del match
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Modo incógnito
                    </li>
                </ul>

                <div class="pm-visual-avatars">
                    <img src="https://ui-avatars.com/api/?name=A&background=fff3&color=fff&size=40" alt="">
                    <img src="https://ui-avatars.com/api/?name=B&background=fff3&color=fff&size=40" alt="">
                    <img src="https://ui-avatars.com/api/?name=C&background=fff3&color=fff&size=40" alt="">
                    <span>+2.4k activos hoy</span>
                </div>
            </div>
        </div>

        {{-- RIGHT: plans --}}
        <div class="pm-plans">
            <button class="pm-close" id="close-premium-modal" aria-label="Cerrar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18" stroke-linecap="round" />
                    <line x1="6" y1="6" x2="18" y2="18" stroke-linecap="round" />
                </svg>
            </button>

            <div class="pm-plans-head">
                <p class="pm-eyebrow">Elige tu plan</p>
                <h3 class="pm-plans-title">Empieza hoy</h3>

                {{-- Billing toggle --}}
                <div class="pm-toggle" id="billing-toggle">
                    <button class="pm-toggle-btn active" data-period="monthly">Mensual</button>
                    <button class="pm-toggle-btn" data-period="annual">
                        Anual
                        <span class="pm-badge">-40%</span>
                    </button>
                </div>
            </div>

            <div class="pm-cards">
                {{-- PLUS --}}
                <div class="pm-card">
                    <div class="pm-card-head">
                        <span class="pm-card-name">Plus</span>
                    </div>
                    <div class="pm-card-price">
                        <span class="pm-price" data-monthly="$9.99" data-annual="$5.99">$9.99</span>
                        <span class="pm-period">/mes</span>
                    </div>
                    <p class="pm-card-desc">Lo esencial para acelerar tus conexiones.</p>
                    <button class="pm-cta">Obtener Plus</button>
                </div>

                {{-- GOLD --}}
                <div class="pm-card featured">
                    <div class="pm-card-badge">Popular</div>
                    <div class="pm-card-head">
                        <span class="pm-card-name">Gold</span>
                    </div>
                    <div class="pm-card-price">
                        <span class="pm-price" data-monthly="$19.99" data-annual="$11.99">$19.99</span>
                        <span class="pm-period">/mes</span>
                    </div>
                    <p class="pm-card-desc">La experiencia completa, destaca entre todos.</p>
                    <button class="pm-cta">Obtener Gold</button>
                </div>

                {{-- ULTIMATE --}}
                <div class="pm-card">
                    <div class="pm-card-head">
                        <span class="pm-card-name">Ultimate</span>
                    </div>
                    <div class="pm-card-price">
                        <span class="pm-price" data-monthly="$29.99" data-annual="$17.99">$29.99</span>
                        <span class="pm-period">/mes</span>
                    </div>
                    <p class="pm-card-desc">Lo máximo: perfil destacado 24/7 y más.</p>
                    <button class="pm-cta">Obtener Ultimate</button>
                </div>
            </div>

            <p class="pm-disclaimer">Cancela cuando quieras. Sin compromisos.</p>
        </div>
    </div>
</div>

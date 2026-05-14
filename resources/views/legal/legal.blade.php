@extends('layouts.app')

@section('title', 'Términos y Privacidad — Nexa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/legal.css') }}">
@endpush

@section('content')

<x-topbar :onlyLogoAvatar="true" />

{{-- ═══ HERO ═══ --}}
<div class="legal-page">

    <div class="legal-hero">
        <div class="legal-hero-eyebrow">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Documentos legales
        </div>
        <h1>Tus derechos y <span>nuestra responsabilidad</span></h1>
        <p class="legal-hero-meta">Última actualización: {{ now()->translatedFormat('d \d\e F \d\e Y') }} &nbsp;·&nbsp; Versión 1.0</p>

        {{-- Tab strip --}}
        <div class="legal-tabs" role="tablist">
            <button class="legal-tab active"
                role="tab"
                aria-selected="true"
                data-panel="terms"
                type="button">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke-linecap="round" />
                    <polyline points="14 2 14 8 20 8" stroke-linecap="round" stroke-linejoin="round" />
                    <line x1="16" y1="13" x2="8" y2="13" stroke-linecap="round" />
                    <line x1="16" y1="17" x2="8" y2="17" stroke-linecap="round" />
                </svg>
                Términos de uso
            </button>
            <button class="legal-tab"
                role="tab"
                aria-selected="false"
                data-panel="privacy"
                type="button">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Política de privacidad
            </button>
        </div>
    </div>

    {{-- ═══ BODY ═══ --}}
    <div class="legal-body">

        {{-- ══════════════════════════════════════
             PANEL: TÉRMINOS DE USO
        ══════════════════════════════════════ --}}
        <div class="legal-panel active" id="panel-terms">

            {{-- TOC --}}
            <aside class="legal-toc" aria-label="Tabla de contenido">
                <div class="legal-toc-inner">
                    <p class="legal-toc-title">En esta sección</p>
                    <ul class="legal-toc-list" id="toc-terms">
                        <li><a href="#t1" class="toc-active"><span class="toc-num">01</span> Aceptación</a></li>
                        <li><a href="#t2"><span class="toc-num">02</span> Descripción del servicio</a></li>
                        <li><a href="#t3"><span class="toc-num">03</span> Cuenta de usuario</a></li>
                        <li><a href="#t4"><span class="toc-num">04</span> Uso aceptable</a></li>
                        <li><a href="#t5"><span class="toc-num">05</span> Contenido del usuario</a></li>
                        <li><a href="#t6"><span class="toc-num">06</span> Propiedad intelectual</a></li>
                        <li><a href="#t7"><span class="toc-num">07</span> Suspensión y cancelación</a></li>
                        <li><a href="#t8"><span class="toc-num">08</span> Limitación de responsabilidad</a></li>
                        <li><a href="#t9"><span class="toc-num">09</span> Modificaciones</a></li>
                        <li><a href="#t10"><span class="toc-num">10</span> Ley aplicable</a></li>
                    </ul>
                </div>
            </aside>

            {{-- Sections --}}
            <div class="legal-sections">

                {{-- 01 --}}
                <article class="legal-section" id="t1">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 01</p>
                            <h2 class="section-title">Aceptación de los términos</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Al registrarte, acceder o usar <strong>Nexa</strong>, aceptas quedar vinculado por estos Términos de Uso. Si no estás de acuerdo con alguna parte de estos términos, no debes utilizar la plataforma.</p>
                        <div class="notice-block">
                            <p><strong>Importante:</strong> el uso de Nexa está restringido a personas mayores de 18 años. Al crear una cuenta confirmas que cumples con este requisito.</p>
                        </div>
                        <p>Estos términos constituyen un acuerdo legal entre tú (<strong>"Usuario"</strong>) y <strong>Nexa</strong> (<strong>"nosotros"</strong>, <strong>"la plataforma"</strong>). Aplican a todas las funciones disponibles, incluyendo perfiles, mensajes, exploración y cualquier servicio Premium.</p>
                    </div>
                </article>

                {{-- 02 --}}
                <article class="legal-section" id="t2">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" stroke-linecap="round" />
                                <line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round" />
                                <line x1="12" y1="16" x2="12.01" y2="16" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 02</p>
                            <h2 class="section-title">Descripción del servicio</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Nexa es una red social diseñada para que personas auténticas se conecten basándose en intereses comunes, afinidad y cercanía. La plataforma ofrece:</p>
                        <ul>
                            <li>Perfil personal con foto, información y preferencias</li>
                            <li>Exploración de otros usuarios según filtros de intereses, ciudad y edad</li>
                            <li>Sistema de "me gusta" y conexiones mutuas</li>
                            <li>Mensajería privada en tiempo real</li>
                            <li>Funciones Premium con beneficios adicionales</li>
                        </ul>
                        <p>Nos reservamos el derecho de modificar, suspender o descontinuar cualquier función del servicio en cualquier momento, con o sin aviso previo.</p>
                    </div>
                </article>

                {{-- 03 --}}
                <article class="legal-section" id="t3">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="7" r="4" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 03</p>
                            <h2 class="section-title">Cuenta de usuario</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Para acceder a las funciones de Nexa debes crear una cuenta. Al registrarte te comprometes a:</p>
                        <ul>
                            <li>Proporcionar información veraz, completa y actualizada</li>
                            <li>Mantener la seguridad de tu contraseña y no compartirla</li>
                            <li>Notificarnos de inmediato ante cualquier uso no autorizado</li>
                            <li>No crear cuentas falsas, duplicadas ni automatizadas</li>
                        </ul>
                        <p>Eres el único responsable de toda la actividad que ocurra bajo tu cuenta. Nexa no se hace responsable por pérdidas derivadas del acceso no autorizado causado por tu negligencia.</p>
                        <div class="notice-block">
                            <p><strong>Autenticación en dos pasos:</strong> te recomendamos activar 2FA desde tu perfil para proteger tu cuenta con una capa adicional de seguridad.</p>
                        </div>
                    </div>
                </article>

                {{-- 04 --}}
                <article class="legal-section" id="t4">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 04</p>
                            <h2 class="section-title">Conducta y uso aceptable</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Al usar Nexa <strong>no debes</strong> realizar ninguna de las siguientes acciones:</p>
                        <ul>
                            <li>Acosar, intimidar o amenazar a otros usuarios</li>
                            <li>Publicar contenido ofensivo, discriminatorio, violento o sexual explícito</li>
                            <li>Suplantar a otras personas o entidades</li>
                            <li>Usar la plataforma con fines comerciales no autorizados o spam</li>
                            <li>Intentar acceder a sistemas, datos o cuentas ajenas</li>
                            <li>Usar bots, scrapers u otras herramientas automatizadas</li>
                            <li>Publicar información falsa o engañosa deliberadamente</li>
                        </ul>
                        <div class="tag-list">
                            <span class="tag-pill">Tolerancia cero al acoso</span>
                            <span class="tag-pill">Sin spam</span>
                            <span class="tag-pill">Sin bots</span>
                            <span class="tag-pill">Sin suplantación</span>
                        </div>
                        <p>El incumplimiento puede resultar en la suspensión o eliminación permanente de tu cuenta, sin perjuicio de las acciones legales que correspondan.</p>
                    </div>
                </article>

                {{-- 05 --}}
                <article class="legal-section" id="t5">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 05</p>
                            <h2 class="section-title">Contenido del usuario</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Todo el contenido que subas a Nexa (fotos, texto, intereses) sigue siendo de tu propiedad. Sin embargo, al publicarlo nos otorgas una <strong>licencia no exclusiva, mundial y libre de regalías</strong> para mostrarlo dentro de la plataforma con el único fin de prestar el servicio.</p>
                        <p>Eres responsable de que tu contenido no infrinja derechos de terceros (derechos de autor, privacidad, imagen) ni viole las leyes aplicables.</p>
                        <p>Nexa puede eliminar cualquier contenido que infrinja estos términos sin previo aviso.</p>
                    </div>
                </article>

                {{-- 06 --}}
                <article class="legal-section" id="t6">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4l3 3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 06</p>
                            <h2 class="section-title">Propiedad intelectual</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>El nombre <strong>Nexa</strong>, el logotipo, la interfaz, el diseño y todos los elementos visuales de la plataforma son propiedad exclusiva de Nexa y están protegidos por las leyes de propiedad intelectual aplicables.</p>
                        <p>No puedes reproducir, distribuir, modificar ni crear obras derivadas de ningún elemento de la plataforma sin autorización expresa y por escrito.</p>
                    </div>
                </article>

                {{-- 07 --}}
                <article class="legal-section" id="t7">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 07</p>
                            <h2 class="section-title">Suspensión y cancelación</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Puedes eliminar tu cuenta en cualquier momento desde la sección de configuración de perfil. Esto eliminará permanentemente tu información personal y contenido de la plataforma, en los plazos establecidos por nuestra política de retención de datos.</p>
                        <p>Nexa puede suspender o cancelar tu cuenta de manera inmediata si:</p>
                        <ul>
                            <li>Violas estos Términos de Uso</li>
                            <li>Tu comportamiento pone en riesgo la seguridad de otros usuarios</li>
                            <li>Recibes múltiples reportes válidos de la comunidad</li>
                            <li>Se detecta actividad fraudulenta o automatizada</li>
                        </ul>
                    </div>
                </article>

                {{-- 08 --}}
                <article class="legal-section" id="t8">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke-linecap="round" stroke-linejoin="round" />
                                <line x1="12" y1="9" x2="12" y2="13" stroke-linecap="round" />
                                <line x1="12" y1="17" x2="12.01" y2="17" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 08</p>
                            <h2 class="section-title">Limitación de responsabilidad</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Nexa se brinda <strong>"tal como está"</strong> y <strong>"según disponibilidad"</strong>. No garantizamos que el servicio sea ininterrumpido, seguro o libre de errores.</p>
                        <p>En la medida permitida por la ley, Nexa no será responsable por:</p>
                        <ul>
                            <li>Daños indirectos, incidentales o consecuentes derivados del uso de la plataforma</li>
                            <li>Interacciones entre usuarios fuera de la plataforma</li>
                            <li>Pérdida de datos por causas ajenas a nuestra responsabilidad</li>
                            <li>Conductas de terceros o contenido generado por usuarios</li>
                        </ul>
                    </div>
                </article>

                {{-- 09 --}}
                <article class="legal-section" id="t9">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 09</p>
                            <h2 class="section-title">Modificaciones a los términos</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Podemos actualizar estos Términos en cualquier momento. Cuando lo hagamos, actualizaremos la fecha de "última actualización" en la parte superior de esta página y, si los cambios son significativos, te notificaremos por correo electrónico o mediante un aviso en la plataforma.</p>
                        <p>El uso continuado de Nexa después de dichas notificaciones constituye tu aceptación de los términos modificados.</p>
                    </div>
                </article>

                {{-- 10 --}}
                <article class="legal-section" id="t10">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="2" y1="12" x2="22" y2="12" />
                                <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Artículo 10</p>
                            <h2 class="section-title">Ley aplicable y jurisdicción</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Estos Términos se rigen por las leyes de la <strong>República de Colombia</strong>. Cualquier disputa relacionada con estos términos se someterá a los tribunales competentes de Colombia, sin perjuicio de los mecanismos alternativos de resolución de conflictos disponibles.</p>
                        <p>Si alguna disposición de estos Términos es declarada inválida o inaplicable, las demás disposiciones continuarán en plena vigencia.</p>
                    </div>
                </article>

                {{-- Contact --}}
                <div class="legal-contact-card">
                    <div class="contact-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke-linecap="round" stroke-linejoin="round" />
                            <polyline points="22,6 12,13 2,6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3>¿Tienes preguntas sobre los términos?</h3>
                    <p>Nuestro equipo está disponible para resolver cualquier duda sobre el uso de Nexa.</p>
                    <a href="mailto:legal@nexa.app" class="contact-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" stroke-linecap="round" />
                        </svg>
                        Contactar al equipo legal
                    </a>
                </div>

            </div>{{-- /legal-sections --}}
        </div>{{-- /panel-terms --}}


        {{-- ══════════════════════════════════════
             PANEL: POLÍTICA DE PRIVACIDAD
        ══════════════════════════════════════ --}}
        <div class="legal-panel" id="panel-privacy">

            {{-- TOC --}}
            <aside class="legal-toc" aria-label="Tabla de contenido">
                <div class="legal-toc-inner">
                    <p class="legal-toc-title">En esta sección</p>
                    <ul class="legal-toc-list" id="toc-privacy">
                        <li><a href="#p1" class="toc-active"><span class="toc-num">01</span> Datos que recopilamos</a></li>
                        <li><a href="#p2"><span class="toc-num">02</span> Cómo usamos tus datos</a></li>
                        <li><a href="#p3"><span class="toc-num">03</span> Almacenamiento y seguridad</a></li>
                        <li><a href="#p4"><span class="toc-num">04</span> Compartir información</a></li>
                        <li><a href="#p5"><span class="toc-num">05</span> Tus derechos</a></li>
                        <li><a href="#p6"><span class="toc-num">06</span> Cookies y rastreo</a></li>
                        <li><a href="#p7"><span class="toc-num">07</span> Menores de edad</a></li>
                        <li><a href="#p8"><span class="toc-num">08</span> Retención de datos</a></li>
                        <li><a href="#p9"><span class="toc-num">09</span> Cambios a esta política</a></li>
                    </ul>
                </div>
            </aside>

            {{-- Sections --}}
            <div class="legal-sections">

                {{-- P01 --}}
                <article class="legal-section" id="p1">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <ellipse cx="12" cy="5" rx="9" ry="3" />
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" stroke-linecap="round" />
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 01</p>
                            <h2 class="section-title">Datos que recopilamos</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Para brindarte el servicio, recopilamos distintos tipos de información:</p>
                        <p><strong>Datos que tú nos proporcionas:</strong></p>
                        <ul>
                            <li>Nombre, correo electrónico y contraseña al registrarte</li>
                            <li>Información de perfil: foto, biografía, ciudad, fecha de nacimiento, género e intereses</li>
                            <li>Contenido de mensajes enviados dentro de la plataforma</li>
                            <li>Preferencias de privacidad y configuración de cuenta</li>
                        </ul>
                        <p><strong>Datos que recopilamos automáticamente:</strong></p>
                        <ul>
                            <li>Dirección IP y datos del dispositivo (navegador, sistema operativo)</li>
                            <li>Interacciones dentro de la plataforma (likes, visitas a perfiles)</li>
                            <li>Registros de acceso y actividad de sesión</li>
                        </ul>
                    </div>
                </article>

                {{-- P02 --}}
                <article class="legal-section" id="p2">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9" stroke-linecap="round" />
                                <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 02</p>
                            <h2 class="section-title">Cómo usamos tus datos</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Usamos tu información exclusivamente para:</p>
                        <ul>
                            <li>Crear y gestionar tu cuenta de usuario</li>
                            <li>Mostrarte perfiles relevantes según tus intereses y preferencias</li>
                            <li>Entregar mensajes y notificaciones dentro de la plataforma</li>
                            <li>Mejorar la experiencia de uso mediante análisis internos</li>
                            <li>Detectar y prevenir actividades fraudulentas o abusivas</li>
                            <li>Cumplir con nuestras obligaciones legales</li>
                        </ul>
                        <div class="notice-block">
                            <p><strong>Sin publicidad dirigida:</strong> Nexa no vende tus datos a terceros ni los usa para publicidad personalizada de ningún tipo.</p>
                        </div>
                    </div>
                </article>

                {{-- P03 --}}
                <article class="legal-section" id="p3">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0110 0v4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 03</p>
                            <h2 class="section-title">Almacenamiento y seguridad</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>La seguridad de tus datos es nuestra prioridad. Aplicamos las siguientes medidas técnicas y organizativas:</p>
                        <ul>
                            <li><strong>Contraseñas cifradas</strong> con bcrypt; nunca las almacenamos en texto plano</li>
                            <li><strong>Comunicación cifrada</strong> mediante HTTPS/TLS en todas las conexiones</li>
                            <li><strong>Autenticación en dos pasos (2FA)</strong> disponible para todos los usuarios</li>
                            <li><strong>Imágenes almacenadas</strong> en Cloudinary con acceso controlado</li>
                            <li><strong>Tokens de sesión</strong> gestionados de forma segura y con expiración automática</li>
                            <li><strong>Acceso restringido</strong> a la base de datos únicamente a personal autorizado</li>
                        </ul>
                        <p>Sin embargo, ningún sistema es 100% infalible. Si detectas una vulnerabilidad, por favor repórtala a <strong>security@nexa.app</strong>.</p>
                        <div class="tag-list">
                            <span class="tag-pill">HTTPS / TLS</span>
                            <span class="tag-pill">Bcrypt</span>
                            <span class="tag-pill">2FA</span>
                            <span class="tag-pill">Tokens seguros</span>
                            <span class="tag-pill">Cloudinary</span>
                        </div>
                    </div>
                </article>

                {{-- P04 --}}
                <article class="legal-section" id="p4">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 00-3-3.87" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M16 3.13a4 4 0 010 7.75" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 04</p>
                            <h2 class="section-title">Compartir información con terceros</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p><strong>No vendemos, alquilamos ni cedemos</strong> tu información personal a terceros con fines comerciales.</p>
                        <p>Podemos compartir datos únicamente en los siguientes casos limitados:</p>
                        <ul>
                            <li><strong>Proveedores de servicio:</strong> empresas que nos ayudan a operar la plataforma (hosting, almacenamiento de imágenes, envío de correos), bajo acuerdos estrictos de confidencialidad</li>
                            <li><strong>Obligación legal:</strong> cuando la ley o una autoridad competente lo exija</li>
                            <li><strong>Protección de derechos:</strong> para prevenir fraudes, violaciones de seguridad o daños a usuarios</li>
                        </ul>
                    </div>
                </article>

                {{-- P05 --}}
                <article class="legal-section" id="p5">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round" stroke-linejoin="round" />
                                <polyline points="9 12 11 14 15 10" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 05</p>
                            <h2 class="section-title">Tus derechos sobre tus datos</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>De acuerdo con la <strong>Ley 1581 de 2012</strong> (Ley de Protección de Datos Personales de Colombia) y sus decretos reglamentarios, tienes los siguientes derechos:</p>
                        <ul>
                            <li><strong>Acceso:</strong> conocer qué datos personales tenemos sobre ti</li>
                            <li><strong>Rectificación:</strong> corregir información inexacta o incompleta</li>
                            <li><strong>Supresión:</strong> solicitar la eliminación de tus datos personales</li>
                            <li><strong>Portabilidad:</strong> recibir una copia de tus datos en formato legible</li>
                            <li><strong>Oposición:</strong> oponerte a ciertos usos de tu información</li>
                            <li><strong>Revocación del consentimiento:</strong> retirar tu consentimiento en cualquier momento</li>
                        </ul>
                        <p>Para ejercer cualquiera de estos derechos, escríbenos a <strong>privacidad@nexa.app</strong> con el asunto "Derechos HABEAS DATA".</p>
                    </div>
                </article>

                {{-- P06 --}}
                <article class="legal-section" id="p6">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
                                <path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 06</p>
                            <h2 class="section-title">Cookies y tecnologías de rastreo</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Nexa utiliza cookies de sesión estrictamente necesarias para mantener tu sesión activa y garantizar el funcionamiento correcto de la plataforma. <strong>No usamos cookies de rastreo o publicidad de terceros.</strong></p>
                        <p>Puedes configurar tu navegador para rechazar cookies, aunque esto puede afectar el funcionamiento de algunas funciones de la plataforma.</p>
                    </div>
                </article>

                {{-- P07 --}}
                <article class="legal-section" id="p7">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="9" cy="7" r="4" />
                                <line x1="23" y1="11" x2="17" y2="11" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 07</p>
                            <h2 class="section-title">Menores de edad</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Nexa está dirigida exclusivamente a personas mayores de <strong>18 años</strong>. No recopilamos intencionalmente información de menores de edad. Si detectamos que un menor ha creado una cuenta, procederemos a eliminarla de inmediato junto con todos sus datos asociados.</p>
                        <div class="notice-block">
                            <p>Si eres padre, madre o tutor y crees que tu hijo/a puede tener una cuenta en Nexa, contáctanos de inmediato en <strong>privacidad@nexa.app</strong>.</p>
                        </div>
                    </div>
                </article>

                {{-- P08 --}}
                <article class="legal-section" id="p8">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 08</p>
                            <h2 class="section-title">Retención y eliminación de datos</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Conservamos tu información mientras tu cuenta esté activa. Al eliminar tu cuenta:</p>
                        <ul>
                            <li>Tu perfil y foto desaparecen de la plataforma de <strong>forma inmediata</strong></li>
                            <li>Los mensajes intercambiados se eliminan en un plazo de <strong>30 días</strong></li>
                            <li>Los registros técnicos (logs) pueden conservarse hasta <strong>90 días</strong> por motivos de seguridad y cumplimiento legal</li>
                            <li>Ciertos datos pueden retenerse por más tiempo si la ley colombiana lo exige</li>
                        </ul>
                    </div>
                </article>

                {{-- P09 --}}
                <article class="legal-section" id="p9">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="section-meta">
                            <p class="section-num">Sección 09</p>
                            <h2 class="section-title">Cambios a esta política</h2>
                        </div>
                    </div>
                    <div class="section-body">
                        <p>Podemos actualizar esta Política de Privacidad cuando sea necesario. Publicaremos la versión actualizada en esta página con la nueva fecha de revisión. Para cambios significativos, te notificaremos por correo electrónico o mediante un aviso destacado en la plataforma.</p>
                        <p>Te recomendamos revisar esta página periódicamente para mantenerte informado.</p>
                    </div>
                </article>

                {{-- Contact --}}
                <div class="legal-contact-card">
                    <div class="contact-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3>¿Preguntas sobre tu privacidad?</h3>
                    <p>Para ejercer tus derechos HABEAS DATA o reportar un problema de seguridad, escríbenos directamente.</p>
                    <a href="mailto:privacidad@nexa.app" class="contact-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        Contactar al equipo de privacidad
                    </a>
                </div>

            </div>{{-- /legal-sections --}}
        </div>{{-- /panel-privacy --}}

    </div>{{-- /legal-body --}}
</div>{{-- /legal-page --}}

@push('scripts')
<script src="{{ asset('js/legal/legal.js') }}"></script>
@endpush
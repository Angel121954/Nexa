function haversineDistance(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const toRad = (deg) => (deg * Math.PI) / 180;
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

/**
 * Inicia el sistema de tracking GPS del usuario.
 *
 * Flujo:
 * 1. Verifica que exista usuario autenticado.
 * 2. Verifica soporte de geolocalización.
 * 3. Escucha cambios de ubicación en tiempo real.
 * 4. Envía coordenadas al backend Laravel.
 * 5. Laravel calcula ciudad/país automáticamente.
 * 6. Actualiza la UI sin recargar página.
 */

function startLocationTracking() {
    /**
     * Meta usada para confirmar que existe usuario logueado.
     * Ejemplo:
     * <meta name="user-id" content="1">
     */
    const userMeta = document.querySelector('meta[name="user-id"]');

    // Si no hay usuario autenticado, detener.
    if (!userMeta) return;

    /**
     * Verifica soporte GPS del navegador.
     */
    if (!navigator.geolocation) {
        console.log("GPS no soportado");
        return;
    }

    console.log("Iniciando tracking GPS");

    let lastUpdateTime = 0;
    let lastLat = null;
    let lastLng = null;
    const MIN_INTERVAL_MS = 30000; // 30 segundos entre peticiones
    const MIN_DISTANCE_M = 100;    // 100 metros de desplazamiento mínimo

    /**
     * watchPosition escucha cambios de ubicación
     * continuamente en tiempo real.
     */
    navigator.geolocation.watchPosition(
        /**
         * Callback ejecutado cuando cambia la ubicación.
         */
        async (position) => {
            const now = Date.now();

            if (now - lastUpdateTime < MIN_INTERVAL_MS) return;

            /**
             * Coordenadas actuales del usuario.
             */
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Si ya tenemos coordenadas previas, calcular distancia
            if (lastLat !== null && lastLng !== null) {
                const d = haversineDistance(lastLat, lastLng, lat, lng);
                if (d < MIN_DISTANCE_M) return;
            }

            lastLat = lat;
            lastLng = lng;
            lastUpdateTime = now;

            console.log("Nueva ubicación:", lat, lng);

            try {
                /**
                 * Enviar ubicación al backend.
                 *
                 * Laravel:
                 * - guarda coordenadas
                 * - hace reverse geocoding
                 * - detecta ciudad actual
                 * - determina si está viajando
                 */
                const response = await fetch("/api/update-location", {
                    method: "POST",

                    credentials: "same-origin",

                    headers: {
                        "Content-Type": "application/json",

                        /**
                         * Protección CSRF Laravel.
                         */
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        )?.content,

                        Accept: "application/json",
                    },

                    /**
                     * Coordenadas enviadas al backend.
                     */
                    body: JSON.stringify({
                        lat,
                        lng,
                    }),
                });

                /**
                 * Respuesta del backend.
                 *
                 * Ejemplo:
                 * {
                 *   ok: true,
                 *   home_city: "Yumbo",
                 *   current_city: "Bogotá",
                 *   traveling: true
                 * }
                 */
                const data = await response.json();

                console.log("Ubicación actualizada:", data);

                /**
                 * Elemento visual donde se muestra
                 * la ubicación del usuario.
                 */
                const locationElement =
                    document.querySelector("#user-location");

                /**
                 * Actualizar UI automáticamente
                 * sin recargar página.
                 */
                if (locationElement) {
                    /**
                     * Usuario viajando:
                     * ciudad actual diferente
                     * de ciudad origen.
                     */
                    if (data.traveling) {
                        locationElement.innerHTML = `
                            <svg class="w-4 h-4 text-gray-400"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">

                                <path d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z" />

                                <circle cx="12"
                                        cy="10"
                                        r="3" />
                            </svg>

                            <span>
                                ${data.home_city}
                                ·
                                <span class="text-yellow-500">
                                    Visitando
                                    ${data.current_city}
                                </span>
                            </span>
                        `;
                    } else {
                        /**
                         * Usuario en ciudad origen.
                         */
                        locationElement.innerHTML = `
                            <svg class="w-4 h-4 text-gray-400"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">

                                <path d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z" />

                                <circle cx="12"
                                        cy="10"
                                        r="3" />
                            </svg>

                            ${data.home_city}
                        `;
                    }
                }
            } catch (error) {
                /**
                 * Error de red/API.
                 */
                console.error("Error enviando ubicación", error);
            }
        },

        /**
         * Error GPS.
         */
        (error) => {
            console.error("Error GPS:", error);
        },

        /**
         * Configuración GPS.
         */
        {
            /**
             * Máxima precisión posible.
             */
            enableHighAccuracy: true,

            /**
             * No reutilizar cache GPS.
             */
            maximumAge: 0,

            /**
             * Tiempo máximo de espera.
             */
            timeout: 10000,
        },
    );
}

/**
 * Iniciar tracking cuando el DOM
 * termine de cargar.
 */
document.addEventListener("DOMContentLoaded", () => {
    startLocationTracking();
});

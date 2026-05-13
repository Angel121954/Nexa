document.addEventListener("DOMContentLoaded", () => {
    console.log("JScargado");

    if (!navigator.geolocation) {
        console.log("No soporta geolocalización ❌");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            console.log("Ubicación obtenida ", position.coords);

            fetch("/api/update-location", {
                method: "POST",
                credentials: "same-origin", 
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                }),
            })
                .then((res) => res.json())
                .then((data) => console.log("Respuesta servidor:", data))
                .catch((err) => console.error("Error fetch:", err));
        },
        (error) => {
            console.error("Error ubicación:", error);
        },
    );
});

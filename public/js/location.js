function updateUserLocation() {
    const userMeta = document.querySelector('meta[name="user-id"]');
    if (!userMeta) return;

    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            fetch("/api/update-location", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    )?.content,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                }),
            }).catch(() => {});
        },
        () => {},
    );
}

document.addEventListener(
    "click",
    () => {
        updateUserLocation();
    },
    { once: true },
);

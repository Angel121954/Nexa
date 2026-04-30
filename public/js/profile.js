// abrir modal
function openModal() {
    const modal = document.getElementById("profileModal");
    if (!modal) return;

    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

// cerrar modal
function closeModal() {
    const modal = document.getElementById("profileModal");
    if (!modal) return;

    modal.classList.add("hidden");
}

// eventos
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("profileModal");

    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) closeModal();
        });
    }

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") closeModal();
    });
});

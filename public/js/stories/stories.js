// stories.js
document.addEventListener("DOMContentLoaded", () => {
    // ═══ STATE ═══
    const state = {
        storiesData: [],
        currentUserIndex: 0,
        currentStoryIndex: 0,
        progressInterval: null,
        isPaused: false,
        isClosing: false,
    };

    const elements = {
        scroll: document.getElementById("stories-scroll"),
        createBtn: document.getElementById("story-create-btn"),
        uploadInput: document.getElementById("story-upload-input"),
        viewer: document.getElementById("story-viewer"),
        backdrop: document.getElementById("story-viewer-backdrop"),
        closeBtn: document.getElementById("story-viewer-close"),
        progress: document.getElementById("story-viewer-progress"),
        body: document.getElementById("story-viewer-body"),
        media: document.getElementById("story-viewer-media"),
        avatar: document.getElementById("story-viewer-avatar"),
        name: document.getElementById("story-viewer-name"),
        views: document.getElementById("story-viewer-views"),
        tapLeft: document.getElementById("story-tap-left"),
        tapRight: document.getElementById("story-tap-right"),
    };

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const currentUserId = document.querySelector(
        'meta[name="user-id"]',
    )?.content;

    // ═══ HELPERS ═══
    function escapeHtml(text) {
        const d = document.createElement("div");
        d.textContent = text;
        return d.innerHTML;
    }

    // ═══ FETCH STORIES ═══
    async function fetchStories() {
        try {
            const res = await fetch("/api/stories", {
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                credentials: "same-origin",
            });
            if (!res.ok) return [];
            return await res.json();
        } catch {
            return [];
        }
    }

    // ═══ RENDER BAR ═══
    function renderBar(stories) {
        state.storiesData = stories;

        // Remove existing story items (keep the "create" button)
        const existing = elements.scroll.querySelectorAll(
            ".story-avatar:not(.story-create)",
        );
        existing.forEach((el) => el.remove());

        stories.forEach((group, idx) => {
            const div = document.createElement("div");
            div.className = "story-avatar";
            div.dataset.index = idx;
            div.title = group.user.name;

            const ringClass = group.all_viewed
                ? "story-avatar-ring viewed"
                : "story-avatar-ring";

            div.innerHTML = `
                <div class="${ringClass}">
                    <img src="${escapeHtml(group.user.avatar)}" alt="${escapeHtml(group.user.name)}" loading="lazy">
                </div>
                <span class="story-name">${escapeHtml(group.user.name)}</span>
            `;

            div.addEventListener("click", () => openViewer(idx));
            elements.scroll.appendChild(div);
        });
    }

    function getScrollbarWidth() {
        return window.innerWidth - document.documentElement.clientWidth;
    }

    // ═══ OPEN VIEWER ═══
    function openViewer(userIndex) {
        if (state.isClosing) return;
        state.currentUserIndex = userIndex;
        state.currentStoryIndex = 0;
        state.isPaused = false;
        state.isClosing = false;
        elements.viewer.classList.remove("closing");
        elements.viewer.style.display = "flex";
        const sbw = getScrollbarWidth();
        document.body.style.overflow = "hidden";
        document.body.style.paddingRight = sbw + "px";
        showCurrentStory();
        requestAnimationFrame(() => {
            elements.viewer.classList.add("open");
        });
    }

    function closeViewer() {
        if (state.isClosing) return;
        state.isClosing = true;
        clearInterval(state.progressInterval);
        elements.viewer.classList.remove("open");
        elements.viewer.classList.add("closing");
        setTimeout(() => {
            elements.viewer.style.display = "none";
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
            elements.viewer.classList.remove("closing");
            state.isClosing = false;
        }, 350);
    }

    // ═══ SHOW STORY ═══
    function showCurrentStory() {
        const group = state.storiesData[state.currentUserIndex];
        if (!group) {
            closeViewer();
            return;
        }

        const story = group.stories[state.currentStoryIndex];
        if (!story) {
            closeViewer();
            return;
        }

        // Header
        elements.avatar.src = group.user.avatar;
        elements.name.textContent = group.user.name;

        // Media crossfade
        elements.media.classList.add("changing");
        setTimeout(() => {
            elements.media.src = story.media_url;
            elements.media.alt = `Story de ${group.user.name}`;
            elements.media.classList.remove("changing");
        }, 150);

        // Progress segments
        renderProgress(group.stories.length, state.currentStoryIndex);

        // Start progress
        startProgress();

        // Mark as seen (skip for own stories)
        if (String(group.user.id) !== String(currentUserId)) {
            markSeen(story.id);
        }
    }

    function renderProgress(total, activeIndex) {
        let html = "";
        for (let i = 0; i < total; i++) {
            const isActive = i === activeIndex;
            const isPast = i < activeIndex;
            html += `<div class="story-progress-segment">
                <div class="story-progress-fill ${isPast ? "complete" : ""}" id="progress-fill-${i}" style="${isActive ? "width:0%" : isPast ? "width:100%" : "width:0%"}"></div>
            </div>`;
        }
        elements.progress.innerHTML = html;
    }

    function startProgress() {
        clearInterval(state.progressInterval);
        const fill = document.getElementById(
            `progress-fill-${state.currentStoryIndex}`,
        );
        if (!fill) return;

        let width = 0;
        const duration = 5000; // 5 seconds
        const interval = 50; // update every 50ms
        const step = (interval / duration) * 100;

        fill.style.width = "0%";

        state.progressInterval = setInterval(() => {
            if (state.isPaused) return;
            width += step;
            if (width >= 100) {
                width = 100;
                fill.style.width = "100%";
                fill.classList.add("complete");
                clearInterval(state.progressInterval);
                setTimeout(() => goNext(), 300);
            } else {
                fill.style.width = width + "%";
            }
        }, interval);
    }

    // ═══ NAVIGATION ═══
    function goNext() {
        const group = state.storiesData[state.currentUserIndex];
        if (!group) {
            closeViewer();
            return;
        }

        if (state.currentStoryIndex < group.stories.length - 1) {
            state.currentStoryIndex++;
            showCurrentStory();
        } else if (state.currentUserIndex < state.storiesData.length - 1) {
            state.currentUserIndex++;
            state.currentStoryIndex = 0;
            showCurrentStory();
        } else {
            closeViewer();
        }
    }

    function goPrev() {
        if (state.currentStoryIndex > 0) {
            state.currentStoryIndex--;
            showCurrentStory();
        } else if (state.currentUserIndex > 0) {
            state.currentUserIndex--;
            const group = state.storiesData[state.currentUserIndex];
            state.currentStoryIndex = group.stories.length - 1;
            showCurrentStory();
        }
    }

    // ═══ MARK AS SEEN ═══
    async function markSeen(storyId) {
        try {
            await fetch(`/api/stories/${storyId}/seen`, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                credentials: "same-origin",
            });
            // Update the ring in the bar
            const group = state.storiesData[state.currentUserIndex];
            if (group) {
                const story = group.stories[state.currentStoryIndex];
                if (story) story.viewed = true;

                const allViewed = group.stories.every((s) => s.viewed === true);
                group.all_viewed = allViewed;

                const items = elements.scroll.querySelectorAll(
                    ".story-avatar:not(.story-create)",
                );
                const ring =
                    items[state.currentUserIndex]?.querySelector(
                        ".story-avatar-ring",
                    );
                if (ring) {
                    ring.classList.toggle("viewed", allViewed);
                }
            }
        } catch {}
    }

    // ═══ UPLOAD STORY ═══
    async function uploadStory(file) {
        elements.createBtn.classList.add("story-uploading");

        const formData = new FormData();
        formData.append("media", file);

        try {
            const res = await fetch("/api/stories", {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: formData,
                credentials: "same-origin",
            });

            if (res.ok) {
                showToast("Story publicada.", "success");
                const data = await res.json();
                // Refresh the bar silently
                const stories = await fetchStories();
                renderBar(stories);
            } else {
                const data = await res.json();
                showToast(data.error || "Error al publicar story.", "error");
            }
        } catch {
            showToast("Error de conexión.", "error");
        } finally {
            elements.createBtn.classList.remove("story-uploading");
        }
    }

    // ═══ EVENTS ═══
    // Create story: click opens file picker
    elements.createBtn?.addEventListener("click", () => {
        elements.uploadInput?.click();
    });

    elements.uploadInput?.addEventListener("change", () => {
        const file = elements.uploadInput.files[0];
        if (file) {
            uploadStory(file);
            elements.uploadInput.value = "";
        }
    });

    // Close viewer
    elements.closeBtn?.addEventListener("click", closeViewer);
    elements.backdrop?.addEventListener("click", closeViewer);

    // Tap navigation
    elements.tapLeft?.addEventListener("click", (e) => {
        e.stopPropagation();
        goPrev();
    });
    elements.tapRight?.addEventListener("click", (e) => {
        e.stopPropagation();
        goNext();
    });

    // Keyboard
    document.addEventListener("keydown", (e) => {
        if (elements.viewer.style.display === "none") return;
        if (e.key === "Escape") closeViewer();
        if (e.key === "ArrowRight") goNext();
        if (e.key === "ArrowLeft") goPrev();
        if (e.key === " ") {
            e.preventDefault();
            togglePause();
        }
    });

    // Pause on mouse hold
    let holdTimer;
    elements.body?.addEventListener("mousedown", () => {
        state.isPaused = true;
    });
    elements.body?.addEventListener("mouseup", () => {
        state.isPaused = false;
    });
    elements.body?.addEventListener("mouseleave", () => {
        state.isPaused = false;
    });
    // Touch support
    elements.body?.addEventListener(
        "touchstart",
        () => {
            state.isPaused = true;
        },
        { passive: true },
    );

    elements.body?.addEventListener(
        "touchend",
        () => {
            state.isPaused = false;
        },
        { passive: true },
    );

    function togglePause() {
        state.isPaused = !state.isPaused;
    }

    // ═══ ECHO / REALTIME ═══
    if (window.Echo) {
        window.Echo.channel("stories").listen(".StoryCreated", (e) => {
            // Refresh bar when a new story is created
            fetchStories().then((stories) => renderBar(stories));
        });
    }

    // ═══ INIT ═══
    fetchStories().then((stories) => renderBar(stories));
});

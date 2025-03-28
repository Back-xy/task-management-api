import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.Pusher = Pusher;

document.addEventListener("DOMContentLoaded", () => {
    const token = localStorage.getItem("api_token");
    const userId = localStorage.getItem("user_id");
    const signOutBtn = document.getElementById("signout-btn");

    if (!token || !userId) {
        window.location.href = "/login";
        return;
    }

    // Sign out: clear stored values and redirect to login.
    signOutBtn.addEventListener("click", () => {
        localStorage.removeItem("api_token");
        localStorage.removeItem("user_id");
        window.location.href = "/login";
    });

    // Initialize Echo with Reverb configuration.
    window.Echo = new Echo({
        broadcaster: "reverb",
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT || 80,
        wssPort: import.meta.env.VITE_REVERB_PORT || 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
        enabledTransports: ["ws", "wss"],
        auth: {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        },
    });

    // Listen for overdue task events.
    window.Echo.private(`user.${userId}`).listen(".task.overdue", (event) => {
        console.log("Overdue task received!", event.task);
        appendJson(event.task);
    });

    // Append a plain JSON representation of the task.
    function appendJson(task) {
        const container = document.getElementById("alerts-container");
        const pre = document.createElement("pre");
        pre.textContent = JSON.stringify(task, null, 2);
        container.appendChild(pre);
    }
});

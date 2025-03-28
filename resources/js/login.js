document.addEventListener("DOMContentLoaded", () => {
    // If already logged in, redirect to test page.
    const token = localStorage.getItem("api_token");
    if (token) {
        window.location.href = "/test-alert";
        return;
    }

    const form = document.getElementById("login-form");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        try {
            const response = await fetch("/api/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({ email, password }),
            });

            if (!response.ok) {
                const err = await response.json();
                throw new Error(err.message || "Login failed");
            }

            const data = await response.json();
            // Store token and user ID in localStorage
            localStorage.setItem("api_token", data.token);
            localStorage.setItem("user_id", data.user.id);
            alert("Login successful");
            // Redirect to test page
            window.location.href = "/test-alert";
        } catch (error) {
            alert("Error: " + error.message);
        }
    });
});

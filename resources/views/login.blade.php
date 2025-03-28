<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Task Management Overdue Alerts</title>
    @vite('resources/js/login.js')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="max-w-md mx-auto mt-16 p-6 border rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Login</h1>
        <p class="mb-4 text-sm text-gray-600">
            As a Product Owner, log in here to receive real‑time web‑socket alerts when any task you created passes its
            due date.
        </p>
        <form id="login-form">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" id="email" name="email" required
                    class="mt-1 block w-full border border-gray-300 p-2 rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full border border-gray-300 p-2 rounded">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded">Login</button>
        </form>
    </div>
</body>

</html>

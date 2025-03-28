<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Overdue Alerts - Task Management</title>
    @vite('resources/js/test-alert.js')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800">
    <header class="flex justify-between items-center p-4 border-b">
        <h1 class="text-xl font-bold">Overdue Alerts</h1>
        <button id="signout-btn" class="text-sm text-red-600 hover:underline">Sign Out</button>
    </header>
    <main class="p-4">
        <p class="mb-4 text-sm">
            This page displays real‑time alerts sent via web‑socket to the Product Owner when a task passes its due
            date.
        </p>
        <div id="alerts-container"></div>
    </main>
</body>

</html>

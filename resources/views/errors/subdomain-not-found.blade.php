<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subdomain Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <div class="text-6xl mb-4">🚫</div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Subdomain Not Found
                </h1>
                <p class="text-lg text-gray-600 mb-2">
                    The subdomain <span class="font-mono text-indigo-600">{{ request()->getHost() }}</span> does not exist.
                </p>
                <p class="text-sm text-gray-500">
                    This tenant has either been deactivated or never existed.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">What you can do:</h2>
                <ul class="text-left space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Check if you typed the subdomain correctly</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Visit the main platform to create a subscription</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Contact your administrator if you believe this is an error</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-3">
                <a href="https://ideenpipeline.de" 
                   class="block w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    🏠 Go to Main Platform
                </a>
                <a href="https://ideenpipeline.de/tools" 
                   class="block w-full bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                    🔍 Browse Available Tools
                </a>
            </div>

            <div class="mt-8 text-xs text-gray-400">
                <p>Error Code: SUBDOMAIN_NOT_FOUND</p>
                <p>Host: {{ request()->getHost() }}</p>
            </div>
        </div>
    </div>
</body>
</html>

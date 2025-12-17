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
                    Tenant Not Found
                </h1>
                <p class="text-lg text-gray-600 mb-2">
                    The subdomain <span class="font-mono text-indigo-600">{{ $subdomain }}</span> does not exist in this CRM tool.
                </p>
                <p class="text-sm text-gray-500">
                    This tenant may have been deactivated or never existed.
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
                        <span>Contact your administrator</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Visit the main platform to check your subscriptions</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-3">
                <a href="https://ideenpipeline.de" 
                   class="block w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    🏠 Go to Main Platform
                </a>
                <a href="/" 
                   class="block w-full bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                    🔍 View All Tenants
                </a>
            </div>

            <div class="mt-8 text-xs text-gray-400">
                <p>Error: TENANT_NOT_FOUND</p>
                <p>Requested: {{ $host }}</p>
            </div>
        </div>
    </div>
</body>
</html>

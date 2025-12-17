<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade Required</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <svg class="mx-auto h-24 w-24 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Subscription Expired
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Your subscription has expired. Please upgrade to continue using the service.
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold mb-4">Subscription Details</h3>
                <div class="space-y-2 text-left">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Package:</span>
                        <span class="font-semibold">{{ $tenant->package_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Expired:</span>
                        <span class="font-semibold text-red-600">{{ $tenant->expires_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ $upgradeUrl }}" 
                   class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Upgrade Now
                </a>
            </div>

            <p class="text-xs text-gray-500">
                You'll be redirected to the main platform to complete your upgrade
            </p>
        </div>
    </div>
</body>
</html>

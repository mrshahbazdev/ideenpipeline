<?php

return [
    'url' => env('PLATFORM_URL', 'http://127.0.0.1:8000'),
    
    'api_token' => env('PLATFORM_API_TOKEN'),
    
    'tool' => [
        'id' => env('TOOL_ID'),
        'name' => env('APP_NAME', 'CRM Tool'),
        'domain' => env('APP_DOMAIN', 'crm-tool.test'),
    ],
    
    'webhook_endpoints' => [
        'subscription_updated' => '/webhooks/subscription-updated',
        'subscription_cancelled' => '/webhooks/subscription-cancelled',
        'payment_received' => '/webhooks/payment-received',
    ],
];
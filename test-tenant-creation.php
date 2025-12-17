<?php

$apiUrl = 'http://127.0.0.1:8001/api/tenants/create';
$apiToken = 'test-token-12345'; // Match with .env PLATFORM_API_TOKEN

$data = [
    'tenant_id' => 'tenant_' . uniqid(),
    'subdomain' => 'shahbaz',
    'subscription_id' => 1,
    'user_id' => 1,
    'admin_name' => 'Shahbaz',
    'admin_email' => 'shahbaz@example.com',
    'admin_password' => 'password123',
    'package_name' => 'Basic Plan',
    'starts_at' => date('Y-m-d H:i:s'),
    'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
    'metadata' => [
        'created_from' => 'test',
    ],
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

$result = json_decode($response, true);
if ($result) {
    print_r($result);
}
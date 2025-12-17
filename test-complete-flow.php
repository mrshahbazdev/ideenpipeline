<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Tool;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\Transaction;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║        COMPLETE SAAS PLATFORM FLOW TEST                   ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Step 1: Get/Create Test User
echo "👤 Step 1: Getting test user...\n";
$user = User::firstOrCreate(
    ['email' => 'shahbaz@example.com'],
    [
        'name' => 'Shahbaz',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]
);
echo "   ✓ User: {$user->name} ({$user->email})\n\n";

// Step 2: Get Tool and Package
echo "🔧 Step 2: Getting CRM Tool...\n";
$tool = Tool::where('domain', 'crm')->first();

if (!$tool) {
    die("   ✗ CRM Tool not found. Run setup in tinker first.\n");
}

echo "   ✓ Tool: {$tool->name}\n";
echo "   ✓ API URL: {$tool->api_url}\n";

// Test connection
echo "   🔌 Testing connection...\n";
$connected = $tool->checkConnection();
echo "   " . ($connected ? "✓ Connected!" : "✗ Connection failed!") . "\n\n";

if (!$connected) {
    echo "   ⚠️  Make sure tool server is running on http://127.0.0.1:8001\n";
    echo "   Run: cd crm-tool && php artisan serve --port=8001\n\n";
    die();
}

// Get package
$package = $tool->packages()->first();
if (!$package) {
    die("   ✗ No packages found for this tool.\n");
}

echo "📦 Step 3: Using package...\n";
echo "   ✓ Package: {$package->name}\n";
echo "   ✓ Price: €{$package->price}\n";
echo "   ✓ Duration: {$package->duration_value} {$package->duration_type}\n\n";

// Step 4: Create Subscription
echo "📝 Step 4: Creating subscription...\n";
$subdomain = 'test' . rand(1000, 9999);
echo "   ✓ Subdomain: {$subdomain}\n";

$transaction = Transaction::create([
    'user_id' => $user->id,
    'package_id' => $package->id,
    'transaction_id' => 'TEST-' . strtoupper(uniqid()),
    'payment_method' => 'manual',
    'amount' => $package->price,
    'currency' => 'EUR',
    'status' => 'completed',
]);

echo "   ✓ Transaction: {$transaction->transaction_id}\n";

$expiresAt = $package->duration_type === 'lifetime' 
    ? null 
    : now()->addDays($package->duration_value);

$subscription = Subscription::create([
    'user_id' => $user->id,
    'package_id' => $package->id,
    'transaction_id' => $transaction->id,
    'subdomain' => $subdomain,
    'starts_at' => now(),
    'expires_at' => $expiresAt,
    'status' => 'active',
    'admin_email' => $user->email,
]);

echo "   ✓ Subscription ID: {$subscription->id}\n\n";

// Step 5: Create Tenant on Tool Server
echo "🏗️  Step 5: Creating tenant on tool server...\n";

try {
    $tenantId = 'tenant_' . \Illuminate\Support\Str::uuid();
    $adminPassword = 'password123';
    
    echo "   ⏳ Sending request to tool server...\n";
    
    $response = $tool->createTenant([
        'tenant_id' => $tenantId,
        'subdomain' => $subdomain,
        'subscription_id' => $subscription->id,
        'user_id' => $user->id,
        'admin_name' => $user->name,
        'admin_email' => $user->email,
        'admin_password' => $adminPassword,
        'package_name' => $package->name,
        'starts_at' => $subscription->starts_at->toIso8601String(),
        'expires_at' => $subscription->expires_at?->toIso8601String(),
        'metadata' => [
            'platform_subscription_id' => $subscription->id,
            'platform_user_id' => $user->id,
        ],
    ]);
    
    if ($response['success']) {
        echo "   ✓ Tenant created successfully!\n\n";
        
        // Update subscription with tenant info
        $subscription->update([
            'tenant_id' => $tenantId,
            'tenant_database' => $response['data']['database'],
            'admin_password' => encrypt($adminPassword),
            'is_tenant_active' => true,
            'tenant_created_at' => now(),
            'tenant_metadata' => $response,
        ]);
        
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║                    🎉 SUCCESS!                             ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
        
        echo "📊 TENANT DETAILS:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🌐 Tenant URL:    {$response['data']['login_url']}\n";
        echo "🆔 Tenant ID:     {$tenantId}\n";
        echo "📧 Admin Email:   {$user->email}\n";
        echo "🔑 Admin Password: {$adminPassword}\n";
        echo "💾 Database:      {$response['data']['database']}\n";
        echo "📍 Subdomain:     {$subdomain}.{$tool->domain}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        echo "📝 SUBSCRIPTION INFO:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📦 Package:       {$package->name}\n";
        echo "💰 Price:         €{$package->price}\n";
        echo "📅 Starts:        {$subscription->starts_at->format('M d, Y H:i')}\n";
        echo "📅 Expires:       " . ($subscription->expires_at ? $subscription->expires_at->format('M d, Y H:i') : 'Lifetime') . "\n";
        echo "✅ Status:        {$subscription->status}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        echo "🔗 QUICK LINKS:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Main Platform:    http://127.0.0.1:8000\n";
        echo "Tool Server:      http://127.0.0.1:8001\n";
        echo "Tenant Login:     {$response['data']['login_url']}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        echo "✅ NEXT STEPS:\n";
        echo "1. Visit tenant URL and login with above credentials\n";
        echo "2. Access main platform to view subscription\n";
        echo "3. Test subscription expiry and upgrade flow\n\n";
        
    } else {
        echo "   ✗ Tenant creation failed\n";
        echo "   Error: " . ($response['message'] ?? 'Unknown error') . "\n";
    }
    
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "\n   Stack trace:\n";
    echo "   " . $e->getTraceAsString() . "\n";
}
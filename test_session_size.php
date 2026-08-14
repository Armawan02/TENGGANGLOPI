<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    putenv('SESSION_DRIVER=cookie');
    config(['session.driver' => 'cookie']);
    
    $provider = new \App\Providers\FirestoreUserProvider();
    $authUser = $provider->retrieveByCredentials(['email' => 'admin@admin.com', 'password' => 'password']);
    
    // Simulate login
    \Illuminate\Support\Facades\Auth::login($authUser);
    
    // Get session data
    $sessionData = session()->all();
    echo "Session Data: \n";
    print_r($sessionData);
    
    // Simulate encryption
    $serialized = serialize($sessionData);
    echo "\nSerialized Session Size: " . strlen($serialized) . " bytes\n";
    
    $encrypted = \Illuminate\Support\Facades\Crypt::encrypt($serialized, false);
    echo "Encrypted Cookie Size: " . strlen($encrypted) . " bytes\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

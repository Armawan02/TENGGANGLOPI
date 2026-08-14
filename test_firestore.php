<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $firebase = app(\App\Services\FirebaseService::class);
    $user = $firebase->getDocument('users', 'admin_1');
    echo "USER_DOC:\n";
    print_r($user);
    
    $provider = new \App\Providers\FirestoreUserProvider();
    $authUser = $provider->retrieveByCredentials(['email' => 'admin@admin.com', 'password' => 'admin']);
    echo "\nAUTH_USER:\n";
    print_r($authUser);
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

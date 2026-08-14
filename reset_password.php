<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $firebase = app(\App\Services\FirebaseService::class);
    $userDoc = $firebase->getDocument('users', 'admin_1');
    
    // Hash password baru 'password'
    $userDoc['password'] = \Illuminate\Support\Facades\Hash::make('password');
    
    // Simpan kembali ke firestore
    $firebase->saveDocument('users', $userDoc, 'admin_1');
    
    echo "SUCCESS: Password admin_1 berhasil direset menjadi: password\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

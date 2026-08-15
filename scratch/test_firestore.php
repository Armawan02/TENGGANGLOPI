<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firebaseService = app(App\Services\FirebaseService::class);
$data = $firebaseService->getDocument('fleet', 'NODE_01');

echo json_encode($data, JSON_PRETTY_PRINT);

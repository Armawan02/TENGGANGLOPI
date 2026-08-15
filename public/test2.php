<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$fs = app()->make('App\Services\FirebaseService');
try {
    $fishermen = $fs->getCollection('fishermen');
    file_put_contents('fishermen_debug.json', json_encode($fishermen));
} catch (\Exception $e) {
    file_put_contents('fishermen_debug.json', 'Error: ' . $e->getMessage());
}

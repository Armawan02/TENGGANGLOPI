<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$c = app()->make('App\Http\Controllers\FirebaseApiController');
$res = $c->getFleetList();
echo json_encode($res->getData());

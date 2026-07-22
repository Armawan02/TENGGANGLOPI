<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TelemetryController;

Route::post('/telemetry', [TelemetryController::class, 'store']);

Route::get('/telemetry', function () {
    return response()->json(['message' => 'Telemetry API is running. Use POST to submit data.']);
});

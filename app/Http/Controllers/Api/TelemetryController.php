<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Node;
use App\Models\Telemetry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelemetryController extends Controller
{
    /**
     * Handle incoming telemetry data from LoRaWAN gateway.
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming payload
            $validated = $request->validate([
                'mac_address'       => 'required|string',
                'temperature'       => 'nullable|numeric',
                'humidity'          => 'nullable|numeric',
                'pressure'          => 'nullable|numeric',
                'roll'              => 'nullable|numeric',
                'pitch'             => 'nullable|numeric',
                'latitude'          => 'nullable|numeric',
                'longitude'         => 'nullable|numeric',
                'water_level'       => 'nullable|numeric',
                'weather_condition' => 'nullable|string',
            ]);

            // Find or create the node by mac_address
            $node = Node::firstOrCreate(
                ['mac_address' => $validated['mac_address']],
                ['name' => 'Unknown Node ' . substr($validated['mac_address'], -4)]
            );

            // Create Telemetry Record
            $telemetry = Telemetry::create([
                'node_id'           => $node->id,
                'temperature'       => $validated['temperature'] ?? null,
                'humidity'          => $validated['humidity'] ?? null,
                'pressure'          => $validated['pressure'] ?? null,
                'roll'              => $validated['roll'] ?? null,
                'pitch'             => $validated['pitch'] ?? null,
                'latitude'          => $validated['latitude'] ?? null,
                'longitude'         => $validated['longitude'] ?? null,
                'water_level'       => $validated['water_level'] ?? null,
                'weather_condition' => $validated['weather_condition'] ?? null,
            ]);

            // Simple Alert Logic for the Prototype
            $this->checkAnomalies($node, $telemetry);

            return response()->json([
                'status' => 'success',
                'message' => 'Telemetry data recorded successfully',
                'data' => $telemetry
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error saving telemetry: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to record telemetry data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check anomalies based on sensor values and trigger alerts.
     */
    private function checkAnomalies(Node $node, Telemetry $telemetry)
    {
        // 1. Capsizing Check (Roll/Pitch anomaly)
        if (abs($telemetry->roll) > 60 || abs($telemetry->pitch) > 60) {
            Alert::create([
                'node_id' => $node->id,
                'type' => 'Capsizing',
                'message' => 'Critical tilt detected! Potential capsizing. Roll: ' . $telemetry->roll . ', Pitch: ' . $telemetry->pitch,
            ]);
        }

        // 2. Leak Check (Water level rising in hull)
        // Assume HC-SR04 measures distance from top to water. If distance is very small, water is high.
        // Or if water_level > certain threshold. Let's assume water_level > 50 means leak.
        if ($telemetry->water_level > 50) {
            Alert::create([
                'node_id' => $node->id,
                'type' => 'Leak',
                'message' => 'High water level detected in hull (' . $telemetry->water_level . ' cm). Possible leak.',
            ]);
        }

        // 3. Severe Weather SOS Check
        if (strtolower($telemetry->weather_condition) === 'bad storm') {
            Alert::create([
                'node_id' => $node->id,
                'type' => 'Weather Warning',
                'message' => 'Edge-AI detected bad storm conditions.',
            ]);
        }
    }
}

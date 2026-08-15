<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelemetryController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle incoming telemetry data from LoRaWAN gateway and save to Firebase.
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

            // Untuk Vercel/Firebase, gunakan MAC Address yang disanitasi sebagai Document ID (misal NODE_01 atau C8_F0_9E_...)
            // Anda juga bisa memaksanya menjadi "NODE_01" jika perahu Anda hanya 1:
            // $docId = "NODE_01";
            $docId = str_replace(':', '_', $validated['mac_address']);

            // Mengambil dokumen yang ada dari fleet (atau buat array kosong jika belum ada)
            $nodeData = $this->firebaseService->getDocument('fleet', $docId);
            if (!$nodeData) {
                $nodeData = []; // Inisialisasi jika node baru
            }

            // Update struktur data sesuai format FirebaseTrackingController Anda (gyroscope, waterLevel, heartbeat)
            $nodeData['temperature'] = $validated['temperature'] ?? ($nodeData['temperature'] ?? 0);
            $nodeData['humidity'] = $validated['humidity'] ?? ($nodeData['humidity'] ?? 0);
            $nodeData['pressure'] = $validated['pressure'] ?? ($nodeData['pressure'] ?? 0);
            $nodeData['latitude'] = $validated['latitude'] ?? ($nodeData['latitude'] ?? 0);
            $nodeData['longitude'] = $validated['longitude'] ?? ($nodeData['longitude'] ?? 0);
            $nodeData['weather_condition'] = $validated['weather_condition'] ?? ($nodeData['weather_condition'] ?? 'unknown');
            
            // Menggabungkan Roll dan Pitch ke Gyroscope sesuai format aplikasi Anda
            $nodeData['gyroscope'] = [
                'x' => $validated['roll'] ?? 0,
                'y' => $validated['pitch'] ?? 0,
                'z' => 0
            ];
            
            // Format Water Level
            $nodeData['waterLevel'] = $validated['water_level'] ?? ($nodeData['waterLevel'] ?? 0);
            
            // Update Heartbeat (Timestamp UNIX untuk menandakan kapan terakhir online)
            $nodeData['heartbeat'] = time();
            $nodeData['updated_at'] = date('Y-m-d H:i:s');

            // Simpan kembali ke Firebase Firestore
            $this->firebaseService->saveDocument('fleet', $nodeData, $docId);

            // Cek Anomali
            $this->checkAnomalies($docId, $nodeData);

            return response()->json([
                'status' => 'success',
                'message' => 'Telemetry data recorded to Firebase successfully',
                'data' => $nodeData
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error saving telemetry to Firebase: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to record telemetry data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check anomalies based on sensor values and trigger alerts in Firebase.
     */
    private function checkAnomalies($docId, $nodeData)
    {
        $alertType = null;
        $alertMessage = null;

        $roll = $nodeData['gyroscope']['x'] ?? 0;
        $pitch = $nodeData['gyroscope']['y'] ?? 0;
        $waterLevel = $nodeData['waterLevel'] ?? 0;

        // 1. Capsizing Check (Roll/Pitch anomaly)
        if (abs($roll) > 60 || abs($pitch) > 60) {
            $alertType = 'Capsizing';
            $alertMessage = 'Critical tilt detected! Potential capsizing. Roll: ' . $roll . ', Pitch: ' . $pitch;
        }
        // 2. Leak Check (Water level rising in hull)
        elseif ($waterLevel > 50) {
            $alertType = 'Leak';
            $alertMessage = 'High water level detected in hull (' . $waterLevel . ' cm). Possible leak.';
        }
        // 3. Severe Weather Check
        elseif (strtolower($nodeData['weather_condition'] ?? '') === 'bad storm') {
            $alertType = 'Weather Warning';
            $alertMessage = 'Edge-AI detected bad storm conditions.';
        }

        if ($alertType) {
            // Log peringatan ke Firebase koleksi history_logs
            $logData = [
                'node_id' => $docId,
                'type' => $alertType,
                'message' => $alertMessage,
                'is_resolved' => false,
                'timestamp' => time()
            ];
            
            // Simpan tanpa ID (parameter ke-3 dikosongkan) agar Firebase membuatkan UUID
            $this->firebaseService->saveDocument('history_logs', $logData);
            
            // Nyalakan alarm / buzzer di perahu melalui data fleet (agar dibaca oleh sistem Anda)
            $nodeData['buzzerSignal'] = 'ON';
            $this->firebaseService->saveDocument('fleet', $nodeData, $docId);
        }
    }
}

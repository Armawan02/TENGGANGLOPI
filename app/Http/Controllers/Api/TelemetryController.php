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
            // Karena data dari ESP Perahu menggunakan format bahasa Indonesia:
            // {"Suhu":29.18, "Kelembapan":75.74, "Tekanan":1010.65, "Kemiringan":40, "JarakAir":379.7, "Lat":-3.539471, "Lng":118.989296}
            
            // Parse ID Node dari ESP32 (Jika ada), kalau tidak ada anggap NODE_01
            $rawId = $request->input('NodeID', $request->input('ID', $request->input('id', 'NODE_01')));
            // Ganti spasi/strip jadi underscore dan ubah ke huruf besar agar rapi (misal "Node-2" jadi "NODE_02")
            $docId = strtoupper(str_replace('-', '_', $rawId));
            
            // Khusus jika formatnya NODE_2, ubah jadi NODE_02 agar selaras dengan NODE_01
            if (preg_match('/^NODE_(\d)$/', $docId, $matches)) {
                $docId = 'NODE_0' . $matches[1];
            }

            // Mengambil dokumen yang ada dari fleet (atau buat array kosong jika belum ada)
            $nodeData = $this->firebaseService->getDocument('fleet', $docId);
            if (!$nodeData) {
                $nodeData = []; // Inisialisasi jika node baru
                // Simpan ID agar bisa dibaca oleh Dashboard
                $nodeData['id'] = $docId;
            }

            // Map data JSON berbahasa Indonesia ke variabel Firebase sesuai struktur bersarang (Nested)
            
            // 1. Sensor BME280 (Suhu, Kelembapan, Tekanan)
            $nodeData['bme280'] = [
                'temperature' => (float) $request->input('Suhu', $nodeData['bme280']['temperature'] ?? 0),
                'humidity'    => (float) $request->input('Kelembapan', $nodeData['bme280']['humidity'] ?? 0),
                'pressure'    => (float) $request->input('Tekanan', $nodeData['bme280']['pressure'] ?? 0)
            ];
            
            // 2. GPS / Koordinat
            $nodeData['coordinates'] = [
                'lat' => (float) $request->input('Lat', $nodeData['coordinates']['lat'] ?? 0),
                'lng' => (float) $request->input('Lng', $nodeData['coordinates']['lng'] ?? 0)
            ];
            
            // 3. Menggabungkan "Kemiringan" ke Gyroscope sesuai format aplikasi Anda
            $kemiringan = (float) $request->input('Kemiringan', 0);
            $nodeData['gyroscope'] = [
                'x' => $kemiringan,
                'y' => $kemiringan,
                'z' => 0
            ];
            
            // 4. Format Water Level
            $nodeData['waterLevel'] = (float) $request->input('JarakAir', $nodeData['waterLevel'] ?? 0);
            
            // 5. LoRa RSSI
            $nodeData['rssi'] = (int) $request->input('Rssi', $nodeData['rssi'] ?? -85);
            
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
            $alertType = 'Automated SOS';
            $alertMessage = 'Kapal Terbalik! Kemiringan kritis terdeteksi (Roll: ' . $roll . ', Pitch: ' . $pitch . ').';
        }
        // 2. Leak Check (Semakin rendah jarak air ke sensor, berarti air semakin naik mengisi lambung)
        elseif ($waterLevel > 0 && $waterLevel < 20) {
            $alertType = 'Automated SOS';
            $alertMessage = 'Bahaya Kebocoran! Genangan air sangat tinggi (Jarak sensor sisa ' . $waterLevel . ' cm).';
        }
        // 3. Severe Weather Check
        elseif (strtolower($nodeData['weather_condition'] ?? '') === 'bad storm') {
            $alertType = 'Weather Warning';
            $alertMessage = 'Edge-AI mendeteksi anomali cuaca badai di sekitar kapal.';
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

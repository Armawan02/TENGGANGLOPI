<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class FirebaseApiController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Mengambil semua node/kapal untuk list di Dashboard
     */
    public function getFleetList()
    {
        try {
            // Ambil dari collection fishermen, filter yang memiliki node_id
            $fishermen = $this->firebaseService->getCollection('fishermen');
            // Ambil data real-time sensor dari collection fleet
            $fleetData = $this->firebaseService->getCollection('fleet');
            
            // Buat map (dictionary) untuk akses data fleet lebih cepat berdasarkan ID
            $fleetMap = [];
            foreach ($fleetData as $fd) {
                if (isset($fd['id'])) {
                    $fleetMap[$fd['id']] = $fd;
                }
            }
            
            $activeFleet = [];
            foreach ($fishermen as $f) {
                if (!empty($f['node_id'])) {
                    $nodeId = $f['node_id'];
                    $sensor = $fleetMap[$nodeId] ?? [];
                    
                    $activeFleet[] = [
                        'id' => $nodeId, // Dashboard JS menggunakan 'id'
                        'vesselName' => !empty($f['boat_name']) ? $f['boat_name'] : 'Kapal Tanpa Nama',
                        'fishermanName' => !empty($f['name']) ? $f['name'] : 'Tidak Diketahui',
                        'buzzerSignal' => $sensor['buzzerSignal'] ?? 'OFF',
                        // Memasukkan data sensor real-time
                        'waterLevel' => $sensor['waterLevel'] ?? null,
                        'heartbeat' => $sensor['heartbeat'] ?? null,
                        'gyroscope' => $sensor['gyroscope'] ?? null,
                        'bme280' => $sensor['bme280'] ?? null,
                        'coordinates' => $sensor['coordinates'] ?? null,
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $activeFleet
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil riwayat log kejadian terbaru
     */
    public function getRecentLogs()
    {
        try {
            // Karena tidak bisa orderBy kompleks tanpa index di Firestore jika query rumit,
            // kita ambil semua log (limitasi dilakukan di frontend atau backend array slice)
            $logs = $this->firebaseService->getCollection('history_logs');
            
            // Sort by time descending (asumsi ada field timestamp)
            usort($logs, function($a, $b) {
                $timeA = $a['timestamp'] ?? 0;
                $timeB = $b['timestamp'] ?? 0;
                return $timeB <=> $timeA;
            });
            
            return response()->json([
                'status' => 'success',
                'data' => array_slice($logs, 0, 10) // Ambil 10 terbaru
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

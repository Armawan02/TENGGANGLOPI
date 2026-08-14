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
            
            $activeFleet = [];
            foreach ($fishermen as $f) {
                if (!empty($f['node_id'])) {
                    $activeFleet[] = [
                        'id' => $f['node_id'], // Dashboard JS menggunakan 'id' sebagai referensi telemetry (e.g. node-01)
                        'vesselName' => !empty($f['boat_name']) ? $f['boat_name'] : 'Kapal Tanpa Nama',
                        'fishermanName' => !empty($f['name']) ? $f['name'] : 'Tidak Diketahui',
                        'buzzerSignal' => $f['buzzerSignal'] ?? 'OFF', // Default
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

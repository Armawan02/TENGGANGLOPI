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
            
            // Buat map (dictionary) untuk akses data fishermen berdasarkan node_id
            $fishermenMap = [];
            foreach ($fishermen as $f) {
                if (!empty($f['node_id'])) {
                    $fishermenMap[$f['node_id']] = $f;
                }
            }
            
            $activeFleet = [];
            foreach ($fleetData as $fd) {
                if (isset($fd['id'])) {
                    $nodeId = $fd['id'];
                    $fish = $fishermenMap[$nodeId] ?? [];
                    
                    $activeFleet[] = [
                        'id' => $nodeId, // Dashboard JS menggunakan 'id'
                        'vesselName' => !empty($fish['boat_name']) ? $fish['boat_name'] : 'Kapal Tanpa Nama',
                        'fishermanName' => !empty($fish['name']) ? $fish['name'] : 'Tidak Diketahui',
                        'buzzerSignal' => $fd['buzzerSignal'] ?? 'OFF',
                        // Memasukkan data sensor real-time
                        'waterLevel' => $fd['waterLevel'] ?? null,
                        'heartbeat' => $fd['heartbeat'] ?? null,
                        'rssi' => $fd['rssi'] ?? null,
                        'gyroscope' => $fd['gyroscope'] ?? null,
                        'bme280' => $fd['bme280'] ?? null,
                        'coordinates' => $fd['coordinates'] ?? null,
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $activeFleet
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getFleetList Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil data node/kapal untuk publik (tanpa data sensitif)
     */
    public function getPublicFleetList()
    {
        try {
            $fishermen = $this->firebaseService->getCollection('fishermen');
            $fleetData = $this->firebaseService->getCollection('fleet');
            
            $fishermenMap = [];
            foreach ($fishermen as $f) {
                if (!empty($f['node_id'])) {
                    $fishermenMap[$f['node_id']] = $f;
                }
            }
            
            $publicFleet = [];
            foreach ($fleetData as $fd) {
                if (isset($fd['id'])) {
                    $nodeId = $fd['id'];
                    $fish = $fishermenMap[$nodeId] ?? [];
                    
                    $publicFleet[] = [
                        'id' => $nodeId,
                        'vesselName' => !empty($fish['boat_name']) ? $fish['boat_name'] : 'Kapal Tanpa Nama',
                        'heartbeat' => $fd['heartbeat'] ?? null,
                        'coordinates' => $fd['coordinates'] ?? null,
                        // Data sensitif seperti nama nelayan, riwayat SOS disembunyikan
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $publicFleet
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getPublicFleetList Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Mengambil riwayat log kejadian terbaru
     */
    public function getRecentLogs(\Illuminate\Http\Request $request)
    {
        try {
            $logs = $this->firebaseService->getCollection('history_logs');
            
            $dateFilter = $request->query('date');
            $statusFilter = $request->query('status');
            
            // Sort by time descending
            usort($logs, function($a, $b) {
                $timeA = $a['timestamp'] ?? 0;
                $timeB = $b['timestamp'] ?? 0;
                return $timeB <=> $timeA;
            });
            
            // Filtering
            if ($dateFilter || ($statusFilter && $statusFilter !== 'Semua Status')) {
                $logs = array_filter($logs, function($log) use ($dateFilter, $statusFilter) {
                    $passDate = true;
                    $passStatus = true;
                    
                    if ($dateFilter) {
                        $logDate = isset($log['timestamp']) ? date('Y-m-d', $log['timestamp']) : '';
                        $passDate = ($logDate === $dateFilter);
                    }
                    
                    if ($statusFilter && $statusFilter !== 'Semua Status') {
                        $logType = $log['type'] ?? 'INFO';
                        
                        $mappedLevel = 'INFO';
                        if ($logType === 'Capsizing') {
                            $mappedLevel = 'CRITICAL';
                        } elseif (in_array($logType, ['Leak', 'Weather Warning'])) {
                            $mappedLevel = 'WARNING';
                        }
                        
                        $passStatus = ($mappedLevel === $statusFilter);
                    }
                    
                    return $passDate && $passStatus;
                });
                
                // reset array keys
                $logs = array_values($logs);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => array_slice($logs, 0, 50) // Tampilkan hingga 50
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

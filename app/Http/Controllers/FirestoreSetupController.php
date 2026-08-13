<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\FirebaseService;
use Illuminate\Support\Str;

class FirestoreSetupController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function setupDatabase()
    {
        try {
            // 1. Setup Data USERS
            $users = [
                'admin_1' => [
                    'name' => 'Administrator',
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('password'),
                    'role' => 'superadmin',
                    'created_at' => time()
                ],
                'petugas_1' => [
                    'name' => 'Petugas Jaga',
                    'email' => 'petugas@tengganglopi.com',
                    'password' => Hash::make('password'),
                    'role' => 'petugas',
                    'created_at' => time()
                ]
            ];
            foreach ($users as $id => $data) {
                $this->firebaseService->saveDocument('users', $data, $id);
            }

            // 2. Setup Data FLEET / NODES
            $fleet = [
                'NODE_01' => [
                    'name' => 'Salili Mandar',
                    'waterLevel' => 0,
                    'heartbeat' => 50,
                    'buzzerSignal' => 'OFF',
                    'gyroscope' => ['x' => 0, 'y' => 0, 'z' => 0],
                    'status' => 'Online',
                    'last_seen' => time()
                ],
                'NODE_02' => [
                    'name' => 'Bintang Laut',
                    'waterLevel' => 15,
                    'heartbeat' => 45,
                    'buzzerSignal' => 'OFF',
                    'gyroscope' => ['x' => 10, 'y' => -5, 'z' => 2],
                    'status' => 'Online',
                    'last_seen' => time()
                ],
                'NODE_03' => [
                    'name' => 'Harapan Jaya',
                    'waterLevel' => 40,
                    'heartbeat' => 80,
                    'buzzerSignal' => 'PING',
                    'gyroscope' => ['x' => 5, 'y' => 5, 'z' => 0],
                    'status' => 'Online',
                    'last_seen' => time()
                ]
            ];
            foreach ($fleet as $id => $data) {
                $this->firebaseService->saveDocument('fleet', $data, $id);
            }

            // 3. Setup Data ALERTS / HISTORY LOGS
            $alerts = [
                [
                    'node_id' => 'NODE_03',
                    'type' => 'Leak',
                    'message' => 'Sensor ultrasonik mendeteksi debit air 40cm di lambung.',
                    'latitude' => -3.582,
                    'longitude' => 118.990,
                    'is_resolved' => false,
                    'time' => 'Hari ini, 15:30 WITA',
                    'level' => 'Warning',
                    'timestamp' => time() - 3600
                ],
                [
                    'node_id' => 'NODE_01',
                    'type' => 'Capsizing',
                    'message' => 'Kemiringan kapal sempat mencapai 65 derajat, namun telah kembali normal.',
                    'latitude' => -3.600,
                    'longitude' => 118.950,
                    'is_resolved' => true,
                    'time' => 'Kemarin, 12:15 WITA',
                    'level' => 'Recovered',
                    'timestamp' => time() - 86400
                ]
            ];
            foreach ($alerts as $data) {
                $this->firebaseService->saveDocument('history_logs', $data);
            }

            // 4. Setup Data CONFIGS
            $configs = [
                'system_settings' => [
                    'water_danger_threshold_cm' => 30,
                    'tilt_danger_threshold_degree' => 45,
                    'ping_interval_seconds' => 10,
                    'is_lora_active' => true
                ]
            ];
            foreach ($configs as $id => $data) {
                $this->firebaseService->saveDocument('configs', $data, $id);
            }
            
            // 5. Setup Data LORA GATEWAYS
            $gateways = [
                'GW_MAJENE_01' => [
                    'location' => 'Stasiun Majene',
                    'status' => 'Active',
                    'frequency' => '915 MHz',
                    'last_uplink' => time()
                ]
            ];
            foreach ($gateways as $id => $data) {
                $this->firebaseService->saveDocument('lora_gateways', $data, $id);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Semua struktur Collection dan Data Sample Firestore berhasil dibuat menggunakan Custom REST Client!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat setup: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class FirebaseTrackingController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Mengambil data NODE_01 dari Firestore
     */
    public function getNodeData()
    {
        try {
            // Mengambil dokumen NODE_01 dari collection fleet
            $data = $this->firebaseService->getDocument('fleet', 'NODE_01');

            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'buzzerSignal' => $data['buzzerSignal'] ?? null,
                        'gyroscope' => $data['gyroscope'] ?? ['x' => 0, 'y' => 0, 'z' => 0],
                        'heartbeat' => $data['heartbeat'] ?? 0,
                        'waterLevel' => $data['waterLevel'] ?? 0,
                    ]
                ]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Node tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Mengubah status buzzer di Firestore
     */
    public function triggerBuzzer(Request $request)
    {
        try {
            $data = $this->firebaseService->getDocument('fleet', 'NODE_01') ?? [];
            $data['buzzerSignal'] = $request->input('signal', 'ON');
            
            $this->firebaseService->saveDocument('fleet', $data, 'NODE_01');
            
            return response()->json(['status' => 'success', 'message' => 'Buzzer signal updated!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

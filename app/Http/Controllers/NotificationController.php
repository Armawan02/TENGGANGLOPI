<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function getPendingUsers()
    {
        // Hanya superadmin yang butuh notifikasi ini
        if (!Auth::check() || Auth::user()->role !== 'superadmin') {
            return response()->json(['status' => 'success', 'count' => 0, 'data' => []]);
        }

        try {
            // Ambil semua user dari Firestore dan filter manual
            $users = $this->firebaseService->getCollection('users');
            $pendingUsers = [];
            
            foreach ($users as $user) {
                if (isset($user['status']) && $user['status'] === 'pending') {
                    $pendingUsers[] = [
                        'id' => $user['id'] ?? '',
                        'name' => $user['name'] ?? 'User Baru',
                        'email' => $user['email'] ?? '',
                        'created_at' => $user['created_at'] ?? ''
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success', 
                'count' => count($pendingUsers),
                'data' => $pendingUsers
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getPendingUsers Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getActiveAlerts()
    {
        try {
            $logs = $this->firebaseService->getCollection('history_logs');
            $activeAlerts = [];
            
            $oneDayAgo = time() - (24 * 3600);
            foreach ($logs as $log) {
                if (isset($log['type']) && $log['type'] === 'Automated SOS' && isset($log['timestamp']) && $log['timestamp'] > $oneDayAgo) {
                    $isResolved = $log['is_resolved'] ?? false;
                    if (!$isResolved) {
                        $activeAlerts[] = $log;
                    }
                }
            }
            
            return response()->json([
                'status' => 'success',
                'count' => count($activeAlerts),
                'data' => $activeAlerts
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getActiveAlerts Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

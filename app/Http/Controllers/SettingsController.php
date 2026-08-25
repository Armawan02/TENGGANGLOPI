<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class SettingsController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        // Ambil data pengaturan sensor dari Firebase jika ada, jika tidak pakai default
        try {
            $sensorSettings = $this->firebaseService->getDocument('settings', 'sensor_thresholds');
        } catch (\Exception $e) {
            $sensorSettings = null;
        }

        $defaultSettings = [
            'water_warning' => 50,
            'water_danger' => 15,
            'tilt_warning' => 60,
            'tilt_danger' => 90,
        ];

        $settings = $sensorSettings ? array_merge($defaultSettings, $sensorSettings) : $defaultSettings;

        return view('superadmin.settings', compact('settings'));
    }

    public function updateSensor(Request $request)
    {
        $request->validate([
            'water_warning' => 'required|numeric',
            'water_danger' => 'required|numeric',
            'tilt_warning' => 'required|numeric',
            'tilt_danger' => 'required|numeric',
        ]);

        try {
            $data = [
                'water_warning' => (int) $request->water_warning,
                'water_danger' => (int) $request->water_danger,
                'tilt_warning' => (int) $request->tilt_warning,
                'tilt_danger' => (int) $request->tilt_danger,
                'updated_at' => time()
            ];

            // Simpan ke Firestore
            $this->firebaseService->saveDocument('settings', $data, 'sensor_thresholds');

            return redirect()->back()->with('success', 'Pengaturan sensor berhasil disimpan ke Firebase.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    public function updateNotifications(Request $request)
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            
            $user->notify_buzzer = $request->has('notify_buzzer');
            $user->notify_bmkg = $request->has('notify_bmkg');
            
            $user->save();

            return redirect()->back()->with('success', 'Preferensi notifikasi berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan preferensi notifikasi: ' . $e->getMessage());
        }
    }
}

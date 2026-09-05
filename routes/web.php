<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FirestoreSetupController;
use Illuminate\Support\Facades\Route;

Route::get('/setup-db', [FirestoreSetupController::class, 'setupDatabase']);

Route::get('/', function () {
    return view('guest.index');
});

Route::get('/demo-login', function (\App\Services\FirebaseService $firebase) {
    $users = $firebase->getCollection('users');
    
    $foundUser = null;
    $emails = [];
    foreach ($users as $u) {
        $email = $u['email'] ?? '';
        $emails[] = $email;
        if (str_contains(strtolower($email), 'armawan')) {
            $foundUser = $u;
            break;
        }
    }
    
    if ($foundUser) {
        $userModel = new \App\Models\User($foundUser, $foundUser['id'] ?? null);
        \Illuminate\Support\Facades\Auth::login($userModel);
        if (($foundUser['role'] ?? '') === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }
        return redirect()->route('petugas.dashboard');
    }
    
    return 'Gagal Login: Akun armawan tidak ditemukan. Daftar email di database: ' . implode(', ', $emails);
})->name('demo.login');

Route::get('/clear-dummy', function(\App\Services\FirebaseService $firebase) {
    // Hapus semua riwayat log
    foreach($firebase->getCollection('history_logs') as $log) {
        if(isset($log['id'])) $firebase->deleteDocument('history_logs', $log['id']);
    }
    // Reset data sensor kapal menjadi 0 (jangan hapus kapalnya agar tetap tampil Offline)
    foreach($firebase->getCollection('fleet') as $f) {
        if(isset($f['id'])) {
            $f['waterLevel'] = 0;
            $f['buzzerSignal'] = 'OFF';
            $f['gyroscope'] = ['x' => 0, 'y' => 0, 'z' => 0];
            $f['heartbeat'] = 0; // Pastikan status offline
            $firebase->saveDocument('fleet', $f, $f['id']);
        }
    }
    return "Dummy Data Cleared & Reset!";
});

Route::get('/setup-database', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true]);
    return "Database berhasil di-reset dan akun bawaan berhasil dibuat!";
});

Route::get('/troll-bsod', function () {
    return view('troll.bsod');
})->name('troll.bsod');

Route::get('/antrian', function () {
    return view('auth.antrian');
})->name('antrian');

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('superadmin.dashboard');

    Route::get('/tracking', function () {
        return view('superadmin.tracking');
    })->name('superadmin.tracking');

    Route::get('/history', function () {
        return view('superadmin.history');
    })->name('superadmin.history');

    // System Health & Automation
    Route::get('/system-overview', function () {
        return view('superadmin.system_overview');
    })->name('superadmin.system_overview');
    
    Route::get('/risk-map', function () {
        return view('superadmin.risk_map');
    })->name('superadmin.risk_map');
    
    Route::get('/healing-activity', function () {
        return view('superadmin.healing_activity');
    })->name('superadmin.healing_activity');
    
    Route::get('/evolution-engine', function () {
        return view('superadmin.evolution_engine');
    })->name('superadmin.evolution_engine');

    Route::get('/integrations/lorawan', function () {
        return view('superadmin.integrations.index');
    })->name('superadmin.integrations.lora');

    Route::middleware(['auth'])->prefix('administration')->name('superadmin.administration.')->group(function () {
        // CRUD Kelola Akun (Users)
        Route::get('/accounts', [\App\Http\Controllers\AccountController::class, 'index'])->name('accounts');
        Route::post('/accounts', [\App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');
        Route::put('/accounts/{id}', [\App\Http\Controllers\AccountController::class, 'update'])->name('accounts.update');
        Route::post('/accounts/{id}/reset-password', [\App\Http\Controllers\AccountController::class, 'resetPassword'])->name('accounts.reset-password');
        Route::post('/accounts/{id}/approve', [\App\Http\Controllers\AccountController::class, 'approve'])->name('accounts.approve');
        Route::delete('/accounts/{id}', [\App\Http\Controllers\AccountController::class, 'destroy'])->name('accounts.destroy');
        
        // CRUD Data Nelayan
        Route::get('/fishermen', [\App\Http\Controllers\FishermanController::class, 'index'])->name('fishermen');
        Route::post('/fishermen', [\App\Http\Controllers\FishermanController::class, 'store'])->name('fishermen.store');
        Route::put('/fishermen/{id}', [\App\Http\Controllers\FishermanController::class, 'update'])->name('fishermen.update');
        Route::delete('/fishermen/{id}', [\App\Http\Controllers\FishermanController::class, 'destroy'])->name('fishermen.destroy');
    });

    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('superadmin.settings');
    Route::post('/settings/sensor', [\App\Http\Controllers\SettingsController::class, 'updateSensor'])->name('superadmin.settings.sensor');
    Route::post('/settings/notifications', [\App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('superadmin.settings.notifications');
});

Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->group(function () {
    Route::get('/dashboard', function () {
        return view('petugas.dashboard');
    })->name('petugas.dashboard');

    Route::get('/tracking', function () {
        return view('petugas.tracking');
    })->name('petugas.tracking');

    Route::get('/history', function () {
        return view('petugas.history');
    })->name('petugas.history');


    Route::get('/settings', function () {
        return view('petugas.settings');
    })->name('petugas.settings');
    Route::post('/settings/notifications', [\App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('petugas.settings.notifications');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/proxy/bmkg', function (Illuminate\Http\Request $request) {
    try {
        $adm2 = $request->query('adm2', '76.05'); // Default to Majene
        
        // Pemetaan Kabupaten (adm2) ke Kelurahan ibukota/referensi (adm4) 
        // karena API BMKG terbaru secara ketat membutuhkan parameter adm4
        $adm2_to_adm4 = [
            '73.71' => '73.71.01.1001', // Makassar (Mariso)
            '73.08' => '73.08.14.1001', // Maros (Cinnong)
            '73.01' => '73.01.01.1001', // Selayar (Benteng Utara)
            '73.06' => '73.06.08.1001', // Gowa (Sungguminasa)
            '73.22' => '73.22.03.1001', // Luwu Utara (Kappuna)
            '76.05' => '76.05.01.1001', // Majene (Banggae)
            '76.02' => '76.02.01.1002', // Mamuju (Binanga)
        ];
        
        $adm4 = $adm2_to_adm4[$adm2] ?? '76.05.01.1001';
        $url = 'https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=' . $adm4;
        
        // Nonaktifkan verifikasi SSL untuk mencegah error cURL 60 di Laragon/lokal
        // Tambahkan header User-Agent untuk bypass Cloudflare 403 Forbidden
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                'Referer' => 'https://api.bmkg.go.id/'
            ])
            ->timeout(15)
            ->get($url);
        
        if ($response->successful()) {
            return response($response->body(), 200, [
                'Content-Type' => 'application/json',
                'Access-Control-Allow-Origin' => '*'
            ]);
        }
        
        return response()->json(['error' => 'Gagal mengambil data dari BMKG: HTTP ' . $response->status()], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error Proxy: ' . $e->getMessage()], 500);
    }
});

Route::get('/api/fleet/public', [\App\Http\Controllers\FirebaseApiController::class, 'getPublicFleetList'])->name('api.fleet.public');

require __DIR__.'/auth.php';

// Route API Firebase Tracking (Untuk Dashboard)
Route::middleware(['auth'])->prefix('api/fleet')->group(function () {
    Route::get('/node-01', [\App\Http\Controllers\FirebaseTrackingController::class, 'getNodeData'])->name('api.fleet.node01');
    Route::post('/buzzer', [\App\Http\Controllers\FirebaseTrackingController::class, 'triggerBuzzer'])->name('api.fleet.buzzer');
    
    // New APIs
    Route::get('/all-nodes', [\App\Http\Controllers\FirebaseApiController::class, 'getFleetList'])->name('api.fleet.all');
});

Route::middleware(['auth'])->prefix('api/history')->group(function () {
    Route::get('/logs', [\App\Http\Controllers\FirebaseApiController::class, 'getRecentLogs'])->name('api.history.logs');
});

Route::middleware(['auth'])->prefix('api/notifications')->group(function () {
    Route::get('/pending-users', [\App\Http\Controllers\NotificationController::class, 'getPendingUsers'])->name('api.notifications.pending');
    Route::get('/alerts', [\App\Http\Controllers\NotificationController::class, 'getActiveAlerts'])->name('api.notifications.alerts');
});

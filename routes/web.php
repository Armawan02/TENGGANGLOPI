<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('guest.index');
});

Route::get('/setup-database', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true]);
    return "Database berhasil di-reset dan akun bawaan berhasil dibuat!";
});

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

    Route::get('/administration/users', function () {
        return view('superadmin.administration.index');
    })->name('superadmin.administration.users');

    Route::get('/administration/accounts', function () {
        return view('superadmin.administration.accounts');
    })->name('superadmin.administration.accounts');
    
    Route::get('/settings', function () {
        return view('superadmin.settings');
    })->name('superadmin.settings');
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

    Route::get('/system-overview', function () {
        return view('petugas.system_overview');
    })->name('petugas.system_overview');
    
    Route::get('/risk-map', function () {
        return view('petugas.risk_map');
    })->name('petugas.risk_map');
    
    Route::get('/healing-activity', function () {
        return view('petugas.healing_activity');
    })->name('petugas.healing_activity');
    
    Route::get('/evolution-engine', function () {
        return view('petugas.evolution_engine');
    })->name('petugas.evolution_engine');
    
    Route::get('/settings', function () {
        return view('petugas.settings');
    })->name('petugas.settings');
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

require __DIR__.'/auth.php';

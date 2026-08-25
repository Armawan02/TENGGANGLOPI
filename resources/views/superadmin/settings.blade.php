@extends('layouts.superadmin')
@section('title', 'Settings & Preferences')
@section('subtitle', 'Manage your display themes and notifications')

@section('content')
<style>
    .settings-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }
    .settings-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 25px;
    }
    .settings-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .theme-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    .theme-card {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    .theme-card:hover {
        border-color: var(--accent);
    }
    .theme-card.active {
        border-color: var(--accent);
        background: rgba(37,99,235,0.05);
    }
    .theme-icon {
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex; justify-content: center; align-items: center;
    }
    .theme-cerah .theme-icon { background: #f8fafc; color: #f59e0b; border: 1px solid #e2e8f0; }
    .theme-gelap .theme-icon { background: #1a1c23; color: #ffffff; border: 1px solid #2d303b; }
    .theme-biru .theme-icon { background: #1e293b; color: #0ea5e9; border: 1px solid #334155; }
    
    /* Toggle Switch */
    .setting-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .setting-item:last-child { border-bottom: none; }
    .setting-info h4 { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
    .setting-info p { font-size: 13px; color: var(--text-muted); }
    
    .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--border-color); transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--accent); }
    input:checked + .slider:before { transform: translateX(20px); }
</style>

<div class="settings-container">
    <!-- Theme Settings -->
    <div class="settings-card">
        <div class="settings-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--accent)"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg>
            Tampilan (Theme)
        </div>
        
        <div class="theme-grid">
            <div class="theme-card theme-cerah" onclick="setTheme('cerah')" id="btn-cerah">
                <div class="theme-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg>
                </div>
                <div style="font-weight:600; font-size:14px;">Cerah</div>
            </div>
            <div class="theme-card theme-gelap" onclick="setTheme('gelap')" id="btn-gelap">
                <div class="theme-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </div>
                <div style="font-weight:600; font-size:14px;">Gelap</div>
            </div>
            <div class="theme-card theme-biru" onclick="setTheme('biru')" id="btn-biru">
                <div class="theme-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12h4l2-9 4 18 2-9h6"></path></svg>
                </div>
                <div style="font-weight:600; font-size:14px;">Biru Laut</div>
            </div>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="settings-card">
        <div class="settings-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--accent)"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            Preferensi Notifikasi
        </div>
        
        <form action="{{ route('superadmin.settings.notifications') }}" method="POST">
            @csrf
            <div class="setting-item">
                <div class="setting-info">
                    <h4>Notifikasi Darurat Suara (Buzzer)</h4>
                    <p>Mainkan suara sirene saat ada SOS dari nelayan.</p>
                </div>
                <label class="switch">
                    <input type="checkbox" name="notify_buzzer" {{ (Auth::user()->notify_buzzer ?? true) ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="setting-item">
                <div class="setting-info">
                    <h4>Peringatan Cuaca BMKG</h4>
                    <p>Tampilkan toast peringatan dini jika cuaca buruk terdeteksi di koordinat stasiun.</p>
                </div>
                <label class="switch">
                    <input type="checkbox" name="notify_bmkg" {{ (Auth::user()->notify_bmkg ?? true) ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
            <div style="text-align: right; margin-top: 15px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                <button type="submit" style="background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;">
                    Simpan Preferensi
                </button>
            </div>
        </form>
    </div>

    <!-- Sensor Settings -->
    <div class="settings-card" style="grid-column: 1 / -1;">
        <div class="settings-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--accent)"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            Pengaturan Ambang Batas Sensor (Edge-AI)
        </div>
        
        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; font-weight: 600;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('superadmin.settings.sensor') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Kebocoran -->
                <div>
                    <h4 style="font-size: 14px; margin-bottom: 10px; color: var(--text-primary);">Deteksi Kebocoran Air (Ultrasonik)</h4>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 5px;">Batas Bocor/Waspada (cm)</label>
                        <input type="number" name="water_warning" value="{{ $settings['water_warning'] ?? 50 }}" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-primary); outline: none;">
                        <small style="color: var(--text-muted); font-size: 11px;">Jarak air < nilai ini akan berstatus BOCOR.</small>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 5px;">Batas Tenggelam/Bahaya (cm)</label>
                        <input type="number" name="water_danger" value="{{ $settings['water_danger'] ?? 15 }}" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-primary); outline: none;">
                        <small style="color: var(--text-muted); font-size: 11px;">Jarak air < nilai ini akan berstatus TENGGELAM.</small>
                    </div>
                </div>

                <!-- Kemiringan -->
                <div>
                    <h4 style="font-size: 14px; margin-bottom: 10px; color: var(--text-primary);">Stabilitas Kapal (MPU6050)</h4>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 5px;">Batas Waspada Kemiringan (°)</label>
                        <input type="number" name="tilt_warning" value="{{ $settings['tilt_warning'] ?? 60 }}" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-primary); outline: none;">
                        <small style="color: var(--text-muted); font-size: 11px;">Sudut ≥ nilai ini akan berstatus WASPADA.</small>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 5px;">Batas Terbalik/Bahaya (°)</label>
                        <input type="number" name="tilt_danger" value="{{ $settings['tilt_danger'] ?? 90 }}" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-primary); outline: none;">
                        <small style="color: var(--text-muted); font-size: 11px;">Sudut ≥ nilai ini akan berstatus TERBALIK (SOS Otomatis).</small>
                    </div>
                </div>
            </div>
            
            <div style="text-align: right; margin-top: 15px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                <button type="submit" style="background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;">
                    Simpan Pengaturan Sensor
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function setTheme(themeName) {
        localStorage.setItem('tengganglopi_theme', themeName);
        
        // Remove active class from all
        document.getElementById('btn-cerah').classList.remove('active');
        document.getElementById('btn-gelap').classList.remove('active');
        document.getElementById('btn-biru').classList.remove('active');
        
        // Add active class
        document.getElementById('btn-' + themeName).classList.add('active');
        
        // Apply theme variables immediately
        const root = document.documentElement;
        if(themeName === 'gelap') {
            root.style.setProperty('--bg-main', '#13151a');
            root.style.setProperty('--bg-sidebar', '#1a1c23');
            root.style.setProperty('--bg-card', '#1a1c23');
            root.style.setProperty('--border-color', '#2d303b');
            root.style.setProperty('--text-primary', '#ffffff');
            root.style.setProperty('--text-muted', '#8b949e');
            
            root.style.setProperty('--sensor-bg', '#1e2029');
            root.style.setProperty('--sensor-border', '#2d303b');
            root.style.setProperty('--sensor-title', '#9ca3af');
            root.style.setProperty('--sensor-val', '#f8fafc');
            root.style.setProperty('--sensor-status-bg', '#13151a');
            root.style.setProperty('--sensor-status-border', '#2d303b');
            root.style.setProperty('--sensor-status-text', '#9ca3af');
        } else if(themeName === 'biru') {
            root.style.setProperty('--bg-main', '#0f172a');
            root.style.setProperty('--bg-sidebar', '#1e293b');
            root.style.setProperty('--bg-card', '#1e293b');
            root.style.setProperty('--border-color', '#334155');
            root.style.setProperty('--text-primary', '#f8fafc');
            root.style.setProperty('--text-muted', '#94a3b8');
            root.style.setProperty('--accent', '#0ea5e9');
            root.style.setProperty('--accent-hover', '#0284c7');
            
            root.style.setProperty('--sensor-bg', '#1e293b');
            root.style.setProperty('--sensor-border', '#334155');
            root.style.setProperty('--sensor-title', '#94a3b8');
            root.style.setProperty('--sensor-val', '#f8fafc');
            root.style.setProperty('--sensor-status-bg', '#0f172a');
            root.style.setProperty('--sensor-status-border', '#334155');
            root.style.setProperty('--sensor-status-text', '#cbd5e1');
        } else {
            // Cerah
            root.style.removeProperty('--bg-main');
            root.style.removeProperty('--bg-sidebar');
            root.style.removeProperty('--bg-card');
            root.style.removeProperty('--border-color');
            root.style.removeProperty('--text-primary');
            root.style.removeProperty('--text-muted');
            
            root.style.removeProperty('--sensor-bg');
            root.style.removeProperty('--sensor-border');
            root.style.removeProperty('--sensor-title');
            root.style.removeProperty('--sensor-val');
            root.style.removeProperty('--sensor-status-bg');
            root.style.removeProperty('--sensor-status-border');
            root.style.removeProperty('--sensor-status-text');
        }
    }

    // On page load, set active button
    document.addEventListener("DOMContentLoaded", function() {
        const theme = localStorage.getItem('tengganglopi_theme') || 'cerah';
        const btnTheme = document.getElementById('btn-' + theme);
        if(btnTheme) btnTheme.classList.add('active');
    });
</script>
@endsection

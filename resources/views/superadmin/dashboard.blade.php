@extends('layouts.superadmin')

@section('title', 'Dashboard Superadmin')
@section('subtitle', 'Pusat Komando & Pemantauan Darurat')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Custom Styles for this dashboard */
        .log-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-top: 3px solid var(--danger);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 25px;
        }
        .log-panel-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: var(--danger);
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .log-table { width: 100%; border-collapse: collapse; }
        .log-table th { color: var(--text-muted); font-size: 11px; padding: 10px; text-transform: uppercase; }
        .log-table td { padding: 12px 10px; font-size: 13px; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .log-table tbody tr:hover { background: rgba(0,0,0,0.02); }
        
        .main-grid {
            display: block;
            margin-top: 20px;
        }

        .map-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 500px;
        }
        .panel-header-custom {
            padding: 15px 20px;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .map-container-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        #map { flex: 1; width: 100%; background: #e2e8f0; }

        /* Map Legend for Risk Zones */
        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            z-index: 1000;
            min-width: 220px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .legend-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 500;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
        }
        
        .list-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            height: 500px;
            display: flex;
            flex-direction: column;
        }
        .list-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
        }
        .list-header h3 { font-size: 15px; display: flex; align-items: center; gap: 8px; margin-bottom: 15px; }
        .search-input {
            width: 100%;
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            padding: 10px 15px;
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Outfit';
            font-size: 13px;
            outline: none;
        }
        .search-input:focus { border-color: var(--accent); }
        
        .vessel-list {
            flex: 1;
            overflow-y: auto;
        }
        .vessel-list::-webkit-scrollbar { width: 4px; }
        .vessel-list::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }

        .vessel-table { width: 100%; border-collapse: collapse; }
        .vessel-table th { font-size: 10px; padding: 12px 15px; color: var(--text-muted); text-transform: uppercase; background: var(--bg-main); position: sticky; top: 0; }
        .vessel-table td { padding: 12px 15px; font-size: 12px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .vessel-name { font-weight: 600; font-size: 13px; color: var(--text-primary); margin-bottom: 3px; }
        .vessel-sub { font-size: 11px; color: var(--text-muted); }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-family: 'Outfit';
            cursor: pointer;
            transition: 0.2s;
            line-height: 1.4;
        }
        .btn-outline:hover { background: rgba(0,0,0,0.05); color: var(--text-primary); border-color: var(--text-muted); }

        /* Sensor Grid */
        .sensor-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
            margin-top: 25px;
            overflow-x: auto;
        }
        @media (max-width: 1200px) {
            .sensor-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 768px) {
            .sensor-grid { grid-template-columns: repeat(2, 1fr); }
        }
        .sensor-card {
            background: var(--sensor-bg);
            border: 1px solid var(--sensor-border);
            border-radius: 8px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-width: 180px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .sensor-card-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--sensor-title);
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sensor-value-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-size: 14px;
            color: var(--sensor-val);
            font-family: 'Outfit', sans-serif;
        }
        .sensor-status {
            font-size: 11px;
            padding: 8px 10px;
            background: var(--sensor-status-bg);
            border: 1px solid var(--sensor-status-border);
            border-radius: 6px;
            color: var(--sensor-status-text);
            text-align: center;
            font-weight: 500;
        }
        .action-card {
            background: #2563eb;
            color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
            border: none;
            min-width: 180px;
        }
        .action-card:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(37,99,235,0.25); }
        .action-card-title { font-weight: 600; font-size: 13px; line-height: 1.4; letter-spacing: 0.5px; }

        /* New UI Components CSS */
        @keyframes blinkRed {
            0% { background-color: rgba(239, 68, 68, 0.2); border-color: #ef4444; color: #ef4444; box-shadow: 0 0 10px rgba(239,68,68,0.5); }
            50% { background-color: #ef4444; border-color: #ef4444; color: #fff; box-shadow: 0 0 20px rgba(239,68,68,0.8); }
            100% { background-color: rgba(239, 68, 68, 0.2); border-color: #ef4444; color: #ef4444; box-shadow: 0 0 10px rgba(239,68,68,0.5); }
        }
        .status-blink-red {
            animation: blinkRed 1s infinite !important;
        }

        /* SOS Overlay */
        #sosOverlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(220, 38, 38, 0.95);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            display: none;
        }
        #sosOverlay.active {
            display: flex;
            animation: sosFlash 0.5s infinite alternate;
        }
        @keyframes sosFlash {
            from { background: rgba(220, 38, 38, 0.95); }
            to { background: rgba(185, 28, 28, 1); }
        }
        .sos-icon { font-size: 80px; margin-bottom: 20px; animation: pulseSOS 1s infinite; }
        @keyframes pulseSOS {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .sos-title { font-size: 48px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; }
        .sos-message { font-size: 24px; font-weight: 500; margin-bottom: 40px; }
        .sos-btn {
            background: white; color: #dc2626; border: none; padding: 15px 40px;
            font-size: 18px; font-weight: 700; border-radius: 50px; cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: 0.3s;
        }
        .sos-btn:hover { background: #fca5a5; transform: translateY(-2px); }

        /* MPU Gauge */
        .gauge-container {
            position: relative; width: 100%; aspect-ratio: 2/1; margin-bottom: 15px;
        }
        .gauge-svg { width: 100%; height: 100%; overflow: visible; }
        .gauge-bg { fill: none; stroke: var(--border-color); stroke-width: 15; stroke-linecap: round; }
        .gauge-val { fill: none; stroke: var(--success); stroke-width: 15; stroke-dasharray: 283; stroke-dashoffset: 283; stroke-linecap: round; transition: stroke-dashoffset 0.5s ease, stroke 0.5s ease; }
        .gauge-text { position: absolute; bottom: 0; left: 0; width: 100%; text-align: center; font-size: 24px; font-weight: 800; color: var(--text-primary); line-height: 1; }
        .gauge-label { position: absolute; bottom: -15px; left: 0; width: 100%; text-align: center; font-size: 10px; font-weight: 600; color: var(--text-muted); }

        /* AI Badge */
        .ai-badge {
            background: var(--success); color: white; padding: 12px 15px; border-radius: 8px;
            text-align: center; font-size: 16px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 5px;
            margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s;
        }
        .ai-badge.waspada { background: var(--warning); color: #1e293b; }
        .ai-badge.bahaya { background: var(--danger); animation: blinkRed 1s infinite; }
        
        /* Water Tube */
        .water-tube {
            width: 50px; height: 100px; border: 3px solid var(--border-color); border-top: none;
            border-radius: 0 0 25px 25px; position: relative; margin: 0 auto; overflow: hidden; background: rgba(0,0,0,0.02);
        }
        .water-fill {
            position: absolute; bottom: 0; left: 0; width: 100%; height: 0%;
            background: var(--success); border-radius: 0 0 20px 20px; transition: height 0.5s ease, background 0.5s;
        }
        .water-fill.waspada { background: var(--warning); }
        .water-fill.bahaya { background: var(--danger); animation: blinkRed 1s infinite; }
        
        /* Map Popup Button */
        .leaflet-popup-content .map-action-btn {
            background: var(--accent); color: white; border: none; padding: 6px 12px; border-radius: 6px;
            font-size: 11px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 8px; display: block; text-align: center; text-decoration: none;
        }
        .leaflet-popup-content .map-action-btn:hover { background: #1d4ed8; color: white; }

        /* Status Colors */

        /* Weather Widget */
        .weather-widget {
            background: var(--bg-sidebar);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .weather-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }
        .weather-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .weather-title svg { color: var(--accent); width: 20px; height: 20px; }
        .weather-title h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0; }
        .weather-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .wita-clock {
            font-size: 14px;
            font-weight: 700;
            color: var(--accent);
            background: rgba(37, 99, 235, 0.1);
            padding: 6px 12px;
            border-radius: 6px;
        }
        .weather-select {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: var(--bg-main);
            color: var(--text-primary);
            font-size: 13px;
            outline: none;
            min-width: 150px;
        }
        .weather-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            overflow-x: auto;
        }
        .weather-tab {
            background: transparent;
            border: 1px solid var(--border-color);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .weather-tab.active, .weather-tab:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }
        .weather-content {
            min-height: 120px;
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .weather-card {
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            min-width: 140px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .weather-card .time { font-size: 12px; font-weight: 600; color: var(--text-muted); }
        .weather-card img { width: 40px; height: 40px; }
        .weather-card .temp { font-size: 18px; font-weight: 700; color: var(--text-primary); }
        .weather-card .desc { font-size: 12px; color: var(--text-muted); line-height: 1.2; }
        .weather-loading {
            width: 100%; text-align: center; color: var(--text-muted); font-size: 13px; padding: 20px;
        }
    
        /* Sensor Modal Overlay */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(5px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background: var(--bg-main);
            width: 95%;
            max-width: 1100px;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalFadeIn 0.3s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-close {
            position: absolute;
            top: 25px; right: 25px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            width: 36px; height: 36px;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-muted);
            display: flex; justify-content: center; align-items: center;
            transition: all 0.2s;
        }
        .modal-close:hover { color: var(--danger); border-color: var(--danger); background: rgba(248, 113, 113, 0.1); }

    </style>
@endsection

@section('content')
<!-- BMKG Weather Widget -->
<div class="log-panel" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; flex-wrap: wrap; gap: 10px;">
        <div>
            <div style="font-weight: 700; color: var(--text-primary); font-size: 16px;">Informasi Cuaca & Waktu</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Sumber Data: BMKG - <span id="bmkg-station-name" style="font-weight: 600; color: var(--accent);">Stasiun Meteorologi Tampa Padang (Mamuju)</span></div>
        </div>
        <select id="bmkg-region-select" class="weather-select" onchange="fetchWeatherBMKG()" style="font-family: 'Outfit', sans-serif;">
            <option value="76.02">Stasiun Meteorologi Tampa Padang (Mamuju)</option>
            <option value="76.05">Stasiun Meteorologi Majene</option>
            <option value="73.71">Stasiun Meteorologi Paotere (Makassar)</option>
            <option value="73.08">Stasiun Klimatologi Sulawesi Selatan (Maros)</option>
            <option value="73.01">Stasiun Meteorologi Aroepala (Selayar)</option>
            <option value="73.06">Stasiun Geofisika Gowa</option>
            <option value="73.22">Stasiun Meteorologi Andi Jemma (Luwu Utara)</option>
        </select>
    </div>
    
    <div class="weather-tabs" id="weather-tabs-container">
        <!-- Tabs generated via JS -->
    </div>
    
    <div class="weather-content" id="weather-content-container">
        <div class="weather-loading">Mengambil data dari server BMKG...</div>
    </div>
</div>

<!-- Log Panel -->
<div class="log-panel">
    <div class="log-panel-header">
        <div style="width: 12px; height: 12px; background: var(--danger); border-radius: 50%; box-shadow: 0 0 10px var(--danger);"></div>
        PUSAT INFORMASI DARURAT & LOG EVENT
    </div>
    <div style="overflow-x: auto;">
        <table class="log-table">
            <thead>
                <tr>
                    <th style="width: 15%; text-align: left;">Waktu</th>
                    <th style="width: 70%; text-align: left;">Peristiwa</th>
                    <th style="width: 15%; text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody id="log-table-body">
                <!-- Log akan dirender via JS dari Firestore -->
                <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Memuat riwayat log...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Main Content (Full Width List) -->
<div class="main-grid">
    <!-- List Panel -->
    <div class="list-panel">
        <div class="list-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <h3 style="margin: 0;">Daftar Kapal Nelayan</h3>
                <span id="vessel-count" style="font-size: 14px; background: rgba(0,0,0,0.05); padding: 5px 12px; border-radius: 20px; font-weight: 500;">Total: 5</span>
            </div>
            
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <select id="vessel-filter-type" style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-primary); outline: none; font-family: 'Outfit', sans-serif; font-size: 14px;">
                    <option value="all">Filter: Semua</option>
                    <option value="node">Berdasarkan Node ID</option>
                    <option value="name">Berdasarkan Nama Kapal</option>
                </select>
                
                <div style="position: relative;">
                    <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="vessel-search-input" placeholder="Cari..." style="padding: 8px 15px 8px 38px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-primary); outline: none; font-family: 'Outfit', sans-serif; font-size: 14px; width: 220px;">
                </div>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="vessel-table">
                <thead>
                    <tr>
                        <th style="width: 15%; text-align: left;">ID Node</th>
                        <th style="width: 45%; text-align: left;">Nama Kapal & Info AI</th>
                        <th style="width: 20%; text-align: left;">Status Node</th>
                        <th style="width: 20%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="vessel-table-body">
                    <!-- Data kapal akan dirender via JavaScript (AJAX) dari Firestore -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="global-sensor-panel" style="display: none; padding: 25px; border-top: 1px solid var(--border-color); background: var(--bg-card); border-radius: 0 0 12px 12px; margin-bottom: 20px;">
    <!-- Sensor Panel Header -->
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-end;">
        <h3 id="sensor-panel-title" style="font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent);"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            Menampilkan Sensor: Kapal
        </h3>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div class="lora-signal" title="LoRaWAN Signal">
                <div class="lora-bar active" style="height: 8px;" id="loraBar1"></div>
                <div class="lora-bar active" style="height: 12px;" id="loraBar2"></div>
                <div class="lora-bar active" style="height: 16px;" id="loraBar3"></div>
                <div class="lora-bar" style="height: 20px;" id="loraBar4"></div>
                <span class="lora-status-text" id="loraStatusText">-85 dBm</span>
            </div>
            <span id="sensor-panel-status" style="font-size: 12px; color: var(--text-muted);">Status Data: Simulator</span>
        </div>
    </div>

    <!-- Sensor Grid -->
    <div class="sensor-grid" id="sensor-grid-container">
        <!-- Kemiringan -->
        <div class="sensor-card">
            <div class="sensor-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M2 12h4l2-9 4 18 2-9h6"></path></svg>
                Kemiringan (MPU-6050)
            </div>
            <div class="sensor-value-area" style="align-items: center;">
                <div class="gauge-container">
                    <svg class="gauge-svg" viewBox="0 0 200 100">
                        <path class="gauge-bg" d="M 10 100 A 90 90 0 0 1 190 100"></path>
                        <path class="gauge-val" id="mpuGaugeVal" d="M 10 100 A 90 90 0 0 1 190 100"></path>
                    </svg>
                    <div class="gauge-text" id="val-roll">- - &deg;</div>
                    <div class="gauge-label">ROLL ANGLE</div>
                </div>
                <div style="display:flex; justify-content:space-around; width: 100%; margin-top: 15px; font-weight:700; font-size:12px;">
                    <div style="text-align: center;"><div style="font-size:10px; color:var(--text-muted); font-weight: 500;">PITCH</div><span id="val-pitch">- - &deg;</span></div>
                    <div style="text-align: center;"><div style="font-size:10px; color:var(--text-muted); font-weight: 500;">YAW</div><span id="val-yaw">- - &deg;</span></div>
                </div>
            </div>
            <div class="sensor-status" id="status-mpu">Sensor Mati (Offline)</div>
        </div>
        
        <!-- Cuaca -->
        <div class="sensor-card">
            <div class="sensor-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"></path></svg>
                Iklim Mikro (BME280)
            </div>
            <div class="sensor-value-area">
                <div class="ai-badge" id="bmeAiBadge">
                    <svg id="bmeAiIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    <span id="bmeAiText">MENUNGGU DATA</span>
                </div>
                <div style="background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: 8px; padding: 10px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:5px; font-size:10px; color:var(--sensor-title); font-family: 'Outfit', sans-serif;">
                        <span>SUHU</span><span>RH</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; font-size:16px;">
                        <span id="val-temp">- - &deg;C</span>
                        <span id="val-rh" style="color:var(--accent)">- - %</span>
                    </div>
                    <div style="text-align:center; margin-top:10px; font-size:12px; font-weight:600; color:var(--text-muted);" id="val-press">- - hPa</div>
                </div>
            </div>
            <div class="sensor-status" id="status-bme">Model Edge AI: Offline</div>
        </div>

        <!-- Ultrasonik -->
        <div class="sensor-card">
            <div class="sensor-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M2 12h20"></path><path d="M2 16h20"></path><path d="M2 20h20"></path><path d="M12 4v4"></path><path d="M10 6l2-2 2 2"></path></svg>
                Tinggi Air Palka
            </div>
            <div class="sensor-value-area" style="flex-direction: row; gap: 15px; align-items:center;">
                <div style="width: 40px; height: 100px; border: 2px solid var(--border-color); border-radius: 4px; position: relative; background: rgba(0,0,0,0.02); overflow: hidden; display: flex; flex-direction: column; justify-content: flex-end;">
                    <div id="val-water-bar" style="width: 100%; height: 0%; background: var(--accent); transition: height 0.5s ease; border-radius: 0 0 2px 2px;"></div>
                </div>
                <div style="flex:1;">
                    <div style="font-size:32px; font-weight:700; color:var(--text-primary); display:flex; align-items: baseline; gap: 5px;">
                        <span id="val-water">- -</span> <span style="font-size:14px; font-weight:600; color:var(--text-muted)">cm</span>
                    </div>
                    <div style="font-size:12px; font-weight:600; margin-top:5px;" id="val-water-pct">- - % Penuh</div>
                </div>
            </div>
            <div class="sensor-status" id="status-ultra">Sensor Mati (Offline)</div>
        </div>
        
        <!-- Baterai -->
        <div class="sensor-card">
            <div class="sensor-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect width="16" height="10" x="2" y="7" rx="2" ry="2"></rect><line x1="22" x2="22" y1="11" y2="13"></line></svg>
                Sisa Baterai (Li-Ion)
            </div>
            <div class="sensor-value-area">
                <div id="val-batt" style="font-weight:700; font-size:24px; margin-bottom:10px;">- - %</div>
                <div style="height:6px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                    <div id="val-batt-bar" style="width:0%; height:100%; background:var(--danger);"></div>
                </div>
            </div>
            <div class="sensor-status" id="status-batt">Sensor Mati (Offline)</div>
        </div>
        
        <!-- LoRa -->
        <div class="sensor-card">
            <div class="sensor-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M2 20h.01"></path><path d="M7 20v-4"></path><path d="M12 20v-8"></path><path d="M17 20V8"></path><path d="M22 4v16"></path></svg>
                LoRa SX1276 (RSSI)
            </div>
            <div class="sensor-value-area">
                <div style="display:flex; justify-content:space-between; font-weight:700; font-size:20px;">
                    <span id="val-lora">- -</span><span style="font-size:14px; font-weight:500;">dBm</span>
                </div>
            </div>
            <div class="sensor-status" id="status-lora">Tidak ada sinyal</div>
        </div>

        <!-- Action Card -->
        <button class="action-card" onclick="triggerBuzzer()" style="background: var(--danger); border-color: var(--danger);">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"><path d="M13.73 21a2 2 0 0 1-3.46 0"></path><path d="M18.63 13A17.89 17.89 0 0 1 18 8"></path><path d="M6.26 6.26A5.86 5.86 0 0 0 6 8c0 7-3 9-3 9h14"></path><path d="M18 8a6 6 0 0 0-9.33-5"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
            <div class="action-card-title" style="color:white;">NYALAKAN<br>BUZZER DARURAT</div>
        </button>
    </div>
</div>


<script>
    let currentBoatStatus = 'Offline';

    let fleetNodesData = {};
    let activeNodeId = null;

    // Search and Filter Logic
    document.getElementById('vessel-search-input').addEventListener('keyup', filterVessels);
    document.getElementById('vessel-filter-type').addEventListener('change', filterVessels);

    function filterVessels() {
        const query = document.getElementById('vessel-search-input').value.toLowerCase();
        const filterType = document.getElementById('vessel-filter-type').value;
        const rows = document.querySelectorAll('.vessel-table tbody .vessel-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const nodeId = row.children[0].innerText.toLowerCase();
            const vesselName = row.querySelector('.vessel-name').innerText.toLowerCase();
            
            let match = false;
            if (filterType === 'all') {
                match = nodeId.includes(query) || vesselName.includes(query);
            } else if (filterType === 'node') {
                match = nodeId.includes(query);
            } else if (filterType === 'name') {
                match = vesselName.includes(query);
            }

            if (match) {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
                // If it's expanded, collapse it
                const nextRow = row.nextElementSibling;
                if (nextRow && nextRow.classList.contains('sensor-row') && nextRow.style.display === 'table-row') {
                    nextRow.style.display = 'none';
                }
            }
        });
        
        document.getElementById('vessel-count').innerText = `Total: ${visibleCount}`;
    }

    function toggleSensorRow(rowElement, vesselName, status) {
        const nextRow = rowElement.nextElementSibling;
        const sensorPanel = document.getElementById('global-sensor-panel');
        
        if (nextRow.style.display === 'table-row' && nextRow.querySelector('.sensor-container').contains(sensorPanel)) {
            nextRow.style.display = 'none';
            return;
        }
        
        document.querySelectorAll('.sensor-row').forEach(row => row.style.display = 'none');
        nextRow.querySelector('.sensor-container').appendChild(sensorPanel);
        sensorPanel.style.display = 'block';
        nextRow.style.display = 'table-row';
        
        updateSensorPanelWithRealData(rowElement.getAttribute('data-node-id'));
    }

    function fetchFleetData() {
        fetch("{{ route('api.fleet.all') }}")
            .then(res => res.json())
            .then(response => {
                if(response.status === 'success') {
                    const nodes = response.data;
                    document.getElementById('vessel-count').innerText = `Total: ${nodes.length}`;
                    
                    const tbody = document.querySelector('.vessel-table-body');
                    if (!tbody) return;
                    
                    // Simpan status detail mana yang sedang terbuka
                    const activeSensorPanel = document.getElementById('global-sensor-panel');
                    const activeRowId = activeSensorPanel.parentElement && activeSensorPanel.closest('.sensor-row') && activeSensorPanel.closest('.sensor-row').previousElementSibling 
                        ? activeSensorPanel.closest('.sensor-row').previousElementSibling.getAttribute('data-node-id') 
                        : null;
                    
                    tbody.innerHTML = ''; // bersihkan tabel
                    
                    nodes.forEach(node => {
                        fleetNodesData[node.id] = node; // Save real data globally
                        
                        const isWarning = node.buzzerSignal === 'ON' || (node.waterLevel && node.waterLevel < 20);
                        const statusColor = isWarning ? 'var(--warning)' : 'var(--success)';
                        const statusText = isWarning ? 'Warning' : 'Online';
                        const aiInfo = isWarning ? 'Al: Butuh Perhatian' : 'Al: Cuaca Aman';
                        
                        const tr = document.createElement('tr');
                        tr.className = 'vessel-row';
                        tr.setAttribute('data-node-id', node.id);
                        tr.style.cursor = 'pointer';
                        tr.onclick = function() { toggleSensorRow(this, node.id, statusText); };
                        
                        tr.innerHTML = `
                            <td style="color: var(--text-muted);">${node.id}</td>
                            <td>
                                <div class="vessel-name">${node.vesselName || node.id}</div>
                                <div class="vessel-sub">${node.fishermanName || 'Unknown'} - ${aiInfo}</div>
                            </td>
                            <td style="color: ${statusColor};">${statusText}</td>
                            <td style="text-align: center;"><button class="btn-outline">Pantau<br>Sensor</button></td>
                        `;
                        
                        const trSensor = document.createElement('tr');
                        trSensor.className = 'sensor-row';
                        trSensor.style.display = 'none';
                        trSensor.style.background = 'rgba(0,0,0,0.01)';
                        trSensor.innerHTML = `<td colspan="4" class="sensor-container" style="padding: 0;"></td>`;
                        
                        tbody.appendChild(tr);
                        tbody.appendChild(trSensor);
                        
                        // Kembalikan panel jika sebelumnya terbuka
                        if(activeRowId === node.id) {
                            trSensor.querySelector('.sensor-container').appendChild(activeSensorPanel);
                            trSensor.style.display = 'table-row';
                            activeSensorPanel.style.display = 'block';
                            updateSensorPanelWithRealData(node.id);
                        }
                    });
                }
            })
            .catch(console.error);
    }

    function updateSensorPanelWithRealData(nodeId) {
        if (!fleetNodesData[nodeId]) return;
        const node = fleetNodesData[nodeId];
        activeNodeId = nodeId;

        // 1. MPU6050
        let roll = node.gyroscope?.x ?? 0;
        let pitch = node.gyroscope?.y ?? 0;
        let yaw = node.gyroscope?.z ?? 0;
        
        document.getElementById('val-roll').innerHTML = roll + '&deg;';
        document.getElementById('val-pitch').innerHTML = pitch + '&deg;';
        document.getElementById('val-yaw').innerHTML = yaw + '&deg;';
        
        let maxDash = 283;
        let fill = (Math.abs(roll) / 180) * maxDash;
        document.getElementById('mpuGaugeVal').style.strokeDashoffset = maxDash - fill;

        let mpuStatus = 'AMAN';
        let mpuColor = 'var(--success)';
        document.getElementById('mpuGaugeVal').classList.remove('status-blink-red');

        if (Math.abs(roll) >= 90) { mpuStatus = 'TERBALIK'; mpuColor = 'var(--danger)'; document.getElementById('mpuGaugeVal').classList.add('status-blink-red'); }
        else if (Math.abs(roll) >= 60) { mpuStatus = 'WASPADA'; mpuColor = 'var(--warning)'; }
        
        document.getElementById('mpuGaugeVal').style.stroke = mpuColor;
        document.getElementById('status-mpu').innerText = mpuStatus;
        document.getElementById('status-mpu').style.color = mpuColor;

        // 2. HC-SR04
        let waterDist = node.waterLevel ?? 60; // default aman
        document.getElementById('val-water').innerText = waterDist;
        let waterPct = Math.max(0, 100 - (waterDist / 50 * 100));
        let waterBar = document.getElementById('val-water-bar');
        waterBar.style.height = waterPct + '%';
        document.getElementById('val-water-pct').innerText = Math.round(waterPct) + '% Penuh';
        waterBar.classList.remove('bahaya', 'waspada');
        
        let waterStatus = 'AMAN (KERING)';
        let waterColor = 'var(--success)';
        if (waterDist < 15) { waterStatus = 'TENGGELAM (KRITIS)'; waterColor = 'var(--danger)'; waterBar.classList.add('bahaya'); }
        else if (waterDist < 50) { waterStatus = 'BOCOR (AIR MASUK)'; waterColor = 'var(--warning)'; waterBar.classList.add('waspada'); }
        
        document.getElementById('status-ultra').innerText = waterStatus;
        document.getElementById('status-ultra').style.color = waterColor;

        // 3. BME280 (Real Data dari Firebase)
        let temp = node.bme280?.temperature ?? 0;
        let rh = node.bme280?.humidity ?? 0;
        let press = node.bme280?.pressure ?? 0;
        
        document.getElementById('val-temp').innerHTML = temp + '&deg;C';
        document.getElementById('val-rh').innerHTML = rh + '%';
        document.getElementById('val-press').innerHTML = press + ' hPa';
        
        let aiBadge = document.getElementById('bmeAiBadge');
        aiBadge.classList.remove('waspada', 'bahaya');
        
        let aiStatus = 'CUACA AMAN';
        let aiColor = 'var(--success)';
        if (temp > 33) { aiStatus = 'CUACA BAHAYA'; aiColor = 'var(--danger)'; aiBadge.classList.add('bahaya'); }
        else if (temp > 29) { aiStatus = 'CUACA WASPADA'; aiColor = 'var(--warning)'; aiBadge.classList.add('waspada'); }
        
        document.getElementById('bmeAiText').innerText = aiStatus;
        document.getElementById('status-bme').innerText = 'Model Edge AI: Aktif';

        // 4. Baterai (Sementara kita asumsikan 100% jika ESP tidak mengirim baterai)
        let batt = node.battery ?? 100;
        document.getElementById('val-batt').innerText = batt + ' %';
        let battBar = document.getElementById('val-batt-bar');
        battBar.style.width = batt + '%';
        battBar.style.background = (batt > 50) ? 'var(--success)' : ((batt > 20) ? 'var(--warning)' : 'var(--danger)');
        document.getElementById('status-batt').innerText = (batt > 20) ? 'Baterai Normal' : 'Baterai Lemah';
        document.getElementById('status-batt').style.color = (batt > 20) ? 'var(--success)' : 'var(--danger)';

        // 5. Lora Signal
        let rssi = node.heartbeat ?? -85; // asumsikan heartbeat menyimpan rssi
        document.getElementById('loraStatusText').innerText = rssi + ' dBm';
        document.getElementById('loraBar1').classList.add('active');
        document.getElementById('loraBar2').classList.toggle('active', rssi > -105);
        document.getElementById('loraBar3').classList.toggle('active', rssi > -95);
        document.getElementById('loraBar4').classList.toggle('active', rssi > -85);
        
        document.getElementById('val-lora').innerText = rssi;
        document.getElementById('status-lora').innerText = 'Terkoneksi';
        document.getElementById('status-lora').style.color = 'var(--success)';
    }

    function triggerBuzzer() {
        if (!activeNodeId) {
            alert("Silakan pilih kapal terlebih dahulu!");
            return;
        }
        
        // Asumsi hanya untuk NODE_01 sesuai dengan route di FirebaseTrackingController
        fetch("{{ route('api.fleet.buzzer') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ signal: 'ON' }) // atau PING
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Sinyal BUZZER DARURAT berhasil dikirim ke perangkat kapal!");
            } else {
                alert("Gagal memicu buzzer: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Gagal terhubung ke API Buzzer.");
        });
    }

    // BMKG Weather API logic
    function fetchWeatherBMKG() {
        const region = document.getElementById('bmkg-region-select').value;
        const container = document.getElementById('weather-content-container');
        const tabsContainer = document.getElementById('weather-tabs-container');
        
        container.innerHTML = '<div class="weather-loading">Mengambil data dari server BMKG...</div>';
        
        const sel = document.getElementById('bmkg-region-select');
        const regionText = sel.options[sel.selectedIndex].text;
        document.getElementById('bmkg-station-name').innerText = regionText;

        fetch('/proxy/bmkg?adm2=' + region)
            .then(res => res.json())
            .then(data => {
                if(!data || !data.data || !data.data[0] || !data.data[0].cuaca) {
                    container.innerHTML = '<div class="weather-loading">Gagal memuat data cuaca dari BMKG.</div>';
                    return;
                }
                
                const cuacaList = data.data[0].cuaca.flat();
                let tabsHTML = '';
                let contentHTML = '';
                
                const grouped = {};
                cuacaList.forEach(item => {
                    const datePart = item.local_datetime.split(' ')[0];
                    if(!grouped[datePart]) grouped[datePart] = [];
                    grouped[datePart].push(item);
                });
                
                let isFirst = true;
                for(const date in grouped) {
                    const d = new Date(date);
                    let tabName = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short' });
                    if(isFirst) tabName = "Hari Ini";
                    
                    tabsHTML += `<button class="weather-tab ${isFirst ? 'active' : ''}" onclick="switchWeatherTab(this, 'date-${date}')">${tabName}</button>`;
                    
                    contentHTML += `<div class="weather-day-group" id="date-${date}" style="display: ${isFirst ? 'flex' : 'none'}; gap: 15px; width: 100%; overflow-x: auto;">`;
                    grouped[date].forEach(w => {
                        const time = w.local_datetime.split(' ')[1].substring(0,5);
                        contentHTML += `
                            <div class="weather-card">
                                <div class="time">${time}</div>
                                <img src="${w.image}" alt="${w.weather_desc}">
                                <div class="temp">${w.t}&deg;C</div>
                                <div class="desc">${w.weather_desc}</div>
                            </div>
                        `;
                    });
                    contentHTML += `</div>`;
                    
                    isFirst = false;
                }
                
                tabsContainer.innerHTML = tabsHTML;
                container.innerHTML = contentHTML;
            })
            .catch(err => {
                container.innerHTML = '<div class="weather-loading">Terjadi kesalahan jaringan saat mengambil data BMKG.</div>';
                console.error(err);
            });
    }

    function switchWeatherTab(btn, dateId) {
        document.querySelectorAll('.weather-tab').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.weather-day-group').forEach(el => el.style.display = 'none');
        document.getElementById(dateId).style.display = 'flex';
    }

    document.addEventListener("DOMContentLoaded", function() {
        fetchWeatherBMKG();
        fetchFleetData();
        fetchRecentLogs();
        setInterval(fetchFleetData, 10000); // refresh list kapal tiap 10 detik
        setInterval(fetchRecentLogs, 15000); // refresh log tiap 15 detik
    });

    // Mengambil Log dari Firestore
    function fetchRecentLogs() {
        fetch("{{ route('api.history.logs') }}")
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const tbody = document.getElementById('log-table-body');
                    tbody.innerHTML = '';
                    
                    if (response.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada riwayat darurat</td></tr>';
                        return;
                    }

                    response.data.forEach(log => {
                        const tr = document.createElement('tr');
                        const statusColor = log.level === 'Critical' ? 'var(--danger)' : (log.level === 'Warning' ? 'var(--warning)' : 'var(--success)');
                        
                        tr.innerHTML = `
                            <td>${log.time || '-'}</td>
                            <td>${log.message || '-'}</td>
                            <td style="text-align: right; color: ${statusColor}; font-weight: 600;">${log.level || 'Info'}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(console.error);
    }

</script>


@endsection


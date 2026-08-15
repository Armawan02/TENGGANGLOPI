@extends('layouts.superadmin')

@section('title', 'Peta Perairan')
@section('subtitle', 'Tracking Kapal Real-Time')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .map-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 150px);
            width: 100%;
        }
        #map-full {
            flex: 1;
            width: 100%;
            background: #e2e8f0;
        }

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
        .ship-tooltip {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 8px 12px;
            line-height: 1.3;
        }
        .ship-tooltip::before { border-right-color: rgba(255, 255, 255, 0.95); }
    </style>
@endsection

@section('content')
<div class="map-container" style="position: relative;">
    <div id="map-full"></div>
    <div class="map-legend">
        <div class="legend-title">Indikator Cuaca Ekstrem</div>
        <div class="legend-item">
            <div class="legend-color" style="background: rgba(248, 113, 113, 0.4); border: 2px solid var(--danger);"></div>
            <span>Badai / Gelombang Tinggi</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: rgba(251, 191, 36, 0.4); border: 2px solid var(--warning);"></div>
            <span>Hujan Lebat / Angin Kencang</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: rgba(52, 211, 153, 0.4); border: 2px solid var(--success);"></div>
            <span>Aman</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var map = L.map('map-full').setView([-3.543, 118.974], 12);

        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a>'
        }).addTo(map);

        // Red Zone (Danger) - BMKG Data
        var dangerCircle = L.circle([-3.650, 118.850], {
            color: '#F87171',
            fillColor: '#F87171',
            fillOpacity: 0.2,
            radius: 8000 // 8km radius
        }).addTo(map);
        dangerCircle.bindPopup("<b>Status: BAHAYA</b><br>Gelombang setinggi 3 meter. Hindari area ini.");

        // Yellow Zone (Warning) - BMKG Data
        var warningCircle = L.circle([-3.450, 119.100], {
            color: '#FBBF24',
            fillColor: '#FBBF24',
            fillOpacity: 0.2,
            radius: 12000 // 12km radius
        }).addTo(map);
        warningCircle.bindPopup("<b>Status: WASPADA</b><br>Hujan badai dengan angin 25 knot.");

        var shipIcon = L.divIcon({
            html: `<div style="background:var(--accent); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #fff; box-shadow:0 0 10px rgba(0,0,0,0.5);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="5" r="3"></circle><line x1="12" y1="22" x2="12" y2="8"></line><path d="M5 12H2a10 10 0 0 0 20 0h-3"></path></svg>
                   </div>`,
            className: 'custom-ship-icon',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        var markerNode1 = L.marker([-3.580, 118.930], {icon: shipIcon}).addTo(map)
            .bindTooltip('<div style="font-weight:700; color:#64748b; font-size:10px;">NODE_01</div><div style="font-weight:700; color:#1e293b; font-size:12px;">Salili Mandar</div><div id="node1_status" style="color:#64748b; font-size:11px; font-weight:600;">Memuat koneksi...</div><div id="node1_data" style="font-size:10px; color:#475569; margin-top:3px; line-height:1.4;"></div>', {permanent: true, direction: 'right', offset: [15, 0], className: 'ship-tooltip'});
        
        L.marker([-3.560, 118.900], {icon: shipIcon}).addTo(map)
            .bindTooltip('<div style="font-weight:700; color:#64748b; font-size:10px;">NODE_02</div><div style="font-weight:700; color:#1e293b; font-size:12px;">Bintang Laut</div><div style="color:#10b981; font-size:11px; font-weight:600;">Status: Online</div>', {permanent: true, direction: 'right', offset: [15, 0], className: 'ship-tooltip'})
            .bindPopup('<strong style="color:#000;">Bintang Laut</strong><br><span style="color:#22c55e;">Status: Online</span>');
        
        L.marker([-3.600, 118.950], {icon: shipIcon}).addTo(map)
            .bindTooltip('<div style="font-weight:700; color:#64748b; font-size:10px;">NODE_03</div><div style="font-weight:700; color:#1e293b; font-size:12px;">Harapan Jaya</div><div style="color:#10b981; font-size:11px; font-weight:600;">Status: Online</div>', {permanent: true, direction: 'right', offset: [15, 0], className: 'ship-tooltip'})
            .bindPopup('<strong style="color:#000;">Harapan Jaya</strong><br><span style="color:#22c55e;">Status: Online</span>');

        // ==== LOGIKA PENGAMBILAN DATA FIRESTORE REAL-TIME ====
        function fetchNode1Data() {
            fetch("{{ route('api.fleet.node01') }}")
                .then(response => response.json())
                .then(res => {
                    if(res.status === 'success') {
                        let data = res.data;
                        let statusEl = document.getElementById('node1_status');
                        let dataEl = document.getElementById('node1_data');
                        
                        if(statusEl && dataEl) {
                            statusEl.innerHTML = 'Status: Terhubung 🟢';
                            statusEl.style.color = '#10b981';
                            
                            let latText = data.coordinates ? data.coordinates.lat : '-';
                            let lngText = data.coordinates ? data.coordinates.lng : '-';
                            
                            // Konversi UNIX timestamp (detik) ke format jam yang mudah dibaca
                            let lastSeenTime = data.heartbeat ? new Date(data.heartbeat * 1000).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit', second:'2-digit'}) : '-';
                            
                            dataEl.innerHTML = `
                                📍 GPS: ${latText}, ${lngText}<br>
                                💧 Air: ${data.waterLevel} cm<br>
                                🕒 Update: ${lastSeenTime}<br>
                                🧭 Gyro X: ${data.gyroscope.x} | Y: ${data.gyroscope.y} | Z: ${data.gyroscope.z}<br>
                                🔔 Buzzer: ${data.buzzerSignal}
                            `;
                        }
                        
                        // Update posisi GPS pada Marker
                        if(data.coordinates && data.coordinates.lat && data.coordinates.lng) {
                            let newLatLng = new L.LatLng(data.coordinates.lat, data.coordinates.lng);
                            markerNode1.setLatLng(newLatLng);
                            // Opsional: pusatkan peta ke kapal
                            // map.panTo(newLatLng);
                        }
                    }
                })
                .catch(err => {
                    let statusEl = document.getElementById('node1_status');
                    if(statusEl) {
                        statusEl.innerHTML = 'Status: Terputus 🔴';
                        statusEl.style.color = '#ef4444';
                    }
                });
        }
        
        // Panggil pertama kali dan ulang setiap 3 detik
        fetchNode1Data();
        setInterval(fetchNode1Data, 3000);
    });
</script>
@endsection

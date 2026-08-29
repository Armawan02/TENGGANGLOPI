@extends('layouts.petugas')

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
    </style>
@endsection

@section('content')
<div class="map-container" style="position: relative;">
    <div style="padding: 15px 20px; background: var(--bg-card); border-bottom: 1px solid var(--border-color); display: flex; gap: 10px; align-items: center; z-index: 10;">
        <input type="text" id="searchInput" onkeypress="if(event.key === 'Enter') searchNode()" placeholder="Cari kode node (misal: NODE_01)..." style="flex: 1; max-width: 400px; padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 8px; outline: none; font-size: 14px; background: var(--bg-main); color: var(--text-primary);">
        <button onclick="searchNode()" style="background: var(--accent); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cari Kapal</button>
    </div>
    <div id="map-full"></div>
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

        var bmkgIcon = L.divIcon({
            html: `<div style="background:#f59e0b; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #fff; box-shadow:0 0 10px rgba(0,0,0,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"></path></svg>
                   </div>`,
            className: 'custom-bmkg-icon',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
        
        var bmkgStations = [
            { name: 'Stasiun Meteorologi Tampa Padang (Mamuju)', lat: -2.5900, lng: 119.0251 },
            { name: 'Stasiun Meteorologi Majene', lat: -3.5507, lng: 118.9805 },
            { name: 'Stasiun Meteorologi Paotere (Makassar)', lat: -5.1186, lng: 119.4124 },
            { name: 'Stasiun Klimatologi Sulawesi Selatan (Maros)', lat: -5.0592, lng: 119.5539 },
            { name: 'Stasiun Meteorologi Aroepala (Selayar)', lat: -6.1666, lng: 120.4439 },
            { name: 'Stasiun Geofisika Gowa', lat: -5.2045, lng: 119.4674 },
            { name: 'Stasiun Meteorologi Andi Jemma (Luwu Utara)', lat: -2.5594, lng: 120.3235 }
        ];

        bmkgStations.forEach(function(station) {
            L.marker([station.lat, station.lng], {icon: bmkgIcon}).addTo(map)
             .bindPopup('<strong style="color:var(--text-primary); font-size:12px;">' + station.name + '</strong><br><span style="color:var(--text-muted); font-size:11px;">Stasiun Pemantau Cuaca BMKG</span>');
        });



        var shipIcon = L.divIcon({
            html: `<div style="background:var(--accent); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #fff; box-shadow:0 0 10px rgba(0,0,0,0.5);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="5" r="3"></circle><line x1="12" y1="22" x2="12" y2="8"></line><path d="M5 12H2a10 10 0 0 0 20 0h-3"></path></svg>
                   </div>`,
            className: 'custom-ship-icon',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        // Simpan referensi marker yang aktif di peta
        let fleetMarkers = {};

        window.searchNode = function() {
            const query = document.getElementById('searchInput').value.trim().toUpperCase();
            if(!query) return;
            
            if(fleetMarkers[query]) {
                const latLng = fleetMarkers[query].getLatLng();
                map.flyTo(latLng, 17, { duration: 1.5 });
            } else {
                alert("Node kapal '" + query + "' tidak ditemukan di peta atau belum mengirimkan lokasi GPS.");
            }
        };

        function updateFleetMap() {
            fetch("{{ route('api.fleet.all') }}")
                .then(response => response.json())
                .then(res => {
                    if(res.status === 'success') {
                        let nodes = res.data;
                        
                        nodes.forEach(node => {
                            let isOffline = false;
                            if (node.heartbeat) {
                                let nowUnix = Math.floor(Date.now() / 1000);
                                if (nowUnix - node.heartbeat > 30) {
                                    isOffline = true;
                                }
                            }
                            
                            let statusText = isOffline ? 'Status: Terputus 🔴' : 'Status: Terhubung 🟢';
                            let statusColor = isOffline ? '#ef4444' : '#10b981';
                            
                            let latText = (node.coordinates && node.coordinates.lat !== 0) ? node.coordinates.lat : 'Belum Ada';
                            let lngText = (node.coordinates && node.coordinates.lng !== 0) ? node.coordinates.lng : 'Belum Ada';
                            
                            let lastSeenTime = node.heartbeat ? new Date(node.heartbeat * 1000).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit', second:'2-digit'}) : '-';
                            
                            let mpuX = node.gyroscope ? node.gyroscope.x : 0;
                            let mpuY = node.gyroscope ? node.gyroscope.y : 0;
                            let mpuZ = node.gyroscope ? node.gyroscope.z : 0;
                            
                            let tooltipContent = `
                                <div style="font-weight:700; color:#64748b; font-size:10px;">${node.id}</div>
                                <div style="font-weight:700; color:#1e293b; font-size:12px;">${node.vesselName}</div>
                                <div style="color:${statusColor}; font-size:11px; font-weight:600;">${statusText}</div>
                                <div style="font-size:10px; color:#475569; margin-top:3px; line-height:1.4;">
                                    📍 GPS: ${latText}, ${lngText}<br>
                                    💧 Air: ${node.waterLevel ?? 0} cm<br>
                                    🕒 Update: ${lastSeenTime}<br>
                                    🧭 Gyro X: ${mpuX} | Y: ${mpuY} | Z: ${mpuZ}<br>
                                    🔔 Buzzer: ${node.buzzerSignal ?? 'OFF'}
                                </div>
                            `;

                            // Jika koordinat GPS valid dan bukan 0,0
                            if (node.coordinates && node.coordinates.lat !== 0 && node.coordinates.lng !== 0) {
                                let newLatLng = new L.LatLng(node.coordinates.lat, node.coordinates.lng);
                                
                                // Jika marker belum ada, buat baru
                                if (!fleetMarkers[node.id]) {
                                    fleetMarkers[node.id] = L.marker(newLatLng, {icon: shipIcon}).addTo(map)
                                        .bindTooltip(tooltipContent, {permanent: true, direction: 'right', offset: [15, 0], className: 'ship-tooltip'});
                                } else {
                                    // Jika sudah ada, update posisi dan tooltip
                                    fleetMarkers[node.id].setLatLng(newLatLng);
                                    fleetMarkers[node.id].setTooltipContent(tooltipContent);
                                }
                            }
                        });
                    }
                })
                .catch(console.error);
        }
        
        // Panggil pertama kali dan ulang setiap 3 detik
        updateFleetMap();
        setInterval(updateFleetMap, 3000);
    });
</script>
@endsection


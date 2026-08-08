@extends('layouts.petugas')

@section('title', 'Risk Map (Cuaca)')
@section('subtitle', 'Pemantauan Cuaca & Anomali Area')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .risk-map-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 150px);
            width: 100%;
            position: relative;
        }
        #risk-map {
            flex: 1;
            width: 100%;
            background: #1a1c23;
        }
        
        .map-legend {
            position: absolute;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            padding: 15px;
            border-radius: 8px;
            z-index: 1000;
            color: var(--text-primary);
            min-width: 200px;
        }
        .legend-title {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
<div class="risk-map-container">
    <div id="risk-map"></div>
    
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
        var map = L.map('risk-map').setView([-3.543, 118.974], 11);

        // CartoDB Dark Matter
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        }).addTo(map);

        // Red Zone (Danger)
        var dangerCircle = L.circle([-3.650, 118.850], {
            color: '#F87171',
            fillColor: '#F87171',
            fillOpacity: 0.2,
            radius: 8000 // 8km radius
        }).addTo(map);
        dangerCircle.bindPopup("<b>Status: BAHAYA</b><br>Gelombang setinggi 3 meter. Hindari area ini.");

        // Yellow Zone (Warning)
        var warningCircle = L.circle([-3.450, 119.100], {
            color: '#FBBF24',
            fillColor: '#FBBF24',
            fillOpacity: 0.2,
            radius: 12000 // 12km radius
        }).addTo(map);
        warningCircle.bindPopup("<b>Status: WASPADA</b><br>Hujan badai dengan angin 25 knot.");
        
        // Add one ship inside danger zone to show risk
        var shipIcon = L.divIcon({
            html: `<div style="background:var(--accent); width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #fff; box-shadow:0 0 10px rgba(0,0,0,0.5);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="5" r="3"></circle><line x1="12" y1="22" x2="12" y2="8"></line><path d="M5 12H2a10 10 0 0 0 20 0h-3"></path></svg>
                   </div>`,
            className: 'custom-ship-icon',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        
        L.marker([-3.630, 118.860], {icon: shipIcon}).addTo(map).bindPopup('<strong style="color:#000;">Salili Mandar</strong><br><span style="color:#F87171;">Terjebak Badai!</span>');
    });
</script>
@endsection

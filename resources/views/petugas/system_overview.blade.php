@extends('layouts.petugas')

@section('title', 'System Overview')
@section('subtitle', 'Server Health & Performance Metrics')

@section('styles')
    <style>
        .grid-overview {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        .overview-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
        }
        .overview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .overview-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .metric-value {
            font-size: 42px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: var(--bg-main);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        /* Specific Colors */
        .color-accent { background: var(--accent); }
        .color-warning { background: var(--warning); }
        .color-success { background: var(--success); }
        .color-danger { background: var(--danger); }
        
        .node-list {
            margin-top: 20px;
        }
        .node-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
        }
        .node-item:last-child { border-bottom: none; }
    </style>
@endsection

@section('content')
<div class="grid-overview">
    <!-- CPU Usage -->
    <div class="overview-card">
        <div class="overview-header">
            <div class="overview-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                CPU Usage
            </div>
            <span style="color: var(--success); font-weight: 600; font-size: 12px;">Healthy</span>
        </div>
        <div class="metric-value">24<span style="font-size: 24px; color: var(--text-muted);">%</span></div>
        <div class="progress-bar">
            <div class="progress-fill color-success" style="width: 24%;"></div>
        </div>
        <div class="progress-label">
            <span>Core i7-10700K (16 Threads)</span>
            <span>2.8 GHz</span>
        </div>
    </div>

    <!-- Memory Usage -->
    <div class="overview-card">
        <div class="overview-header">
            <div class="overview-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                Memory (RAM)
            </div>
            <span style="color: var(--warning); font-weight: 600; font-size: 12px;">Moderate</span>
        </div>
        <div class="metric-value">12.4<span style="font-size: 24px; color: var(--text-muted);">GB</span></div>
        <div class="progress-bar">
            <div class="progress-fill color-warning" style="width: 77%;"></div>
        </div>
        <div class="progress-label">
            <span>Total: 16.0 GB</span>
            <span>77% Used</span>
        </div>
    </div>

    <!-- Storage Network -->
    <div class="overview-card">
        <div class="overview-header">
            <div class="overview-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                Database / Storage
            </div>
            <span style="color: var(--success); font-weight: 600; font-size: 12px;">Optimal</span>
        </div>
        <div class="metric-value">148<span style="font-size: 24px; color: var(--text-muted);">GB</span></div>
        <div class="progress-bar">
            <div class="progress-fill color-accent" style="width: 45%;"></div>
        </div>
        <div class="progress-label">
            <span>NVMe SSD (500GB)</span>
            <span>352 GB Free</span>
        </div>
        
        <div class="node-list">
            <div class="node-item">
                <span style="color: var(--text-muted);">MySQL Connections</span>
                <span style="color: var(--text-primary); font-weight: 600;">14 / 200</span>
            </div>
            <div class="node-item">
                <span style="color: var(--text-muted);">Redis Cache Hit Rate</span>
                <span style="color: var(--text-primary); font-weight: 600;">98.2%</span>
            </div>
        </div>
    </div>

    <!-- Network Traffic -->
    <div class="overview-card">
        <div class="overview-header">
            <div class="overview-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                Network Traffic (LoRa Nodes)
            </div>
            <span style="color: var(--success); font-weight: 600; font-size: 12px;">Active</span>
        </div>
        
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div>
                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 5px;">Inbound</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--success);">2.4 <span style="font-size: 14px; color: var(--text-muted);">MB/s</span></div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 5px;">Outbound</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--accent);">0.8 <span style="font-size: 14px; color: var(--text-muted);">MB/s</span></div>
            </div>
        </div>

        <div class="node-list">
            <div class="node-item">
                <span style="color: var(--text-muted);">Active MQTT Packets</span>
                <span style="color: var(--text-primary); font-weight: 600;">342 req/sec</span>
            </div>
            <div class="node-item">
                <span style="color: var(--text-muted);">Packet Loss</span>
                <span style="color: var(--success); font-weight: 600;">0.01%</span>
            </div>
            <div class="node-item">
                <span style="color: var(--text-muted);">Average Latency</span>
                <span style="color: var(--warning); font-weight: 600;">42 ms</span>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.superadmin')

@section('title', 'Connected Tools')
@section('subtitle', 'Integrations > LoRaWAN Gateway')

@section('content')
<div class="stats-grid">
    <div class="stat-card" style="border-top: 3px solid var(--accent);">
        <div class="title">Gateway Status <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value" style="color: var(--success);">ONLINE</div>
        <div class="trend" style="color: var(--success);">99.9% Uptime</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid var(--warning);">
        <div class="title">Active Nodes <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value">42</div>
        <div class="trend">Total Registered Nodes</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid var(--danger);">
        <div class="title">Failed Transmissions <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value" style="color: var(--danger);">0.05%</div>
        <div class="trend">In the last 24 hours</div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">LoRaWAN Configuration</div>
        <button style="background: var(--accent); color: var(--text-primary); border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">+ Add Node</button>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Device EUI</th>
                    <th>Vessel Name</th>
                    <th>Last Seen</th>
                    <th>RSSI</th>
                    <th>SNR</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color: var(--text-primary); font-family: monospace;">A840410000000001</td>
                    <td>KM. Bintang Laut 01</td>
                    <td>2 mins ago</td>
                    <td style="color: var(--warning);">-110 dBm</td>
                    <td>8.5 dB</td>
                    <td><span class="badge safe">Connected</span></td>
                    <td><button style="background: transparent; color: var(--accent); border: 1px solid var(--accent); padding: 4px 8px; border-radius: 4px; cursor: pointer;">Sync</button></td>
                </tr>
                <tr>
                    <td style="color: var(--text-primary); font-family: monospace;">A840410000000002</td>
                    <td>KM. Harapan Jaya</td>
                    <td>Just now</td>
                    <td style="color: var(--success);">-85 dBm</td>
                    <td>12.0 dB</td>
                    <td><span class="badge safe">Connected</span></td>
                    <td><button style="background: transparent; color: var(--accent); border: 1px solid var(--accent); padding: 4px 8px; border-radius: 4px; cursor: pointer;">Sync</button></td>
                </tr>
                <tr>
                    <td style="color: var(--text-primary); font-family: monospace;">A840410000000003</td>
                    <td>KM. Cahaya Pagi</td>
                    <td>5 hours ago</td>
                    <td style="color: var(--danger);">-135 dBm</td>
                    <td>-5.0 dB</td>
                    <td><span class="badge danger">Offline</span></td>
                    <td><button style="background: transparent; color: var(--accent); border: 1px solid var(--accent); padding: 4px 8px; border-radius: 4px; cursor: pointer;">Sync</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

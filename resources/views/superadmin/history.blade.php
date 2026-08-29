@extends('layouts.superadmin')

@section('title', 'Riwayat Event')
@section('subtitle', 'Log & Notifikasi Sistem Darurat')

@section('styles')
    <style>
        .log-panel-full {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-top: 3px solid var(--danger);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 150px);
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
        
        .table-container {
            flex: 1;
            overflow-y: auto;
        }
        .table-container::-webkit-scrollbar { width: 4px; }
        .table-container::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }

        .log-table { width: 100%; border-collapse: collapse; }
        .log-table th { 
            color: var(--text-muted); 
            font-size: 11px; 
            padding: 12px 10px; 
            text-transform: uppercase; 
            background: var(--bg-main); 
            position: sticky; 
            top: 0; 
            z-index: 10;
        }
        .log-table td { padding: 14px 10px; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .log-table tbody tr:hover { background: rgba(255,255,255,0.02); }
        
        /* Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .badge.warning { background: rgba(251, 191, 36, 0.1); color: var(--warning); border: 1px solid var(--warning); }
        .badge.danger { background: rgba(248, 113, 113, 0.1); color: var(--danger); border: 1px solid var(--danger); }
        .badge.info { background: rgba(59, 130, 246, 0.1); color: var(--accent); border: 1px solid var(--accent); }
        .badge.success { background: rgba(52, 211, 153, 0.1); color: var(--success); border: 1px solid var(--success); }
    </style>
@endsection

@section('content')
<div class="log-panel-full">
    <div class="log-panel-header">
        <div style="width: 12px; height: 12px; background: var(--danger); border-radius: 50%; box-shadow: 0 0 10px var(--danger);"></div>
        PUSAT INFORMASI DARURAT & LOG EVENT
    </div>
    
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="date" id="filterDate" style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 12px; border-radius: 6px; font-family: 'Outfit'; color-scheme: dark;">
        <select id="filterStatus" style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 12px; border-radius: 6px; font-family: 'Outfit';">
            <option value="">Semua Status</option>
            <option value="CRITICAL">CRITICAL</option>
            <option value="WARNING">WARNING</option>
            <option value="INFO">INFO</option>
        </select>
        <button onclick="fetchHistoryLogs()" style="background: var(--accent); color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">Filter Log</button>
    </div>

    <div class="table-container">
        <table class="log-table">
            <thead>
                <tr>
                    <th style="width: 15%; text-align: left;">Waktu (WITA)</th>
                    <th style="width: 20%; text-align: left;">ID Kapal / Node</th>
                    <th style="width: 50%; text-align: left;">Detail Peristiwa</th>
                    <th style="width: 15%; text-align: right;">Status</th>
                </tr>
            </thead>
            <tbody id="history-table-body">
                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">Memuat riwayat log...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetchHistoryLogs();
        setInterval(fetchHistoryLogs, 15000);
    });

    function fetchHistoryLogs() {
        const dateVal = document.getElementById('filterDate').value;
        const statusVal = document.getElementById('filterStatus').value;
        
        let url = new URL("{{ route('api.history.logs') }}", window.location.origin);
        if(dateVal) url.searchParams.append('date', dateVal);
        if(statusVal) url.searchParams.append('status', statusVal);

        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const tbody = document.getElementById('history-table-body');
                    tbody.innerHTML = '';
                    
                    if (response.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada riwayat darurat</td></tr>';
                        return;
                    }

                    response.data.forEach(log => {
                        const tr = document.createElement('tr');
                        
                        let logType = log.type || 'INFO';
                        
                        // Mapping custom types to UI levels
                        let level = 'INFO';
                        if (logType === 'Capsizing') level = 'CRITICAL';
                        else if (logType === 'Leak' || logType === 'Weather Warning') level = 'WARNING';
                        else level = logType.toUpperCase();
                        
                        let badgeClass = 'info';
                        let badgeColor = 'var(--accent)';
                        if(level === 'CRITICAL') { badgeClass = 'danger'; badgeColor = 'var(--danger)'; }
                        else if(level === 'WARNING') { badgeClass = 'warning'; badgeColor = 'var(--warning)'; }
                        else if(level === 'RECOVERED' || level === 'SUCCESS') { badgeClass = 'success'; badgeColor = 'var(--success)'; }
                        
                        let timeStr = '-';
                        if (log.timestamp) {
                            const date = new Date(log.timestamp * 1000);
                            timeStr = date.toLocaleString('id-ID', {
                                year: 'numeric', month: '2-digit', day: '2-digit',
                                hour: '2-digit', minute:'2-digit', second:'2-digit'
                            });
                        }
                        
                        tr.innerHTML = `
                            <td style="color: var(--text-muted);">${timeStr}</td>
                            <td style="font-weight: 600;">${log.node_id || '-'}</td>
                            <td>${log.message || '-'}</td>
                            <td style="text-align: right;">
                                <span class="badge ${badgeClass}"><span style="color:${badgeColor}">●</span> ${level}</span>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(console.error);
    }
</script>
@endsection

@extends('layouts.petugas')

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
        <input type="date" style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 12px; border-radius: 6px; font-family: 'Outfit'; color-scheme: dark;">
        <select style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 12px; border-radius: 6px; font-family: 'Outfit';">
            <option>Semua Status</option>
            <option>CRITICAL</option>
            <option>WARNING</option>
            <option>INFO</option>
        </select>
        <button style="background: var(--accent); color: var(--text-primary); border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">Filter Log</button>
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
            <tbody>
                <tr>
                    <td style="color: var(--text-muted);">Hari ini, 14:30</td>
                    <td style="font-weight: 600;">Salili Mandar</td>
                    <td>[OFFLINE] Node Salili Mandar tidak merespon ping selama 8 detik.</td>
                    <td style="text-align: right;"><span class="badge warning"><span style="color:var(--warning)">●</span> WARNING</span></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Hari ini, 13:15</td>
                    <td style="font-weight: 600;">Bintang Laut</td>
                    <td>[ROLL] Kemiringan kapal mencapai 65 derajat. Resiko terbalik.</td>
                    <td style="text-align: right;"><span class="badge danger"><span style="color:var(--danger)">●</span> CRITICAL</span></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Hari ini, 11:00</td>
                    <td style="font-weight: 600;">Harapan Jaya</td>
                    <td>[SYSTEM] Modul GPS kembali online. Koordinat terkalibrasi.</td>
                    <td style="text-align: right;"><span class="badge success"><span style="color:var(--success)">●</span> RECOVERED</span></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Kemarin, 22:10</td>
                    <td style="font-weight: 600;">Maju Jaya 99</td>
                    <td>[WEATHER] Cuaca memburuk di koordinat -3.582, 118.990. Angin 25 knot.</td>
                    <td style="text-align: right;"><span class="badge info"><span style="color:var(--accent)">●</span> INFO</span></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Kemarin, 19:45</td>
                    <td style="font-weight: 600;">Cahaya Ilahi</td>
                    <td>[LEAK] Sensor ultrasonik mendeteksi debit air 20cm di lambung.</td>
                    <td style="text-align: right;"><span class="badge warning"><span style="color:var(--warning)">●</span> WARNING</span></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted);">Kemarin, 15:30</td>
                    <td style="font-weight: 600;">Semua Kapal</td>
                    <td>[BROADCAST] Peringatan dini gelombang tinggi dari BMKG diteruskan.</td>
                    <td style="text-align: right;"><span class="badge info"><span style="color:var(--accent)">●</span> INFO</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

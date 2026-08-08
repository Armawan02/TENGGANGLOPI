@extends('layouts.superadmin')

@section('title', 'Healing Activity')
@section('subtitle', 'Log Resolusi Otomatis & Pemulihan Sistem')

@section('styles')
    <style>
        .timeline-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px;
        }
        
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .timeline::after {
            content: '';
            position: absolute;
            width: 2px;
            background-color: var(--border-color);
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -1px;
        }
        
        .container {
            padding: 10px 40px;
            position: relative;
            background-color: inherit;
            width: 50%;
        }
        
        .container.left { left: 0; }
        .container.right { left: 50%; }
        
        .container::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            right: -8px;
            background-color: var(--bg-main);
            border: 2px solid var(--accent);
            top: 15px;
            border-radius: 50%;
            z-index: 1;
        }
        .container.right::after { left: -8px; }
        
        /* Different types of healing actions */
        .container.resolved::after { border-color: var(--success); }
        .container.restarted::after { border-color: var(--warning); }
        .container.failed::after { border-color: var(--danger); }
        
        .content {
            padding: 20px 30px;
            background: var(--bg-main);
            position: relative;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .content h2 {
            font-size: 15px;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .content p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
        }
        
        .time-badge {
            display: inline-block;
            background: rgba(255,255,255,0.05);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 12px;
            border: 1px solid var(--border-color);
        }
        
        .bot-tag {
            font-size: 10px;
            background: rgba(59, 130, 246, 0.15);
            color: var(--accent);
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid rgba(59, 130, 246, 0.3);
            text-transform: uppercase;
            font-weight: 700;
        }

        @media screen and (max-width: 600px) {
            .timeline::after { left: 31px; }
            .container { width: 100%; padding-left: 70px; padding-right: 25px; }
            .container.right { left: 0; }
            .container::after { left: 23px; }
        }
    </style>
@endsection

@section('content')
<div class="timeline-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h3 style="color: var(--text-primary); font-size: 18px; margin-bottom: 10px;">Self-Healing AI Logs</h3>
        <p style="color: var(--text-muted); font-size: 13px;">Riwayat pemulihan sistem otomatis oleh TENGGANGLOPI Core</p>
    </div>

    <div class="timeline">
        <div class="container left resolved">
            <div class="content">
                <span class="time-badge">Hari ini, 15:42 WITA</span>
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Koneksi Node Dipulihkan
                </h2>
                <p>Node <strong>Bintang Laut (NODE_02)</strong> kehilangan ping selama 12 detik. Sistem <span class="bot-tag">Auto-Recover</span> memicu reset port TCP pada gateway LoRaWAN. Koneksi kembali stabil.</p>
            </div>
        </div>
        <div class="container right restarted">
            <div class="content">
                <span class="time-badge">Hari ini, 10:15 WITA</span>
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="2"><path d="M2.5 2v6h6M21.5 22v-6h-6"/><path d="M22 11.5A10 10 0 0 0 3.2 7.2M2 12.5a10 10 0 0 0 18.8 4.2"/></svg>
                    Kalibrasi Ulang Gyroscope
                </h2>
                <p>Mendeteksi anomali data kemiringan statis sebesar 15 derajat secara terus-menerus pada <strong>Salili Mandar (NODE_01)</strong>. Sistem <span class="bot-tag">Sensor-Sync</span> melakukan kalibrasi ulang gyroscope dari jarak jauh.</p>
            </div>
        </div>
        <div class="container left failed">
            <div class="content">
                <span class="time-badge">Kemarin, 23:50 WITA</span>
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Upaya Resolusi Gagal
                </h2>
                <p>Sensor kebocoran lambung pada <strong>Maju Jaya 99 (NODE_04)</strong> memberikan pembacaan berfluktuasi ekstrem. Upaya reset tegangan sensor oleh <span class="bot-tag">Auto-Recover</span> gagal mengatasi masalah. Eskalasi ke Peringatan Fisik (Hardware Failure).</p>
            </div>
        </div>
        <div class="container right resolved">
            <div class="content">
                <span class="time-badge">Kemarin, 14:20 WITA</span>
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Optimasi Database Memori
                </h2>
                <p>Penggunaan memori Redis mencapai 92%. Sistem <span class="bot-tag">Garbage-Collector</span> melakukan flushing log usang berusia &gt; 30 hari secara otomatis. Memori turun ke tingkat optimal (45%).</p>
            </div>
        </div>
    </div>
</div>
@endsection

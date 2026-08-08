@extends('layouts.superadmin')

@section('title', 'Data Nelayan')
@section('subtitle', 'Administration > Users & Devices')

@section('content')
<div class="stats-grid">
    <div class="stat-card" style="border-top: 3px solid var(--accent);">
        <div class="title">Total Nelayan Terdaftar <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value">124</div>
        <div class="trend" style="color: var(--success);">↗ 5 Baru bulan ini</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid var(--warning);">
        <div class="title">Perangkat Kapal <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value">42</div>
        <div class="trend">Aktif dari 50 Modul</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid var(--success);">
        <div class="title">Petugas SAR Aktif <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value">12</div>
        <div class="trend">Standby di Lapangan</div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Manajemen Data Nelayan</div>
        <button style="background: var(--accent); color: var(--text-primary); border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">+ Tambah Nelayan</button>
    </div>
    
    <div style="margin-bottom: 20px; display: flex; gap: 10px;">
        <input type="text" placeholder="Cari Nama Nelayan / ID Kapal..." style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 12px; border-radius: 6px; width: 300px; font-family: 'Outfit';">
        <button style="background: var(--bg-main); color: var(--text-muted); border: 1px solid var(--border-color); padding: 8px 12px; border-radius: 6px; cursor: pointer; font-family: 'Outfit';">Filter</button>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID Nelayan</th>
                    <th>Nama Nelayan</th>
                    <th>Kontak / Telp</th>
                    <th>Nama Kapal</th>
                    <th>ID Perangkat LoRa</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color: var(--text-primary); font-family: monospace;">NL-001</td>
                    <td style="font-weight: 600;">Pak Budi</td>
                    <td>081234567890</td>
                    <td>KM. Bintang Laut 01</td>
                    <td style="font-family: monospace; color: var(--text-muted);">A840410000000001</td>
                    <td><span class="badge safe">Terverifikasi</span></td>
                    <td>
                        <button style="background: transparent; color: var(--warning); border: 1px solid var(--warning); padding: 4px 8px; border-radius: 4px; cursor: pointer; margin-right: 4px;">Edit</button>
                        <button style="background: transparent; color: var(--danger); border: 1px solid var(--danger); padding: 4px 8px; border-radius: 4px; cursor: pointer;">Hapus</button>
                    </td>
                </tr>
                <tr>
                    <td style="color: var(--text-primary); font-family: monospace;">NL-002</td>
                    <td style="font-weight: 600;">Pak Anton</td>
                    <td>085399887766</td>
                    <td>KM. Harapan Jaya</td>
                    <td style="font-family: monospace; color: var(--text-muted);">A840410000000002</td>
                    <td><span class="badge safe">Terverifikasi</span></td>
                    <td>
                        <button style="background: transparent; color: var(--warning); border: 1px solid var(--warning); padding: 4px 8px; border-radius: 4px; cursor: pointer; margin-right: 4px;">Edit</button>
                        <button style="background: transparent; color: var(--danger); border: 1px solid var(--danger); padding: 4px 8px; border-radius: 4px; cursor: pointer;">Hapus</button>
                    </td>
                </tr>
                <tr>
                    <td style="color: var(--text-primary); font-family: monospace;">NL-003</td>
                    <td style="font-weight: 600;">Pak Rudi</td>
                    <td>082122334455</td>
                    <td>KM. Cahaya Pagi</td>
                    <td style="font-family: monospace; color: var(--text-muted);">A840410000000003</td>
                    <td><span class="badge safe">Terverifikasi</span></td>
                    <td>
                        <button style="background: transparent; color: var(--warning); border: 1px solid var(--warning); padding: 4px 8px; border-radius: 4px; cursor: pointer; margin-right: 4px;">Edit</button>
                        <button style="background: transparent; color: var(--danger); border: 1px solid var(--danger); padding: 4px 8px; border-radius: 4px; cursor: pointer;">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

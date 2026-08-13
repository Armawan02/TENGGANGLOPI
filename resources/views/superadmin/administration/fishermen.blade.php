@extends('layouts.superadmin')

@section('title', 'Data Nelayan')
@section('subtitle', 'Kelola data identitas nelayan dan kepemilikan kapal.')

@section('styles')
    <style>
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        th {
            background: rgba(0, 0, 0, 0.02);
            font-weight: 600;
            color: var(--text-muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            color: var(--text-primary);
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-edit { color: var(--accent); }
        .btn-edit:hover { background: rgba(37, 99, 235, 0.1); }
        
        .btn-delete { color: var(--danger); }
        .btn-delete:hover { background: rgba(239, 68, 68, 0.1); }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        
        .modal-content {
            background: var(--bg-card);
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--border-color);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            color: var(--text-primary);
        }
        
        .close-modal {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent);
        }
        
        .modal-footer {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-cancel {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-error { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
    </style>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="stats-grid">
    <div class="stat-card" style="border-top: 3px solid var(--accent);">
        <div class="title">Total Nelayan Terdaftar <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value">{{ count($fishermen) }}</div>
        <div class="trend" style="color: var(--success);">Terdata di Firestore</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid var(--warning);">
        <div class="title">Perangkat Kapal <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value">{{ $totalFleet ?? 0 }}</div>
        <div class="trend">Aktif dan Terdaftar</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid var(--success);">
        <div class="title">Petugas SAR Aktif <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
        <div class="value">{{ $totalPetugas ?? 0 }}</div>
        <div class="trend">Standby di Lapangan</div>
    </div>
</div>

<div class="header-actions">
    <div style="display: flex; align-items: center; gap: 10px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--text-primary)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <h2 style="font-size: 20px; color: var(--text-primary); font-weight: 700;">Data Nelayan</h2>
    </div>
    <button class="btn-primary" onclick="openModal('addModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Nelayan
    </button>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>No. HP</th>
                <th>Nama Kapal</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fishermen as $f)
                <tr>
                    <td style="font-weight: 600;">{{ $f['name'] ?? '-' }}</td>
                    <td>{{ $f['nik'] ?? '-' }}</td>
                    <td>{{ $f['phone'] ?? '-' }}</td>
                    <td>
                        @if(!empty($f['boat_name']))
                            <span style="background: rgba(37,99,235,0.1); color: var(--accent); padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ $f['boat_name'] }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $f['address'] ?? '-' }}</td>
                    <td>
                        <div class="action-buttons">
                            <!-- Edit Button -->
                            <button class="btn-icon btn-edit" onclick="openEditModal('{{ $f['id'] }}', '{{ $f['name'] ?? '' }}', '{{ $f['nik'] ?? '' }}', '{{ $f['phone'] ?? '' }}', '{{ $f['boat_name'] ?? '' }}', '{{ $f['address'] ?? '' }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                            </button>
                            <!-- Delete Form -->
                            <form action="{{ route('superadmin.administration.fishermen.destroy', $f['id']) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        Belum ada data nelayan. Silakan tambah data baru.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal" id="addModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Data Nelayan</h3>
            <button class="close-modal" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form action="{{ route('superadmin.administration.fishermen.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required placeholder="Masukkan nama...">
            </div>
            <div class="form-group">
                <label>NIK</label>
                <input type="number" name="nik" class="form-control" required placeholder="Masukkan NIK KTP...">
            </div>
            <div class="form-group">
                <label>Nomor Handphone</label>
                <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123456789">
            </div>
            <div class="form-group">
                <label>Nama Kapal (Jika Ada)</label>
                <input type="text" name="boat_name" class="form-control" placeholder="Contoh: Bintang Laut">
            </div>
            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Masukkan alamat..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Data Nelayan</h3>
            <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>NIK</label>
                <input type="number" name="nik" id="edit_nik" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nomor Handphone</label>
                <input type="text" name="phone" id="edit_phone" class="form-control">
            </div>
            <div class="form-group">
                <label>Nama Kapal (Jika Ada)</label>
                <input type="text" name="boat_name" id="edit_boat" class="form-control">
            </div>
            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="address" id="edit_address" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn-primary">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openEditModal(id, name, nik, phone, boat, address) {
        // Set Action URL
        document.getElementById('editForm').action = "/superadmin/administration/fishermen/" + id;
        
        // Populate Data
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('edit_boat').value = boat;
        document.getElementById('edit_address').value = address;
        
        openModal('editModal');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal active') {
            event.target.classList.remove('active');
        }
    }
</script>
@endsection

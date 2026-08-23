<?php $__env->startSection('title', 'Kelola Akun'); ?>
<?php $__env->startSection('subtitle', 'Kelola data administrator dan sistem (Tambah, Atur Peran, Cabut Akses).'); ?>

<?php $__env->startSection('styles'); ?>
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
        
        .accounts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        @media (max-width: 1024px) {
            .accounts-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .accounts-grid { grid-template-columns: 1fr; }
        }

        .account-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .account-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .account-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .account-info h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
            text-transform: capitalize;
        }
        .badge-role {
            display: inline-block;
            padding: 2px 8px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--accent);
            font-size: 10px;
            font-weight: 700;
            border-radius: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .account-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 13px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .account-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .btn-action {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
        }
        .btn-reset {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .row-actions {
            display: flex;
            gap: 10px;
        }
        .btn-edit {
            flex: 1;
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        .btn-revoke {
            flex: 1;
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

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
            max-height: 90vh;
            overflow-y: auto;
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="header-actions">
    <div style="display: flex; align-items: center; gap: 10px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--text-primary)" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="10" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <h2 style="font-size: 20px; color: var(--text-primary); font-weight: 700;">Kelola Akun</h2>
    </div>
    <button class="btn-primary" onclick="openModal('addModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Akun
    </button>
</div>

<div class="accounts-grid">
    <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="account-card">
        <div class="account-header">
            <div class="account-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($account['name'] ?? 'User')); ?>&background=E2E8F0&color=2563EB" alt="Avatar" style="width:100%;height:100%;border-radius:50%;">
            </div>
            <div class="account-info">
                <h3><?php echo e($account['name'] ?? 'Tanpa Nama'); ?></h3>
                <div style="display: flex; gap: 5px; align-items: center;">
                    <span class="badge-role"><?php echo e($account['role'] ?? 'USER'); ?></span>
                    <?php if(($account['status'] ?? 'active') === 'pending'): ?>
                        <span class="badge-role" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">PENDING</span>
                    <?php else: ?>
                        <span class="badge-role" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">ACTIVE</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="account-details">
            <div class="detail-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <?php echo e($account['email'] ?? '-'); ?>

            </div>
            <div class="detail-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <?php echo e($account['phone'] ?? '-'); ?>

            </div>
        </div>
        <div class="account-actions">
            <?php if(($account['status'] ?? 'active') === 'pending'): ?>
                <form action="<?php echo e(route('superadmin.administration.accounts.approve', $account['id'])); ?>" method="POST" style="width:100%; margin-bottom: 5px;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-action" style="background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Setujui Akun
                    </button>
                </form>
            <?php endif; ?>
            <button class="btn-action btn-reset" onclick="openResetModal('<?php echo e($account['id']); ?>', '<?php echo e($account['name'] ?? ''); ?>')">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"></path><circle cx="16.5" cy="7.5" r=".5"></circle></svg>
                Reset Kata Sandi
            </button>
            <div class="row-actions">
                <button class="btn-action btn-edit" onclick="openEditModal('<?php echo e($account['id']); ?>', '<?php echo e($account['name'] ?? ''); ?>', '<?php echo e($account['email'] ?? ''); ?>', '<?php echo e($account['role'] ?? ''); ?>', '<?php echo e($account['phone'] ?? ''); ?>')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                    Edit
                </button>
                <form action="<?php echo e(route('superadmin.administration.accounts.destroy', $account['id'])); ?>" method="POST" style="flex:1;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-action btn-revoke">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Cabut
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">
            Belum ada data akun yang terdaftar di Firestore.
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Akun -->
<div class="modal" id="addModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Akun Baru</h3>
            <button class="close-modal" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form action="<?php echo e(route('superadmin.administration.accounts.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required placeholder="Masukkan nama...">
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" class="form-control" required placeholder="contoh@domain.com">
            </div>
            <div class="form-group">
                <label>Kata Sandi Sementara</label>
                <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Peran (Role)</label>
                <select name="role" class="form-control" required>
                    <option value="petugas">Petugas Pangkalan</option>
                    <option value="superadmin">Superadmin</option>
                    <option value="user">User Biasa</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nomor HP</label>
                <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="btn-primary">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Profil (Tanpa Password) -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Profil Akun</h3>
            <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Peran (Role)</label>
                <select name="role" id="edit_role" class="form-control" required>
                    <option value="petugas">Petugas Pangkalan</option>
                    <option value="superadmin">Superadmin</option>
                    <option value="user">User Biasa</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nomor HP</label>
                <input type="text" name="phone" id="edit_phone" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn-primary">Perbarui Profil</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal" id="resetModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reset Kata Sandi</h3>
            <button class="close-modal" onclick="closeModal('resetModal')">&times;</button>
        </div>
        <form id="resetForm" method="POST">
            <?php echo csrf_field(); ?>
            <div style="margin-bottom: 20px; font-size: 14px; color: var(--text-muted);">
                Anda akan mereset sandi untuk: <strong id="reset_name_label" style="color:var(--text-primary);"></strong>
            </div>
            <div class="form-group">
                <label>Kata Sandi Baru</label>
                <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('resetModal')">Batal</button>
                <button type="submit" class="btn-primary" style="background: var(--warning);">Kirim Sandi Baru</button>
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

    function openEditModal(id, name, email, role, phone) {
        document.getElementById('editForm').action = "/superadmin/administration/accounts/" + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_phone').value = phone;
        openModal('editModal');
    }

    function openResetModal(id, name) {
        document.getElementById('resetForm').action = "/superadmin/administration/accounts/" + id + "/reset-password";
        document.getElementById('reset_name_label').innerText = name;
        openModal('resetModal');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UNSULBAR\PEMROGRAMAN\laragon\www\TENGGANGLOPI\resources\views/superadmin/administration/accounts.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html>
<head>
    <title>Akun Anda Telah Disetujui</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
        }
        .content {
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TENGGANGLOPI</h1>
        </div>
        
        <div class="content">
            <p>Halo, <strong><?php echo e($userName); ?></strong>!</p>
            
            <p>Selamat <strong><?php echo e($userName); ?></strong>, Anda berhasil menjadi petugas pemantauan dashboard <strong>TENGGANGLOPI</strong>!</p>
            
            <p>Pendaftaran akun Anda telah disetujui. Anda sekarang memiliki akses penuh untuk memonitor aktivitas perairan dan sistem keselamatan kapal.</p>
            
            <p>Silakan klik tombol di bawah ini untuk mengakses dashboard petugas sesuai dengan role akun Anda:</p>
            
            <div style="text-align: center;">
                <a href="<?php echo e($loginUrl); ?>" class="btn">Login Sekarang</a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; <?php echo e(date('Y')); ?> TENGGANGLOPI Command Center. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\UNSULBAR\PEMROGRAMAN\laragon\www\TENGGANGLOPI\resources\views/emails/account_approved.blade.php ENDPATH**/ ?>
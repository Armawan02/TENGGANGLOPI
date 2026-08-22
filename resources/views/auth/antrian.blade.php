<x-guest-layout>
    <div style="text-align: center; padding: 20px 10px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="2" style="margin-bottom: 20px;">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        
        <h2 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 10px;">Pendaftaran Berhasil!</h2>
        
        <p style="color: var(--text-muted); font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
            Akun Anda saat ini masuk dalam <strong>antrian persetujuan</strong>.<br>
            Mohon tunggu hingga Superadmin TENGGANGLOPI meninjau dan menyetujui pendaftaran Anda.
        </p>

        <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); padding: 15px; border-radius: 8px; margin-bottom: 30px;">
            <p style="color: var(--warning); font-size: 13.5px; font-weight: 500; margin: 0;">
                Kami akan mengirimkan notifikasi beserta tautan login ke alamat email Anda setelah akun disetujui.
            </p>
        </div>

        <a href="{{ url('/') }}" class="btn-primary" style="display: inline-block; text-decoration: none;">
            Kembali ke Beranda
        </a>
    </div>
</x-guest-layout>

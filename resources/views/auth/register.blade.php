<x-guest-layout>
    <x-slot name="title">Pendaftaran Akun Baru</x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe">
            @error('name')
                <small style="color: var(--danger); margin-top: 5px; display: block;">{{ $message }}</small>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="email@contoh.com">
            @error('email')
                <small style="color: var(--danger); margin-top: 5px; display: block;">{{ $message }}</small>
            @enderror
        </div>

        <!-- Role -->
        <div class="form-group">
            <label for="role">Peran (Role)</label>
            <select id="role" name="role" required style="width: 100%; padding: 14px 16px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 15px; color: var(--text-dark); transition: all 0.3s; outline: none; background-color: #f8fafc; cursor: pointer; appearance: auto;">
                <option value="" disabled selected>Pilih Peran Anda</option>
                <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Superadmin SAR</option>
            </select>
            @error('role')
                <small style="color: var(--danger); margin-top: 5px; display: block;">{{ $message }}</small>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
            @error('password')
                <small style="color: var(--danger); margin-top: 5px; display: block;">{{ $message }}</small>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi">
            @error('password_confirmation')
                <small style="color: var(--danger); margin-top: 5px; display: block;">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn-primary">
            Daftar Akun Baru
        </button>

        <div class="auth-links">
            Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </form>
</x-guest-layout>

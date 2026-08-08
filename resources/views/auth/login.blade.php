<x-guest-layout>
    <x-slot name="title">Login Akses Base Station</x-slot>

    <!-- Session Status -->
    @if (session('status'))
        <div class="session-alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="email@contoh.com">
            @error('email')
                <small style="color: var(--danger); margin-top: 5px; display: block;">{{ $message }}</small>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <small style="color: var(--danger); margin-top: 5px; display: block;">{{ $message }}</small>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="checkbox-group">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Ingat saya</label>
        </div>

        <button type="submit" class="btn-primary">
            Masuk ke Sistem
        </button>

        <div class="auth-links">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="display: block; margin-bottom: 15px; font-weight: 500;">
                    Lupa kata sandi?
                </a>
            @endif
            <div>
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>
        </div>
    </form>
</x-guest-layout>

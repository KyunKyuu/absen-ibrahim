<x-layouts.app title="Ganti Password">
    <section class="login-card" style="min-height:100dvh">
        <form class="panel stack" method="post" action="{{ route('password.update') }}">
            @csrf
            <h1>Ganti Password</h1>
            <p class="muted">Demi keamanan, ganti password awal sebelum menggunakan aplikasi.</p>
            @if($errors->any()) <div class="alert errors">{{ $errors->first() }}</div> @endif
            <label>Password saat ini <input name="current_password" type="password" required autocomplete="current-password"></label>
            <label>Password baru <input name="password" type="password" minlength="8" required autocomplete="new-password"></label>
            <label>Ulangi password baru <input name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></label>
            <button class="btn primary" type="submit">Simpan Password</button>
        </form>
    </section>
</x-layouts.app>

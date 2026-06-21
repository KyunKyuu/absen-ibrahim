<x-layouts.app title="Login Absensi Sekolah">
    <section class="login">
        <div class="login-hero">
            <div class="brand">Absensi<br>Sekolah</div>
            <div>
                <h1>Satu tempat untuk absen, sikap, poin, dan pantauan orang tua.</h1>
                <p class="muted">Akun dibuat oleh admin sekolah. Login awal admin: admin@sekolah.test / password123 setelah seeding.</p>
            </div>
        </div>
        <div class="login-card">
            <form class="panel stack" method="post" action="{{ route('login') }}">
                @csrf
                <h2>Masuk</h2>
                @if($errors->any()) <div class="alert errors">{{ $errors->first() }}</div> @endif
                <label>Email
                    <input name="email" type="email" value="{{ old('email') }}" required autofocus>
                </label>
                <label>Password
                    <input name="password" type="password" required>
                </label>
                <button class="btn primary" type="submit">Masuk</button>
            </form>
        </div>
    </section>
</x-layouts.app>

<x-layouts.app title="Kelola Akun">
    <div class="topbar">
        <div>
            <h1>Kelola Akun</h1>
            <p class="muted">Akun guru, siswa, dan orang tua dibuat oleh admin sekolah.</p>
        </div>
    </div>

    <div class="grid two">
        <section class="panel">
            <h2>Buat Akun</h2>
            <form class="stack" method="post" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="form-grid">
                    <label>Nama <input name="name" required></label>
                    <label>Email <input name="email" type="email" required></label>
                    <label>Password awal <input name="password" type="password" required minlength="8"></label>
                    <label>Role
                        <select name="role" required>
                            <option value="student">Siswa</option>
                            <option value="teacher">Guru</option>
                            <option value="parent">Orang Tua</option>
                            <option value="admin">Admin</option>
                        </select>
                    </label>
                    <label>Kelas siswa
                        <select name="school_class_id">
                            <option value="">-</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>NIS / Fingerprint ID <input name="nis"></label>
                    <label>NIP guru <input name="employee_number"></label>
                    <label>No. HP orang tua <input name="phone"></label>
                </div>
                <button class="btn primary" type="submit">Buat Akun</button>
            </form>
        </section>

        <section class="panel">
            <h2>Daftar Akun</h2>
            <table>
                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Kelas</th></tr></thead>
                <tbody>
                @foreach($users as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->email }}</td>
                        <td><span class="badge">{{ $account->role }}</span></td>
                        <td>{{ $account->studentProfile?->schoolClass?->name ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div style="margin-top:14px">{{ $users->links() }}</div>
        </section>
    </div>

    <section class="panel" style="margin-top:18px">
        <h2>Hubungkan Orang Tua ke Siswa</h2>
        <form class="stack" method="post" action="{{ route('admin.users.link-parent') }}">
            @csrf
            <div class="form-grid">
                <label>Orang tua
                    <select name="parent_user_id" required>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Siswa
                    <select name="student_user_id" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Relasi <input name="relationship" value="parent" required></label>
            </div>
            <button class="btn primary" type="submit">Hubungkan</button>
        </form>
    </section>
</x-layouts.app>

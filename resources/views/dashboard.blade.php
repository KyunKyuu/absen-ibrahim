<x-layouts.app title="Dashboard">
    <div class="topbar">
        <div>
            <h1>Dashboard {{ ucfirst($user->role) }}</h1>
            <p class="muted">Ringkasan absensi, poin, label, dan prioritas perhatian siswa.</p>
        </div>
        @if($user->canDo('reports.export'))
            <a class="btn" href="{{ route('reports.attitude') }}">Export Raport Sikap CSV</a>
        @endif
    </div>

    @if(in_array($user->role, ['student', 'parent'], true))
        <section class="panel progress-filter">
            <form method="get" action="{{ route('dashboard') }}">
                <label>Semester
                    <select name="semester" onchange="this.form.submit()">
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" @selected($selectedSemester?->id === $semester->id)>{{ $semester->name }} · {{ $semester->academicYear?->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Kelas
                    <select name="class" onchange="this.form.submit()">
                        <option value="">Semua kelas</option>
                        @foreach($profileClasses as $class)
                            <option value="{{ $class->id }}" @selected($selectedClassId === $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </label>
            </form>
        </section>
    @endif

    @if($user->role === 'student')
        <div class="student-dashboard">
            <section class="panel">
                <h2>Absensi Hari Ini</h2>
                @if($todayAttendance)
                    <div class="attendance-success">
                        <span class="status-icon">✓</span>
                        <div>
                            <strong>Sudah tercatat</strong>
                            <p class="muted">{{ substr($todayAttendance->checked_in_at, 0, 5) }} · {{ $todayAttendance->is_ontime ? 'Tepat waktu' : 'Terlambat' }} · {{ ucfirst($todayAttendance->source) }}</p>
                            @if($todayAttendance->distance_meters !== null)<small>Jarak terukur {{ $todayAttendance->distance_meters }} meter dari sekolah.</small>@endif
                        </div>
                    </div>
                @else
                    <form class="stack" method="post" action="{{ route('attendance.check-in') }}" id="attendance-form">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="accuracy" id="accuracy">
                        <p class="muted">Absensi diterima maksimal {{ $attendanceSetting->attendance_radius_meters }} meter dari titik sekolah. Aktifkan GPS dan izin lokasi browser.</p>
                        <p class="geo-status muted" id="geo-status" aria-live="polite"></p>
                        <button class="btn primary" type="button" id="geo-button">Ambil Lokasi & Absen</button>
                    </form>
                @endif
            </section>
            <x-student-progress :student="$user" :progress="$progress->get($user->id)" />
        </div>
        <script>
            document.getElementById('geo-button')?.addEventListener('click', function () {
                const button = this;
                const status = document.getElementById('geo-status');
                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    status.textContent = 'Lokasi browser hanya dapat digunakan melalui HTTPS.';
                    return;
                }
                if (!navigator.geolocation) {
                    status.textContent = 'Browser tidak mendukung pembacaan lokasi.';
                    return;
                }
                button.disabled = true;
                button.textContent = 'Membaca lokasi…';
                status.textContent = 'Tunggu hingga GPS mendapatkan lokasi yang akurat.';
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('accuracy').value = position.coords.accuracy;
                    status.textContent = `Lokasi ditemukan dengan akurasi ±${Math.ceil(position.coords.accuracy)} meter. Mengirim absensi…`;
                    document.getElementById('attendance-form').submit();
                }, function (error) {
                    const messages = {
                        1: 'Izin lokasi ditolak. Izinkan akses lokasi pada pengaturan browser.',
                        2: 'Lokasi tidak tersedia. Pastikan GPS aktif dan coba di area terbuka.',
                        3: 'Pembacaan lokasi terlalu lama. Silakan coba kembali.',
                    };
                    status.textContent = messages[error.code] ?? 'Lokasi tidak dapat dibaca.';
                    button.disabled = false;
                    button.textContent = 'Coba Ambil Lokasi Lagi';
                }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
            });
        </script>
    @elseif($user->role === 'parent')
        <div class="stack progress-list">
            @forelse($children as $child)
                <x-student-progress :student="$child" :progress="$progress->get($child->id)" />
            @empty
                <section class="panel muted">Belum ada data anak yang terhubung ke akun ini.</section>
            @endforelse
        </div>
    @else
        <div class="grid stats">
            <div class="metric"><span class="muted">Siswa</span><strong>{{ $stats['students'] }}</strong></div>
            <div class="metric"><span class="muted">Guru</span><strong>{{ $stats['teachers'] }}</strong></div>
            <div class="metric"><span class="muted">Orang tua</span><strong>{{ $stats['parents'] }}</strong></div>
            <div class="metric"><span class="muted">Absen hari ini</span><strong>{{ $stats['todayAttendance'] }}</strong></div>
            <div class="metric"><span class="muted">Prioritas</span><strong>{{ $stats['priority'] }}</strong></div>
        </div>

        <div class="grid two" style="margin-top:18px">
            <section class="panel">
                <h2>Leaderboard Poin</h2>
                <table>
                    <thead><tr><th>Nama</th><th>Kelas</th><th>Poin</th><th>Label</th></tr></thead>
                    <tbody>
                    @forelse($leaderboard as $row)
                        <tr>
                            <td>{{ $row->student?->name }}</td>
                            <td>{{ $row->student?->studentProfile?->schoolClass?->name }}</td>
                            <td>{{ $row->general_points }}</td>
                            <td><span class="badge {{ $row->general_points <= -50 ? 'priority' : '' }}">{{ $row->label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">Belum ada poin siswa.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </section>
            <section class="panel">
                <h2>Siswa Perlu Dipantau</h2>
                <table>
                    <thead><tr><th>Nama</th><th>Poin</th><th>Label</th></tr></thead>
                    <tbody>
                    @foreach($students->filter(fn($student) => ($student->pointSummary?->general_points ?? 0) < 0)->take(8) as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->pointSummary?->general_points ?? 0 }}</td>
                            <td><span class="badge priority">{{ $student->pointSummary?->label }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
        </div>
    @endif

</x-layouts.app>

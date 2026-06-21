<x-layouts.app title="Dashboard">
    <div class="topbar">
        <div>
            <h1>Dashboard {{ ucfirst($user->role) }}</h1>
            <p class="muted">Ringkasan absensi, poin, label, dan prioritas perhatian siswa.</p>
        </div>
        @if(in_array($user->role, ['admin', 'teacher'], true))
            <a class="btn" href="{{ route('reports.attitude') }}">Export Raport Sikap CSV</a>
        @endif
    </div>

    @if($user->role === 'student')
        <div class="grid two">
            <section class="panel">
                <h2>Absensi Hari Ini</h2>
                <form class="stack" method="post" action="{{ route('attendance.check-in') }}" id="attendance-form">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <p class="muted">Klik tombol di bawah saat berada di area sekolah.</p>
                    <button class="btn primary" type="button" id="geo-button">Ambil Lokasi & Absen</button>
                </form>
            </section>
            <section class="panel">
                @php($summary = $user->pointSummary)
                <h2>Poin Saya</h2>
                <table>
                    <tr><td>General</td><td>{{ $summary?->general_points ?? 0 }}</td></tr>
                    <tr><td>Sikap</td><td>{{ $summary?->attitude_points ?? 0 }}</td></tr>
                    <tr><td>Absen</td><td>{{ $summary?->attendance_points ?? 0 }}</td></tr>
                    <tr><td>Prestasi</td><td>{{ $summary?->achievement_points ?? 0 }}</td></tr>
                    <tr><td>Label</td><td><span class="badge {{ ($summary?->general_points ?? 0) <= -50 ? 'priority' : '' }}">{{ $summary?->label ?? 'Perlu Dipantau' }}</span></td></tr>
                </table>
            </section>
        </div>
        <script>
            document.getElementById('geo-button')?.addEventListener('click', function () {
                if (!navigator.geolocation) return alert('Browser tidak mendukung geolocation.');
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('attendance-form').submit();
                }, function () {
                    alert('Lokasi tidak bisa dibaca. Aktifkan izin lokasi browser.');
                }, { enableHighAccuracy: true, timeout: 12000 });
            });
        </script>
    @elseif($user->role === 'parent')
        <section class="panel">
            <h2>Anak Saya</h2>
            <table>
                <thead><tr><th>Nama</th><th>Kelas</th><th>General</th><th>Sikap</th><th>Absen</th><th>Prestasi</th><th>Label</th></tr></thead>
                @forelse($children as $child)
                    <tr>
                        <td>{{ $child->name }}</td>
                        <td>{{ $child->studentProfile?->schoolClass?->name }}</td>
                        <td>{{ $child->pointSummary?->general_points ?? 0 }}</td>
                        <td>{{ $child->pointSummary?->attitude_points ?? 0 }}</td>
                        <td>{{ $child->pointSummary?->attendance_points ?? 0 }}</td>
                        <td>{{ $child->pointSummary?->achievement_points ?? 0 }}</td>
                        <td><span class="badge {{ ($child->pointSummary?->general_points ?? 0) <= -50 ? 'priority' : '' }}">{{ $child->pointSummary?->label ?? 'Perlu Dipantau' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="muted">Belum ada data anak yang terhubung ke akun ini.</td></tr>
                @endforelse
            </table>
        </section>
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

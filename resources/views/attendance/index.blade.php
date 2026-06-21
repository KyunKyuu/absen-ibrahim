<x-layouts.app title="Rekap Absensi">
    <div class="topbar">
        <div>
            <h1>Rekap Absensi</h1>
            <p class="muted">Absensi dari web GPS dan fingerprint IoT tercatat dalam tabel yang sama.</p>
        </div>
        <a class="btn" href="{{ route('reports.attendance') }}">Export CSV</a>
    </div>

    <section class="panel">
        <table>
            <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Jam</th><th>Sumber</th><th>Jarak</th></tr></thead>
            <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->attendance_date?->format('d M Y') }}</td>
                    <td>{{ $attendance->student?->name }}</td>
                    <td>{{ $attendance->student?->studentProfile?->schoolClass?->name }}</td>
                    <td><span class="badge">{{ $attendance->status }}</span></td>
                    <td>{{ $attendance->checked_in_at }}</td>
                    <td>{{ $attendance->source }}</td>
                    <td>{{ $attendance->distance_meters ? $attendance->distance_meters.' m' : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada absensi.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:14px">{{ $attendances->links() }}</div>
    </section>
</x-layouts.app>

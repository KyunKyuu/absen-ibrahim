<x-layouts.app title="Rekap Absensi">
    <style>
        .filter-bar{display:grid;grid-template-columns:minmax(210px,1.5fr) repeat(3,minmax(130px,.65fr)) auto;gap:10px;align-items:end;margin-bottom:18px}.filter-dates{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px}
        @media(max-width:1000px){.filter-bar{grid-template-columns:repeat(2,minmax(0,1fr))}.filter-bar .btn{width:100%}}@media(max-width:560px){.filter-bar,.filter-dates{grid-template-columns:1fr}}
    </style>
    <div class="page-heading">
        <div class="page-heading-copy"><div class="breadcrumb"><span>Absensi</span><span>/</span><span>{{ $scope === 'today' ? 'Hari ini' : 'Semua data' }}</span></div><h1>{{ $scope === 'today' ? 'Absensi hari ini' : 'Rekap absensi' }}</h1><p class="muted">Cari dan saring catatan absensi dari GPS web maupun fingerprint IoT.</p></div>
        <a class="btn" href="{{ route('reports.attendance') }}">Export CSV</a>
    </div>
    <nav class="section-tabs" aria-label="Bagian absensi"><a class="{{ $scope === 'all' ? 'active' : '' }}" href="{{ route('attendance.index') }}">Semua absensi</a><a class="{{ $scope === 'today' ? 'active' : '' }}" href="{{ route('attendance.today') }}">Hari ini</a></nav>

    <section class="panel">
        <form method="get" action="{{ $scope === 'today' ? route('attendance.today') : route('attendance.index') }}">
            <div class="filter-bar">
                <label>Cari siswa<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Ketik nama siswa..."></label>
                <label>Kelas<select name="class"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(($filters['class'] ?? '') == $class->id)>{{ $class->name }}</option>@endforeach</select></label>
                <label>Sumber<select name="source"><option value="">Semua sumber</option><option value="web" @selected(($filters['source'] ?? '') === 'web')>GPS web</option><option value="iot" @selected(($filters['source'] ?? '') === 'iot')>Fingerprint IoT</option></select></label>
                <label>Status<select name="status"><option value="">Semua status</option><option value="present" @selected(($filters['status'] ?? '') === 'present')>Hadir</option><option value="late" @selected(($filters['status'] ?? '') === 'late')>Terlambat</option><option value="excused" @selected(($filters['status'] ?? '') === 'excused')>Izin</option><option value="absent" @selected(($filters['status'] ?? '') === 'absent')>Tidak hadir</option></select></label>
                <button class="btn primary" type="submit">Terapkan</button>
            </div>
            @if($scope === 'all')<div class="filter-dates"><label>Dari tanggal<input type="date" name="from" value="{{ $filters['from'] ?? '' }}"></label><label>Sampai tanggal<input type="date" name="to" value="{{ $filters['to'] ?? '' }}"></label></div>@endif
            @if(array_filter($filters))<a class="btn small" href="{{ $scope === 'today' ? route('attendance.today') : route('attendance.index') }}">Hapus filter</a>@endif
        </form>

        <div class="table-scroll" style="margin-top:18px"><table>
            <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Jam</th><th>Sumber</th><th>Jarak</th></tr></thead>
            <tbody>@forelse($attendances as $attendance)<tr>
                <td>{{ $attendance->attendance_date?->format('d M Y') }}</td><td><strong>{{ $attendance->student?->name }}</strong></td><td>{{ $attendance->student?->studentProfile?->schoolClass?->name ?? '-' }}</td>
                @php($displayStatus = $attendance->status === 'present' && ! $attendance->is_ontime ? 'Terlambat' : ucfirst($attendance->status))
                <td><span class="badge {{ $displayStatus === 'Terlambat' ? 'priority' : '' }}">{{ $displayStatus }}</span></td><td>{{ $attendance->checked_in_at }}</td><td>{{ $attendance->source === 'iot' ? 'IoT' : 'Web' }}</td><td>{{ $attendance->distance_meters !== null ? $attendance->distance_meters.' m' : '-' }}</td>
            </tr>@empty<tr><td colspan="7"><div class="empty-state">Tidak ada absensi yang cocok dengan filter.</div></td></tr>@endforelse</tbody>
        </table></div>
        <div style="margin-top:14px">{{ $attendances->links() }}</div>
    </section>
</x-layouts.app>

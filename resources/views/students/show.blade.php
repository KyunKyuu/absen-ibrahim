@php
    $summary = $student->pointSummary;
    $xp = (int) ($summary?->general_points ?? 0);
    $level = match (true) { $xp >= 100 => 'Teladan', $xp >= 50 => 'Berkembang', $xp >= 0 => 'Pemula', default => 'Mulai Bertumbuh' };
    $levelProgress = match (true) { $xp >= 100 => 100, $xp >= 50 => (int) round(($xp - 50) / 50 * 100), $xp >= 0 => (int) round($xp / 50 * 100), default => (int) round(max(0, min(1, ($xp + 50) / 50)) * 100) };
    $nextLevel = match (true) { $xp >= 100 => 'Maksimum', $xp >= 50 => 'Teladan', $xp >= 0 => 'Berkembang', default => 'Pemula' };
    $neededXp = match (true) { $xp >= 100 => 0, $xp >= 50 => 100 - $xp, $xp >= 0 => 50 - $xp, default => 0 - $xp };
@endphp
<x-layouts.app :title="$student->name">
    <style>
        .attendance-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:20px}
        @media(max-width:600px){.attendance-list{grid-template-columns:1fr}}
        .grade-subject{padding:14px 0;border-bottom:1px solid var(--line)}.grade-subject:last-child{border-bottom:0}.grade-subject h3{margin:0 0 10px;font-size:17px}.grade-kind{margin-top:10px;padding:10px 12px;border-radius:10px;background:#f4f8f5}.grade-kind-head{display:flex;justify-content:space-between;gap:10px;margin-bottom:5px}.grade-score{color:#176a4f;font-size:18px;font-weight:850;white-space:nowrap}
        .student-hero{position:relative;overflow:hidden;padding:26px;border-radius:16px;background:linear-gradient(125deg,#103b30,#176a4f 62%,#36a879);color:#fff;margin-bottom:18px}.student-hero:after{content:'✦';position:absolute;right:5%;top:-36px;font-size:180px;opacity:.1}.hero-top{position:relative;z-index:1;display:flex;justify-content:space-between;align-items:flex-start;gap:16px}.identity{display:flex;align-items:center;gap:16px}.avatar-xl{width:68px;height:68px;display:grid;place-items:center;border-radius:21px;background:#b9efd2;color:#12533b;font-size:25px;font-weight:900}.student-hero h1{margin:0;font-size:30px}.student-hero p{margin:6px 0 0;color:#d2e9de}.rank-pill{padding:10px 14px;border-radius:999px;background:#d4f5e5;color:#15533b;font-weight:850;white-space:nowrap}.xp-area{position:relative;z-index:1;max-width:560px;margin-top:24px}.xp-line{display:flex;justify-content:space-between;gap:10px;margin-bottom:8px;color:#e2f5eb;font-size:13px;font-weight:700}.xp-track{height:10px;border-radius:99px;background:#ffffff40;overflow:hidden}.xp-track span{display:block;height:100%;border-radius:99px;background:#c1f2d6}.detail-layout{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(280px,.75fr);gap:18px;align-items:start}.detail-stack{display:grid;gap:18px}.detail-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.detail-stat{padding:14px;border:1px solid var(--line);border-radius:11px;background:#fff}.detail-stat small,.detail-stat strong{display:block}.detail-stat small{color:var(--muted);font-size:12px}.detail-stat strong{font-size:22px;margin-top:5px}.profile-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.profile-item small,.profile-item strong{display:block}.profile-item small{color:var(--muted);margin-bottom:4px}.profile-item strong{overflow-wrap:anywhere}.activity-list{display:grid}.activity-row{display:flex;justify-content:space-between;gap:12px;padding:11px 0;border-bottom:1px solid var(--line)}.activity-row:last-child{border-bottom:0}.activity-row small{display:block;color:var(--muted);margin-top:3px}.gamify-callout{padding:14px;border-radius:11px;background:#edf7f2;color:#245b49}.detail-filters{display:flex;align-items:end;gap:10px;flex-wrap:wrap}.detail-filters label{min-width:240px}.mini-pagination{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:12px;padding-top:10px;border-top:1px solid var(--line);color:var(--muted);font-size:12px}.mini-pagination-links{display:flex;gap:5px}.mini-pagination a,.mini-pagination span{display:inline-flex;justify-content:center;align-items:center;min-width:30px;height:30px;padding:4px 8px;border:1px solid var(--line);border-radius:7px;color:inherit;text-decoration:none;background:#fff;font-weight:700}.mini-pagination .active{background:var(--accent);border-color:var(--accent);color:#fff}.mini-pagination .disabled{color:#aeb8b3;background:#f5f7f5}@media(max-width:850px){.detail-layout{grid-template-columns:1fr}.detail-stats{grid-template-columns:repeat(2,minmax(0,1fr)}}@media(max-width:540px){.hero-top,.identity{align-items:flex-start}.hero-top{flex-direction:column}.student-hero h1{font-size:24px}.profile-grid{grid-template-columns:1fr}.mini-pagination{align-items:flex-start;flex-direction:column}}
    </style>
    <div class="page-heading"><div class="page-heading-copy"><div class="breadcrumb"><a href="{{ route('students.index') }}">Siswa</a><span>/</span><span>Profil siswa</span></div><p class="muted" style="margin:0">Perjalanan belajar dan perkembangan siswa</p></div><a class="btn" href="{{ route('students.index') }}">← Daftar siswa</a></div>

    <section class="student-hero">
        <div class="hero-top"><div class="identity"><span class="avatar-xl">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($student->name, 0, 1)) }}</span><div><span class="eyebrow" style="color:#b8e8ce">{{ $student->studentProfile?->schoolClass?->name ?? 'Belum ada kelas' }} · {{ $student->studentProfile?->schoolClass?->academicYear?->name ?? 'Tahun ajaran belum diatur' }}</span><h1>{{ $student->name }}</h1><p>NIS {{ $student->studentProfile?->nis ?? '-' }}</p></div></div><span class="rank-pill">🏅 {{ $level }}</span></div>
        <div class="xp-area"><div class="xp-line"><span>{{ $xp }} XP · {{ $summary?->label ?? 'Perjalanan baru dimulai' }}</span><span>{{ $neededXp > 0 ? $neededXp.' XP lagi ke '.$nextLevel : 'Level maksimum tercapai' }}</span></div><div class="xp-track"><span style="width:{{ $levelProgress }}%"></span></div></div>
    </section>

    <section class="panel" style="margin-bottom:18px"><form class="detail-filters" method="get" action="{{ route('students.show', $student) }}">
        <label>Riwayat kelas<select name="class_id"><option value="">Semua kelas</option>@foreach($classOptions as $class)<option value="{{ $class->id }}" @selected($selectedClassId === $class->id)>{{ $class->name }} · {{ $class->academicYear?->name ?? 'Tahun ajaran tidak diketahui' }}</option>@endforeach</select><span class="field-help">Filter absensi, aktivitas, penilaian, prestasi, dan tagihan.</span></label>
        <label>Bulan absensi<select name="attendance_month"><option value="">Semua bulan</option>@foreach($attendanceMonths as $month)<option value="{{ $month }}" @selected($attendanceMonth === $month)>{{ \Carbon\CarbonImmutable::createFromFormat('!Y-m', $month)->locale('id')->translatedFormat('F Y') }}</option>@endforeach</select><span class="field-help">Pilih bulan untuk menyaring catatan kehadiran.</span></label>
        <button class="btn primary" type="submit">Terapkan filter</button>@if($selectedClassId || $attendanceMonth)<a class="btn" href="{{ route('students.show', $student) }}">Tampilkan semua</a>@endif
    </form></section>

    <div class="detail-layout">
        <div class="detail-stack">
            <div class="detail-stats">
                <div class="detail-stat"><small>✨ XP total</small><strong>{{ $xp }}</strong></div>
                <div class="detail-stat"><small>🤝 Poin sikap</small><strong>{{ $summary?->attitude_points ?? 0 }}</strong></div>
                <div class="detail-stat"><small>🏆 Prestasi</small><strong>{{ $summary?->achievement_points ?? 0 }}</strong></div>
                <div class="detail-stat"><small>📅 Kehadiran</small><strong>{{ $attendanceRate === null ? '—' : $attendanceRate.'%' }}</strong><small>{{ $attendanceCount }} catatan tercatat</small></div>
            </div>
            <section class="panel"><div class="topbar"><div><h2>Aktivitas terbaru</h2><span class="muted">XP dan perkembangan yang tercatat</span></div></div><div class="activity-list">
                @forelse($transactions as $transaction)<div class="activity-row"><span><strong>{{ $transaction->description }}</strong><small>{{ $transaction->created_at?->format('d M Y · H:i') }}</small></span><strong class="{{ $transaction->points < 0 ? 'priority' : '' }}">{{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }} XP</strong></div>
                @empty<div class="muted">Belum ada aktivitas poin.</div>@endforelse
            </div>@include('students.partials.pagination', ['paginator' => $transactions, 'label' => 'aktivitas'])</section>
            <section class="panel"><div class="topbar"><div><h2>Catatan kehadiran</h2><span class="muted">{{ $attendanceCount }} catatan · {{ $selectedClassId ? $classOptions->firstWhere('id', $selectedClassId)?->name : 'semua kelas' }}{{ $attendanceMonth ? ' · bulan '.\Carbon\CarbonImmutable::createFromFormat('!Y-m', $attendanceMonth)->locale('id')->translatedFormat('F Y') : '' }}</span></div></div><div class="attendance-list">
                @forelse($attendances as $attendance)<div class="activity-row"><span><strong>{{ $attendance->attendance_date?->format('d M Y') }}</strong><small>{{ $attendance->checked_in_at ? substr($attendance->checked_in_at,0,5).' · ' : '' }}{{ ucfirst($attendance->source) }}</small></span><span class="badge {{ $attendance->status === 'absent' ? 'priority' : '' }}">{{ ucfirst($attendance->status) }}</span></div>
                @empty<div class="muted">Belum ada catatan kehadiran.</div>@endforelse
            </div>@include('students.partials.pagination', ['paginator' => $attendances, 'label' => 'absensi'])</section>
            <section class="panel"><div class="topbar"><div><h2>Apresiasi & pembinaan</h2><span class="muted">Catatan dari guru</span></div></div><div class="activity-list">
                @foreach($achievementAssessments as $achievement)<div class="activity-row"><span><strong>🏆 {{ $achievement->title }}</strong><small>{{ $achievement->awarded_on?->format('d M Y') }} · {{ $achievement->subject?->name ?? 'Umum' }}</small>@if($achievement->notes)<small>{{ $achievement->notes }}</small>@endif</span><strong>+{{ $achievement->points }} XP</strong></div>@endforeach
                @foreach($attitudeAssessments as $assessment)<div class="activity-row"><span><strong>🌱 {{ $assessment->aspect }}</strong><small>{{ $assessment->assessed_on?->format('d M Y') }} · Skor {{ $assessment->score }}/5</small>@if($assessment->notes)<small>{{ $assessment->notes }}</small>@endif</span><strong>{{ $assessment->points > 0 ? '+' : '' }}{{ $assessment->points }} XP</strong></div>@endforeach
                @if($achievementAssessments->isEmpty() && $attitudeAssessments->isEmpty())<div class="muted">Belum ada catatan apresiasi atau pembinaan.</div>@endif
            </div>@include('students.partials.pagination', ['paginator' => $achievementAssessments, 'label' => 'prestasi'])@include('students.partials.pagination', ['paginator' => $attitudeAssessments, 'label' => 'penilaian sikap'])</section>
            <section class="panel"><div class="topbar"><div><h2>Nilai akademik</h2><span class="muted">Dikelompokkan berdasarkan mata pelajaran dan jenis penilaian</span></div></div>
                @php($gradesBySubject = $gradeAssessments->groupBy(fn ($assessment) => $assessment->subject?->name ?? 'Mata pelajaran'))
                @forelse($gradesBySubject as $subjectName => $subjectGrades)
                    <div class="grade-subject"><h3>{{ $subjectName }}</h3>
                        @foreach($subjectGrades->groupBy('kind') as $kind => $kindGrades)
                            <div class="grade-kind"><div class="grade-kind-head"><strong>{{ $gradeKinds[$kind] ?? ucfirst($kind) }}</strong><span class="muted">{{ $kindGrades->count() }} penilaian</span></div>
                                @foreach($kindGrades as $assessment)
                                    @php($grade = $assessment->grades->first())
                                    <div class="activity-row" style="padding:7px 0"><span><strong>{{ $assessment->title }}</strong><small>{{ $assessment->assessed_on?->format('d M Y') ?? '-' }} · {{ $assessment->semester?->name ?? '-' }} · {{ $assessment->semester?->academicYear?->name ?? '' }}</small>@if($grade?->notes)<small>{{ $grade->notes }}</small>@endif</span><span class="grade-score">{{ $grade?->score !== null ? rtrim(rtrim(number_format((float) $grade->score, 2, ',', '.'), '0'), ',') : '-' }}</span></div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="muted">Belum ada nilai akademik untuk siswa ini.</div>
                @endforelse
                @include('students.partials.pagination', ['paginator' => $gradeAssessments, 'label' => 'nilai akademik'])
            </section>
        </div>

        <aside class="detail-stack">
            <section class="panel"><h2>Profil siswa</h2><div class="profile-grid">
                <div class="profile-item"><small>Username</small><strong>{{ $student->username ?? '-' }}</strong></div><div class="profile-item"><small>Email</small><strong>{{ $student->email ?? '-' }}</strong></div>
                <div class="profile-item"><small>Tanggal lahir</small><strong>{{ $student->studentProfile?->birth_date?->format('d M Y') ?? '-' }}</strong></div><div class="profile-item"><small>Jenis kelamin</small><strong>{{ $student->studentProfile?->gender ?? '-' }}</strong></div>
                <div class="profile-item"><small>Status akun</small><strong>{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</strong></div><div class="profile-item"><small>Bergabung</small><strong>{{ $student->created_at?->format('d M Y') }}</strong></div>
            </div></section>
            <section class="panel"><h2>Perjalanan kelas</h2><div class="activity-list">
                @forelse($classHistories as $history)<div class="activity-row"><span><strong>{{ $history->schoolClass?->name ?? '-' }}</strong><small>{{ $history->academicYear?->name ?? '-' }}</small></span><small>{{ $history->started_on?->format('M Y') }} – {{ $history->ended_on?->format('M Y') ?? 'Sekarang' }}</small></div>
                @empty<div class="muted">Riwayat kelas belum dicatat.</div>@endforelse
            </div>@include('students.partials.pagination', ['paginator' => $classHistories, 'label' => 'riwayat kelas'])</section>
            @if($canManage)
                <section class="panel"><h2>Orang tua / wali</h2><div class="activity-list">
                    @forelse($parents as $parent)<div class="activity-row"><span><strong>{{ $parent->name }}</strong><small>{{ ucfirst($parent->relationship) }} · {{ $parent->email ?? 'Email belum diisi' }}</small></span><small>{{ $parent->phone ?? '-' }}</small></div>
                    @empty<div class="muted">Belum ada akun orang tua yang terhubung.</div>@endforelse
                </div></section>
                <section class="panel"><h2>Ringkasan tagihan</h2><div class="activity-list">
                    @forelse($bills as $bill)<div class="activity-row"><span><strong>{{ $bill->title }}</strong><small>Rp{{ number_format($bill->amount,0,',','.') }} · dibayar Rp{{ number_format($bill->paid_amount,0,',','.') }}</small></span><span class="badge {{ $bill->status === 'paid' ? '' : 'priority' }}">{{ $bill->status === 'paid' ? 'Lunas' : 'Belum lunas' }}</span></div>
                    @empty<div class="muted">Belum ada tagihan.</div>@endforelse
                </div>@include('students.partials.pagination', ['paginator' => $bills, 'label' => 'tagihan'])</section>
            @endif
            <div class="gamify-callout"><strong>🌟 Terus bertumbuh!</strong><br><span>Setiap kebiasaan baik, kehadiran, dan pencapaian ikut membangun perjalanan level siswa.</span></div>
        </aside>
    </div>
</x-layouts.app>

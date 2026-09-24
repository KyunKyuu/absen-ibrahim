<x-layouts.app title="Dashboard">
    <style>
        .dash-intro{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}.dash-intro h1{margin:0}.dash-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin:20px 0}.dash-metric{padding:16px;border:1px solid var(--line);border-radius:12px;background:#fff;box-shadow:var(--shadow)}.dash-metric span,.dash-metric strong{display:block}.dash-metric span{font-size:12px;color:var(--muted)}.dash-metric strong{font-size:27px;margin-top:6px}.dash-challenge{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:14px;align-items:center;padding:18px 20px;border-radius:14px;background:linear-gradient(110deg,#e5f6ee,#f4fbf7);border:1px solid #cee9da;margin-bottom:18px}.dash-challenge h2{margin:0 0 5px}.dash-challenge p{margin:0;color:var(--muted)}.challenge-progress{min-width:200px}.challenge-progress strong{display:block;text-align:right;margin-bottom:6px}.challenge-track{height:9px;border-radius:99px;background:#d2e6db;overflow:hidden}.challenge-track span{display:block;height:100%;background:#17815e;border-radius:99px}.dash-columns{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(280px,.8fr);gap:16px}.dash-columns .panel{min-width:0}.rank-chip{display:inline-grid;place-items:center;width:27px;height:27px;border-radius:50%;background:#e7f4ed;font-weight:850;color:#176548}.rank-chip.gold{background:#fff0c2;color:#8a6212}.leader-name{color:inherit;text-decoration:none;font-weight:750}.leader-name:hover{text-decoration:underline}.class-progress-list{display:grid;gap:11px}.class-progress-item{padding:12px;border-radius:10px;background:#f6f8f7}.class-progress-head{display:flex;justify-content:space-between;gap:10px;margin-bottom:8px}.class-progress-track{height:7px;border-radius:99px;background:#e2eae5;overflow:hidden}.class-progress-track span{height:100%;display:block;background:#55ad82;border-radius:99px}.attention-empty{padding:18px;border-radius:10px;background:#edf7f2;color:#245b49}.@media(max-width:1050px){.dash-stats{grid-template-columns:repeat(3,minmax(0,1fr))}.dash-columns{grid-template-columns:1fr}}@media(max-width:650px){.dash-intro{display:block}.dash-intro>.btn{margin-top:12px}.dash-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.dash-challenge{grid-template-columns:1fr}.challenge-progress{min-width:0}.challenge-progress strong{text-align:left}}
        .admin-podium-panel{margin-bottom:18px;overflow:hidden;color:#fff;background:radial-gradient(ellipse at 50% 110%,#347666,#123f36 70%)}.admin-podium-panel h2{color:#fff}.admin-podium-panel .muted{color:rgba(255,255,255,.7)}.admin-podium-filter{display:flex;align-items:end;gap:10px;flex-wrap:wrap;margin-bottom:12px}.admin-podium-filter label{min-width:210px;color:rgba(255,255,255,.8)}.admin-podium-filter select{color:#17201d;background:#fff}.admin-podium{min-height:320px;display:flex;align-items:end;justify-content:center;gap:14px;padding:20px 10px 0}.admin-podium-place{width:min(31%,250px);display:flex;flex-direction:column;align-items:center;text-align:center;animation:admin-podium-enter .65s both cubic-bezier(.2,.8,.2,1)}.admin-podium-place:nth-child(2){order:0;animation-delay:.12s}.admin-podium-place:nth-child(1){order:1}.admin-podium-place:nth-child(3){order:2;animation-delay:.24s}.admin-podium-avatar{width:68px;height:68px;display:grid;place-items:center;margin-bottom:12px;border:3px solid rgba(255,255,255,.8);border-radius:50%;color:#123f36;background:#f2eadb;font-size:25px;font-weight:800;box-shadow:0 10px 28px #0003}.admin-podium-place:first-child .admin-podium-avatar{width:88px;height:88px;color:#6e4812;background:linear-gradient(145deg,#fff1bc,#e6b844);border-color:#f4d77f;font-size:34px}.admin-podium-place strong{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.admin-podium-place small{margin:4px 0 9px;color:rgba(255,255,255,.65)}.admin-podium-points{margin-bottom:10px;color:#f4d77f;font-weight:800}.admin-podium-step{width:100%;display:grid;place-items:center;height:82px;border-radius:8px 8px 0 0;color:#123f36;background:linear-gradient(145deg,#e9eee1,#bbcdb9);font:600 42px Georgia,serif}.admin-podium-place:first-child .admin-podium-step{height:125px;background:linear-gradient(145deg,#ffe9a4,#d4a431)}.admin-podium-place:nth-child(3) .admin-podium-step{height:64px;background:linear-gradient(145deg,#e8c6a5,#a77850)}@keyframes admin-podium-enter{from{opacity:0;transform:translateY(25px)}to{opacity:1;transform:translateY(0)}}@media(prefers-reduced-motion:reduce){.admin-podium-place{animation:none}}@media(max-width:650px){.admin-podium{min-height:280px;gap:6px;padding-inline:0}.admin-podium-avatar{width:52px;height:52px;font-size:20px}.admin-podium-place:first-child .admin-podium-avatar{width:68px;height:68px;font-size:27px}.admin-podium-place strong{font-size:11px}.admin-podium-place small{font-size:9px}.admin-podium-step{height:68px;font-size:34px}.admin-podium-place:first-child .admin-podium-step{height:105px}.admin-podium-place:nth-child(3) .admin-podium-step{height:55px}}
    </style>
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
            <x-attendance-check-in :attendance="$todayAttendance" :setting="$attendanceSetting" />
            <x-student-progress :student="$user" :progress="$progress->get($user->id)" />
        </div>
    @elseif($user->role === 'parent')
        <div class="stack progress-list">
            @forelse($children as $child)
                <x-student-progress :student="$child" :progress="$progress->get($child->id)" />
            @empty
                <section class="panel muted">Belum ada data anak yang terhubung ke akun ini.</section>
            @endforelse
        </div>
    @else
        <div class="dash-stats">
            <div class="dash-metric"><span>👩‍🎓 Siswa aktif</span><strong>{{ $stats['students'] }}</strong></div>
            <div class="dash-metric"><span>🏫 Kelas berjalan</span><strong>{{ $classProgress->count() }}</strong></div>
            <div class="dash-metric"><span>✅ Check-in hari ini</span><strong>{{ $stats['todayAttendance'] }}</strong></div>
            <div class="dash-metric"><span>✨ XP terkumpul</span><strong>{{ number_format($stats['totalPoints']) }}</strong></div>
            <div class="dash-metric"><span>🔎 Perlu perhatian</span><strong>{{ $stats['priority'] }}</strong></div>
        </div>
        <section class="dash-challenge">
            <div><span class="eyebrow">MISI HARI INI</span><h2>Semua siswa sudah check-in?</h2><p>{{ $stats['todayAttendance'] }} dari {{ $stats['students'] }} siswa tercatat hadir hari ini. Konsistensi dimulai dari satu hari.</p></div>
            <div class="challenge-progress"><strong>{{ $stats['checkInRate'] }}%</strong><div class="challenge-track"><span style="width:{{ $stats['checkInRate'] }}%"></span></div></div>
        </section>

        @if($user->isRole('superadmin'))
            <section class="panel admin-podium-panel">
                <div class="topbar"><div><h2>🏆 Podium siswa rajin</h2><span class="muted">Peringkat berdasarkan poin periode kelas aktif</span></div></div>
                <form class="admin-podium-filter" method="get" action="{{ route('dashboard') }}">
                    <label>Lihat peringkat<select name="podium_class" onchange="this.form.submit()">
                        <option value="">Semua kelas</option>
                        @foreach($podiumClasses as $class)<option value="{{ $class->id }}" @selected($podiumClassId === $class->id)>{{ $class->name }}{{ $class->academicYear ? ' · '.$class->academicYear->name : '' }}</option>@endforeach
                    </select></label>
                </form>
                @if($podium->isNotEmpty())
                    <div class="admin-podium" aria-label="Podium siswa berdasarkan poin">
                        @foreach($podium->take(3) as $summary)
                            <article class="admin-podium-place">
                                <div class="admin-podium-avatar" aria-hidden="true">{{ mb_substr($summary->student?->name ?? '?', 0, 1) }}</div>
                                <strong>{{ $summary->student?->name }}</strong>
                                <small>{{ $summary->student?->studentProfile?->schoolClass?->name }}</small>
                                <span class="admin-podium-points">{{ number_format($summary->general_points) }} poin</span>
                                <div class="admin-podium-step" aria-label="Peringkat {{ $loop->iteration }}">{{ $loop->iteration }}</div>
                            </article>
                        @endforeach
                    </div>
                    @if($podium->count() > 3)<ol style="max-width:680px;margin:24px auto 0;padding:16px 24px 16px 46px;border:1px solid #ffffff2b;border-radius:12px;color:#ffffffc9">@foreach($podium->skip(3) as $summary)<li style="padding:7px 0">{{ $summary->student?->name }} · {{ $summary->student?->studentProfile?->schoolClass?->name }} <strong style="float:right;color:#f4d77f">{{ number_format($summary->general_points) }} poin</strong></li>@endforeach</ol>@endif
                @else
                    <div class="muted" style="padding:35px;text-align:center">Belum ada poin positif di kelas ini.</div>
                @endif
            </section>
        @endif

        <div class="dash-columns">
            <section class="panel">
                <div class="topbar"><div><h2>🏆 Papan apresiasi</h2><span class="muted">Siswa dengan XP terbanyak</span></div><a class="btn small" href="{{ route('students.index') }}">Lihat siswa</a></div>
                <table>
                    <thead><tr><th>#</th><th>Nama</th><th>Kelas</th><th>XP</th><th>Level</th></tr></thead>
                    <tbody>
                    @forelse($leaderboard as $row)
                        <tr>
                            <td><span class="rank-chip {{ $loop->first ? 'gold' : '' }}">{{ $loop->iteration }}</span></td>
                            <td><a class="leader-name" href="{{ $row->student ? route('students.show', $row->student) : '#' }}">{{ $row->student?->name }}</a><br><span class="muted">{{ $row->label }}</span></td>
                            <td>{{ $row->student?->studentProfile?->schoolClass?->name }}</td>
                            <td><strong>{{ $row->general_points }}</strong></td>
                            <td>{{ match (true) { $row->general_points >= 100 => 'Teladan', $row->general_points >= 50 => 'Berkembang', $row->general_points >= 0 => 'Pemula', default => 'Mulai Bertumbuh' } }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">Belum ada poin siswa.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </section>
            <div class="stack">
              <section class="panel">
                <div class="topbar"><div><h2>🎯 Pantauan siswa</h2><span class="muted">Siswa dengan poin di bawah nol</span></div></div>
                <table>
                    <thead><tr><th>Nama</th><th>Poin</th><th>Label</th></tr></thead>
                    <tbody>
                    @foreach($students->filter(fn($student) => ($student->pointSummary?->general_points ?? 0) < 0)->take(8) as $student)
                        <tr>
                            <td><a class="leader-name" href="{{ route('students.show', $student) }}">{{ $student->name }}</a></td>
                            <td>{{ $student->pointSummary?->general_points ?? 0 }}</td>
                            <td><span class="badge priority">{{ $student->pointSummary?->label }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if($students->filter(fn($student) => ($student->pointSummary?->general_points ?? 0) < 0)->isEmpty())<div class="attention-empty">🌱 Belum ada siswa dengan poin negatif. Pertahankan dukungan dan apresiasi positif!</div>@endif
              </section>
              <section class="panel"><div class="topbar"><div><h2>📊 Perkembangan kelas</h2><span class="muted">Rata-rata XP per siswa</span></div></div><div class="class-progress-list">
                @forelse($classProgress->take(6) as $class)<div class="class-progress-item"><div class="class-progress-head"><strong>{{ $class->name }}</strong><span class="muted">{{ $class->active_students }} siswa · {{ $class->average_points }} XP rata-rata</span></div><div class="class-progress-track"><span style="width:{{ min(100,max(0,$class->average_points)) }}%"></span></div></div>@empty<div class="muted">Belum ada data kelas.</div>@endforelse
              </div></section>
            </div>
        </div>
    @endif

</x-layouts.app>

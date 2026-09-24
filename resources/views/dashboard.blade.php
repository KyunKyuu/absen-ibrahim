<x-layouts.app title="Dashboard">
    <style>
        .dash-intro{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}.dash-intro h1{margin:0}
        .dash-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin:20px 0}
        .dash-metric{padding:18px;border:1px solid rgba(22,101,72,0.12);border-radius:14px;background:#fff;box-shadow:0 2px 8px -2px rgba(16,35,28,0.06), 0 1px 3px rgba(16,35,28,0.04);transition:transform .2s ease,box-shadow .2s ease}
        .dash-metric:hover{transform:translateY(-2px);box-shadow:0 8px 20px -4px rgba(16,35,28,0.1)}
        .dash-metric span{display:block;font-size:12px;font-weight:600;color:var(--muted)}
        .dash-metric strong{display:block;font-size:28px;font-weight:800;color:#17221e;margin-top:6px;letter-spacing:-0.03em}

        .dash-challenge{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:20px;align-items:center;padding:20px 24px;border-radius:18px;background:linear-gradient(135deg,#f0fdf4 0%,#e6f7ef 45%,#effaf5 100%);border:1px solid #bbf7d0;box-shadow:0 4px 20px -4px rgba(16,185,129,0.12),inset 0 1px 0 rgba(255,255,255,0.8);margin-bottom:22px;position:relative;overflow:hidden}
        .dash-challenge::before{content:'';position:absolute;right:-50px;top:-50px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(16,185,129,0.12) 0%,transparent 70%);pointer-events:none}
        .dash-challenge-icon{width:50px;height:50px;border-radius:14px;display:grid;place-items:center;font-size:24px;background:#fff;border:1px solid #d1fae5;box-shadow:0 4px 12px rgba(16,185,129,0.15);flex-shrink:0}
        .dash-challenge-tag{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:99px;background:#dcfce7;border:1px solid #86efac;color:#15803d;font-size:10.5px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:6px}
        .pulse-indicator{width:7px;height:7px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 0 rgba(34,197,94,0.7);animation:pulse-ring 2s infinite cubic-bezier(0.4, 0, 0.6, 1)}
        @keyframes pulse-ring{0%{box-shadow:0 0 0 0 rgba(34,197,94,0.7)}70%{box-shadow:0 0 0 6px rgba(34,197,94,0)}100%{box-shadow:0 0 0 0 rgba(34,197,94,0)}}
        .dash-challenge h2{margin:0 0 5px;font-size:18px;font-weight:800;color:#14532d;letter-spacing:-0.015em}
        .dash-challenge p{margin:0;color:#374151;font-size:13.5px;line-height:1.5}
        .stat-highlight{font-weight:800;color:#15803d}
        .challenge-motto{color:#6b7280;font-size:12.5px;margin-left:4px;font-style:italic}
        .challenge-progress{min-width:210px;background:#fff;padding:12px 16px;border-radius:12px;border:1px solid #d1fae5;box-shadow:0 2px 6px rgba(16,185,129,0.06)}
        .challenge-progress-head{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px}
        .challenge-progress strong{font-size:22px;font-weight:900;color:#166534;line-height:1}
        .challenge-progress-ratio{font-size:11.5px;font-weight:700;color:#15803d;background:#f0fdf4;padding:2px 8px;border-radius:99px}
        .challenge-track{height:9px;border-radius:99px;background:#e5e7eb;overflow:hidden;position:relative}
        .challenge-track span{display:block;height:100%;background:linear-gradient(90deg,#10b981 0%,#059669 100%);border-radius:99px;transition:width .6s ease;box-shadow:0 0 8px rgba(16,185,129,0.4)}

        .admin-podium-panel{margin-bottom:22px;padding:28px 24px 34px;border-radius:20px;border:1px solid rgba(255,255,255,0.12);color:#fff;background:radial-gradient(110% 120% at 50% 0%,#1a4d3f 0%,#0d2e24 45%,#071914 100%);box-shadow:0 20px 45px -15px rgba(5,20,16,0.5),inset 0 1px 0 rgba(255,255,255,0.18);position:relative;overflow:hidden}
        .admin-podium-panel::before{content:'';position:absolute;top:-25%;left:50%;transform:translateX(-50%);width:600px;height:320px;background:radial-gradient(ellipse at center,rgba(234,179,8,0.16) 0%,rgba(16,185,129,0.08) 50%,transparent 70%);pointer-events:none}
        .podium-header{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:22px;position:relative;z-index:2}
        .podium-eyebrow{display:inline-flex;align-items:center;gap:6px;padding:4px 11px;border-radius:99px;background:rgba(253,224,71,0.15);border:1px solid rgba(253,224,71,0.35);color:#fef08a;font-size:10.5px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:6px}
        .admin-podium-panel h2{color:#fff;font-size:22px;font-weight:800;margin:0 0 4px;letter-spacing:-0.02em}
        .admin-podium-panel .muted{color:rgba(255,255,255,0.72);font-size:13px}
        .admin-podium-filter{display:flex;align-items:center}
        .admin-podium-filter label{font-size:11px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;color:rgba(255,255,255,0.85);display:flex;flex-direction:column;gap:6px}
        .podium-select-wrap{position:relative;min-width:220px}
        .podium-select-wrap select{appearance:none;width:100%;color:#fff;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.22);border-radius:11px;padding:9px 36px 9px 14px;font-weight:650;font-size:13px;cursor:pointer;outline:none;transition:all .2s ease}
        .podium-select-wrap select:hover{background:rgba(255,255,255,0.16);border-color:rgba(255,255,255,0.4)}
        .podium-select-wrap select:focus{border-color:#fde047;box-shadow:0 0 0 3px rgba(253,224,71,0.25)}
        .podium-select-wrap select option{background:#0a231b;color:#fff}
        .podium-select-arrow{position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;color:rgba(255,255,255,0.6);font-size:11px}

        .admin-podium{min-height:360px;display:flex;align-items:flex-end;justify-content:center;gap:16px;padding:36px 10px 0;position:relative;z-index:2}
        .admin-podium-place{width:min(31%,240px);display:flex;flex-direction:column;align-items:center;text-align:center;position:relative;animation:admin-podium-enter .7s both cubic-bezier(.2,.8,.2,1);transition:transform .3s ease}
        .admin-podium-place:nth-child(2){order:0;animation-delay:.12s}
        .admin-podium-place:nth-child(1){order:1;z-index:3}
        .admin-podium-place:nth-child(3){order:2;animation-delay:.24s}
        .admin-podium-place:hover{transform:translateY(-4px)}

        .podium-avatar-wrap{position:relative;margin-bottom:14px}
        .crown-float{position:absolute;top:-28px;left:50%;transform:translateX(-50%);font-size:30px;filter:drop-shadow(0 4px 10px rgba(0,0,0,0.45));animation:crown-sway 2.6s ease-in-out infinite;z-index:4}
        @keyframes crown-sway{0%,100%{transform:translateX(-50%) translateY(0) rotate(0deg)}50%{transform:translateX(-50%) translateY(-5px) rotate(2deg)}}

        .admin-podium-avatar{width:72px;height:72px;display:grid;place-items:center;border-radius:50%;font-size:26px;font-weight:900;position:relative;transition:transform .3s ease}
        .admin-podium-place:hover .admin-podium-avatar{transform:scale(1.05)}
        .admin-podium-place:first-child .admin-podium-avatar{width:94px;height:94px;font-size:36px;color:#5c3b06;background:linear-gradient(135deg,#fffbeb 0%,#fde047 30%,#f59e0b 70%,#d97706 100%);border:4px solid #fef08a;box-shadow:0 0 35px rgba(245,158,11,0.55),0 10px 24px rgba(0,0,0,0.35),inset 0 2px 6px rgba(255,255,255,0.85)}
        .admin-podium-place:nth-child(2) .admin-podium-avatar{color:#1e293b;background:linear-gradient(135deg,#ffffff 0%,#e2e8f0 40%,#94a3b8 100%);border:3.5px solid #f8fafc;box-shadow:0 0 22px rgba(203,213,225,0.4),0 8px 18px rgba(0,0,0,0.3),inset 0 2px 4px rgba(255,255,255,0.9)}
        .admin-podium-place:nth-child(3) .admin-podium-avatar{color:#451a03;background:linear-gradient(135deg,#ffedd5 0%,#fb923c 40%,#c2410c 100%);border:3.5px solid #fed7aa;box-shadow:0 0 22px rgba(234,88,12,0.35),0 8px 18px rgba(0,0,0,0.3),inset 0 2px 4px rgba(255,255,255,0.8)}

        .avatar-badge-chip{position:absolute;bottom:-6px;right:-6px;width:26px;height:26px;border-radius:50%;display:grid;place-items:center;font-size:12px;font-weight:900;border:2px solid #0d2e24;box-shadow:0 2px 6px rgba(0,0,0,0.3)}
        .avatar-badge-chip.gold{background:linear-gradient(135deg,#fef08a,#f59e0b);color:#5c3b06;width:30px;height:30px;font-size:13px;bottom:-8px;right:-8px}
        .avatar-badge-chip.silver{background:linear-gradient(135deg,#f8fafc,#94a3b8);color:#0f172a}
        .avatar-badge-chip.bronze{background:linear-gradient(135deg,#ffedd5,#ea580c);color:#fff}

        .podium-student-name{font-size:15px;font-weight:800;color:#fff;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-shadow:0 1px 3px rgba(0,0,0,0.6);margin-bottom:3px}
        .admin-podium-place:first-child .podium-student-name{font-size:17px}
        .podium-student-class{display:inline-block;padding:2px 9px;border-radius:99px;background:rgba(255,255,255,0.12);backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.18);color:rgba(255,255,255,0.85);font-size:11px;font-weight:700;margin-bottom:8px}

        .admin-podium-points{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:99px;font-weight:800;font-size:12px;margin-bottom:12px;transition:transform .2s ease}
        .admin-podium-place:first-child .admin-podium-points{background:linear-gradient(135deg,rgba(254,240,138,0.22) 0%,rgba(245,158,11,0.2) 100%);border:1px solid rgba(250,204,21,0.6);color:#fef08a;font-size:13.5px;padding:5px 14px;box-shadow:0 0 16px rgba(245,158,11,0.25)}
        .admin-podium-place:nth-child(2) .admin-podium-points{background:rgba(241,245,249,0.14);border:1px solid rgba(203,213,225,0.45);color:#f1f5f9}
        .admin-podium-place:nth-child(3) .admin-podium-points{background:rgba(254,215,170,0.14);border:1px solid rgba(251,146,60,0.45);color:#fed7aa}

        .admin-podium-step{width:100%;display:grid;place-items:center;border-radius:14px 14px 0 0;position:relative;box-shadow:0 12px 28px rgba(0,0,0,0.35)}
        .admin-podium-step::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:rgba(255,255,255,0.65);border-radius:14px 14px 0 0}
        .admin-podium-step-num{font-weight:900;line-height:1;letter-spacing:-0.03em}

        .admin-podium-place:first-child .admin-podium-step{height:140px;background:linear-gradient(180deg,#fcd34d 0%,#f59e0b 35%,#b45309 85%,#78350f 100%);box-shadow:inset 0 2px 0 rgba(255,255,255,0.6),0 -8px 25px rgba(245,158,11,0.3),0 14px 30px rgba(0,0,0,0.4);border-top:1px solid rgba(255,255,255,0.7)}
        .admin-podium-place:first-child .admin-podium-step-num{font-size:54px;color:#ffffff;text-shadow:0 3px 12px rgba(120,53,15,0.75)}

        .admin-podium-place:nth-child(2) .admin-podium-step{height:98px;background:linear-gradient(180deg,#f8fafc 0%,#cbd5e1 35%,#64748b 85%,#334155 100%);box-shadow:inset 0 2px 0 rgba(255,255,255,0.7),0 10px 22px rgba(0,0,0,0.3);border-top:1px solid rgba(255,255,255,0.8)}
        .admin-podium-place:nth-child(2) .admin-podium-step-num{font-size:44px;color:#ffffff;text-shadow:0 2px 8px rgba(51,65,85,0.6)}

        .admin-podium-place:nth-child(3) .admin-podium-step{height:72px;background:linear-gradient(180deg,#fed7aa 0%,#ea580c 35%,#9a3412 85%,#431407 100%);box-shadow:inset 0 2px 0 rgba(255,255,255,0.55),0 10px 22px rgba(0,0,0,0.3);border-top:1px solid rgba(255,255,255,0.7)}
        .admin-podium-place:nth-child(3) .admin-podium-step-num{font-size:38px;color:#ffffff;text-shadow:0 2px 8px rgba(67,20,7,0.6)}

        .podium-stage-base{width:100%;height:8px;border-radius:4px;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,0.18) 20%,rgba(255,255,255,0.35) 50%,rgba(255,255,255,0.18) 80%,transparent 100%);margin-top:-2px;position:relative;z-index:1}

        .podium-runners-card{max-width:760px;margin:28px auto 0;padding:20px 22px;border-radius:16px;background:rgba(0,0,0,0.25);backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,0.1);box-shadow:0 8px 30px rgba(0,0,0,0.2);position:relative;z-index:2}
        .podium-runners-head{display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.08)}
        .podium-runners-title{font-size:13px;font-weight:800;color:rgba(255,255,255,0.9);display:flex;align-items:center;gap:8px;letter-spacing:0.02em}
        .podium-runners-badge{font-size:11px;font-weight:700;color:#fde047;background:rgba(253,224,71,0.12);border:1px solid rgba(253,224,71,0.25);padding:2px 8px;border-radius:99px}
        .podium-runners-list{display:grid;gap:7px}
        .podium-runner-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:9px 14px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);transition:background .18s ease,transform .18s ease,border-color .18s ease}
        .podium-runner-row:hover{background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.15);transform:translateX(3px)}
        .runner-left{display:flex;align-items:center;gap:12px;min-width:0}
        .runner-rank-num{width:24px;height:24px;border-radius:6px;display:grid;place-items:center;background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.8);font-size:11px;font-weight:800;flex-shrink:0}
        .runner-avatar-mini{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#10b981,#047857);color:#fff;font-size:13px;font-weight:800;flex-shrink:0;border:1.5px solid rgba(255,255,255,0.3)}
        .runner-meta{min-width:0;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
        .runner-name{color:#fff;font-size:13.5px;font-weight:750;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .runner-class{display:inline-block;padding:1px 7px;border-radius:99px;background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.7);font-size:10.5px;font-weight:600}
        .runner-points{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;background:rgba(253,224,71,0.1);border:1px solid rgba(253,224,71,0.2);color:#fef08a;font-weight:800;font-size:12.5px;flex-shrink:0}

        @keyframes admin-podium-enter{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
        @media(prefers-reduced-motion:reduce){.admin-podium-place,.crown-float{animation:none}}
        @media(max-width:650px){
            .dash-challenge{grid-template-columns:1fr;gap:14px;padding:18px}
            .challenge-progress{min-width:0}
            .admin-podium{min-height:290px;gap:8px;padding-inline:0}
            .admin-podium-avatar{width:56px;height:56px;font-size:20px}
            .admin-podium-place:first-child .admin-podium-avatar{width:74px;height:74px;font-size:28px}
            .crown-float{top:-22px;font-size:24px}
            .podium-student-name{font-size:12px}
            .admin-podium-place:first-child .podium-student-name{font-size:13px}
            .podium-student-class{font-size:9.5px;padding:1px 7px}
            .admin-podium-points{font-size:10.5px;padding:3px 8px}
            .admin-podium-place:first-child .admin-podium-points{font-size:11.5px;padding:4px 10px}
            .admin-podium-step{height:65px}
            .admin-podium-place:first-child .admin-podium-step{height:110px}
            .admin-podium-place:nth-child(3) .admin-podium-step{height:52px}
            .admin-podium-place:first-child .admin-podium-step-num{font-size:42px}
            .admin-podium-place:nth-child(2) .admin-podium-step-num{font-size:34px}
            .admin-podium-place:nth-child(3) .admin-podium-step-num{font-size:30px}
            .podium-runners-card{padding:14px}
            .runner-meta{flex-direction:column;align-items:flex-start;gap:2px}
        }
        .dash-columns{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(280px,.8fr);gap:16px}.dash-columns .panel{min-width:0}.rank-chip{display:inline-grid;place-items:center;width:27px;height:27px;border-radius:50%;background:#e7f4ed;font-weight:850;color:#176548}.rank-chip.gold{background:#fff0c2;color:#8a6212}.leader-name{color:inherit;text-decoration:none;font-weight:750}.leader-name:hover{text-decoration:underline}.class-progress-list{display:grid;gap:11px}.class-progress-item{padding:12px;border-radius:10px;background:#f6f8f7}.class-progress-head{display:flex;justify-content:space-between;gap:10px;margin-bottom:8px}.class-progress-track{height:7px;border-radius:99px;background:#e2eae5;overflow:hidden}.class-progress-track span{height:100%;display:block;background:#55ad82;border-radius:99px}.attention-empty{padding:18px;border-radius:10px;background:#edf7f2;color:#245b49}.@media(max-width:1050px){.dash-stats{grid-template-columns:repeat(3,minmax(0,1fr))}.dash-columns{grid-template-columns:1fr}}@media(max-width:650px){.dash-intro{display:block}.dash-intro>.btn{margin-top:12px}.dash-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
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
            <div class="dash-challenge-icon" aria-hidden="true">🎯</div>
            <div>
                <span class="dash-challenge-tag"><span class="pulse-indicator"></span> MISI HARI INI</span>
                <h2>Semua siswa sudah check-in?</h2>
                <p><strong class="stat-highlight">{{ $stats['todayAttendance'] }}</strong> dari <strong class="stat-highlight">{{ $stats['students'] }}</strong> siswa tercatat hadir hari ini. <span class="challenge-motto">Konsistensi dimulai dari satu hari.</span></p>
            </div>
            <div class="challenge-progress">
                <div class="challenge-progress-head">
                    <strong>{{ $stats['checkInRate'] }}%</strong>
                    <span class="challenge-progress-ratio">{{ $stats['todayAttendance'] }}/{{ $stats['students'] }} Siswa</span>
                </div>
                <div class="challenge-track"><span style="width:{{ $stats['checkInRate'] }}%"></span></div>
            </div>
        </section>

        @if($user->isRole('superadmin'))
            <section class="panel admin-podium-panel">
                <div class="podium-header">
                    <div>
                        <span class="podium-eyebrow">✨ Leaderboard Siswa</span>
                        <h2>🏆 Podium siswa rajin</h2>
                        <span class="muted">Peringkat berdasarkan poin periode kelas aktif</span>
                    </div>
                    <form class="admin-podium-filter" method="get" action="{{ route('dashboard') }}">
                        <label>
                            Lihat peringkat
                            <div class="podium-select-wrap">
                                <select name="podium_class" onchange="this.form.submit()">
                                    <option value="">Semua kelas</option>
                                    @foreach($podiumClasses as $class)
                                        <option value="{{ $class->id }}" @selected($podiumClassId === $class->id)>
                                            {{ $class->name }}{{ $class->academicYear ? ' · '.$class->academicYear->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="podium-select-arrow" aria-hidden="true">▾</span>
                            </div>
                        </label>
                    </form>
                </div>
                @if($podium->isNotEmpty())
                    <div class="admin-podium" aria-label="Podium siswa berdasarkan poin">
                        @foreach($podium->take(3) as $summary)
                            <article class="admin-podium-place">
                                <div class="podium-avatar-wrap">
                                    @if($loop->iteration === 1)
                                        <div class="crown-float" aria-hidden="true">👑</div>
                                    @endif
                                    <div class="admin-podium-avatar" aria-hidden="true">
                                        {{ mb_substr($summary->student?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="avatar-badge-chip {{ $loop->iteration === 1 ? 'gold' : ($loop->iteration === 2 ? 'silver' : 'bronze') }}" aria-hidden="true">
                                        {{ $loop->iteration === 1 ? '🥇' : ($loop->iteration === 2 ? '🥈' : '🥉') }}
                                    </div>
                                </div>
                                <strong class="podium-student-name">{{ $summary->student?->name }}</strong>
                                <small class="podium-student-class">{{ $summary->student?->studentProfile?->schoolClass?->name }}</small>
                                <span class="admin-podium-points">{{ $loop->iteration === 1 ? '✨ ' : '' }}{{ number_format($summary->general_points) }} poin</span>
                                <div class="admin-podium-step" aria-label="Peringkat {{ $loop->iteration }}">
                                    <span class="admin-podium-step-num">{{ $loop->iteration }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="podium-stage-base" aria-hidden="true"></div>

                    @if($podium->count() > 3)
                        <div class="podium-runners-card">
                            <div class="podium-runners-head">
                                <span class="podium-runners-title">🏅 Peringkat 4 – {{ $podium->count() }}</span>
                                <span class="podium-runners-badge">{{ $podium->count() - 3 }} siswa</span>
                            </div>
                            <div class="podium-runners-list">
                                @foreach($podium->skip(3) as $summary)
                                    <div class="podium-runner-row">
                                        <div class="runner-left">
                                            <span class="runner-rank-num">{{ $loop->iteration + 3 }}</span>
                                            <div class="runner-avatar-mini" aria-hidden="true">
                                                {{ mb_substr($summary->student?->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="runner-meta">
                                                <strong class="runner-name">{{ $summary->student?->name }}</strong>
                                                <span class="runner-class">{{ $summary->student?->studentProfile?->schoolClass?->name }}</span>
                                            </div>
                                        </div>
                                        <span class="runner-points">
                                            {{ number_format($summary->general_points) }} poin
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="muted" style="padding:45px 20px;text-align:center;font-size:15px;background:rgba(255,255,255,0.05);border-radius:14px;border:1px dashed rgba(255,255,255,0.15)">
                        🌱 Belum ada poin positif tercatat di kelas ini.
                    </div>
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

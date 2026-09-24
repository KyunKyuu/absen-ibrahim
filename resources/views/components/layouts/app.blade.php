<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Absensi Sekolah' }}</title>
    <style>
        :root {
            color-scheme: light;
            --ink:#17221e; --muted:#6b7772; --line:#e3e8e5; --bg:#f5f7f5; --surface:#fff;
            --accent:#196b50; --accent-dark:#11513c; --accent-soft:#e7f2ed; --danger:#b44735;
            --sidebar:#132d25; --sidebar-muted:#9db1aa; --shadow:0 1px 2px rgba(16,35,28,.04),0 8px 30px rgba(16,35,28,.04);
        }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; background:var(--bg); color:var(--ink); font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; font-size:14px; }
        a { color:inherit; text-decoration:none; }
        button,input,select,textarea { font:inherit; }
        .shell { min-height:100dvh; display:grid; grid-template-columns:252px minmax(0,1fr); }
        .sidebar { position:sticky; top:0; height:100dvh; display:flex; flex-direction:column; overflow-y:auto; padding:22px 16px 16px; color:#fff; background:var(--sidebar); }
        .brand { display:flex; align-items:center; gap:11px; margin:0 8px 26px; font-size:15px; font-weight:800; line-height:1.15; letter-spacing:-.01em; }
        .brand-mark { display:grid; place-items:center; width:38px; height:38px; flex:0 0 auto; border-radius:11px; color:var(--sidebar); background:#b9e3cc; font-size:17px; }
        .brand small { display:block; margin-top:3px; color:var(--sidebar-muted); font-size:10px; font-weight:650; letter-spacing:.06em; text-transform:uppercase; }
        .nav { display:grid; gap:3px; }
        .nav-label { margin:17px 11px 6px; color:#708d83; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .nav a,.nav button { width:100%; display:flex; align-items:center; gap:10px; min-height:42px; padding:9px 11px; border:0; border-radius:9px; color:#c7d5d0; background:transparent; text-align:left; cursor:pointer; transition:background .18s ease,color .18s ease; }
        .nav a:hover,.nav button:hover { color:#fff; background:rgba(255,255,255,.07); }
        .nav a.active { color:#fff; background:rgba(185,227,204,.14); font-weight:700; }
        .nav-icon { width:18px; height:18px; display:grid; place-items:center; flex:0 0 auto; font-size:15px; opacity:.9; }
        .nav-parent { color:#f4faf7 !important; font-weight:700; }
        .nav-parent::after { content:'⌄'; margin-left:auto; color:#88a399; font-size:16px; line-height:1; transition:transform .18s ease; }
        .subnav { display:none; gap:2px; margin:3px 0 7px 20px; padding-left:15px; border-left:1px solid rgba(255,255,255,.13); }
        .nav-parent.active:not(.collapsed) + .subnav,.nav-parent.expanded + .subnav { display:grid; }
        .nav-parent.active:not(.collapsed)::after,.nav-parent.expanded::after { transform:rotate(180deg); }
        .subnav a { min-height:34px; padding:7px 10px; font-size:12px; }
        .sidebar-foot { margin-top:auto; padding-top:20px; }
        .user-card { display:flex; align-items:center; gap:10px; padding:12px; border:1px solid rgba(255,255,255,.08); border-radius:11px; background:rgba(255,255,255,.04); }
        .avatar { display:grid; place-items:center; width:34px; height:34px; flex:0 0 auto; border-radius:50%; color:#143328; background:#b9e3cc; font-weight:800; }
        .user-card strong,.user-card span { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .user-card strong { font-size:12px; }
        .user-card span { margin-top:2px; color:var(--sidebar-muted); font-size:10px; text-transform:capitalize; }
        .main { min-width:0; width:100%; max-width:1500px; margin:0 auto; padding:34px clamp(22px,3vw,46px) 56px; }
        .topbar,.page-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; margin-bottom:24px; }
        .page-heading-copy { max-width:720px; }
        .breadcrumb { display:flex; gap:7px; align-items:center; margin-bottom:9px; color:var(--muted); font-size:12px; font-weight:650; }
        .breadcrumb a:hover { color:var(--accent); }
        h1 { margin:0; font-size:clamp(27px,3vw,36px); line-height:1.12; letter-spacing:-.035em; }
        h2 { margin:0 0 14px; font-size:17px; letter-spacing:-.015em; }
        h3 { margin:0; font-size:14px; }
        p { line-height:1.6; }
        .topbar p,.page-heading p { margin:7px 0 0; }
        .muted { color:var(--muted); }
        .grid { display:grid; gap:18px; }
        .grid.stats { grid-template-columns:repeat(5,minmax(0,1fr)); }
        .grid.two { grid-template-columns:minmax(0,1.2fr) minmax(300px,.8fr); align-items:start; }
        .panel { padding:22px; border:1px solid var(--line); border-radius:14px; background:var(--surface); box-shadow:var(--shadow); }
        .metric { padding:18px; border:1px solid var(--line); border-radius:13px; background:var(--surface); box-shadow:var(--shadow); }
        .metric strong { display:block; margin-top:7px; font-size:29px; font-variant-numeric:tabular-nums; letter-spacing:-.04em; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:42px; padding:10px 14px; border:1px solid #cfd8d3; border-radius:9px; color:#25342f; background:#fff; font-weight:720; cursor:pointer; transition:transform .16s ease,background .16s ease,border-color .16s ease; }
        .btn:hover { border-color:#aebdb6; background:#f8faf9; }
        .btn:active { transform:translateY(1px); }
        .btn.primary { border-color:var(--accent); color:#fff; background:var(--accent); }
        .btn.primary:hover { border-color:var(--accent-dark); background:var(--accent-dark); }
        .btn.warn { border-color:#efd4cf; color:var(--danger); background:#fff5f3; }
        .btn.small { min-height:34px; padding:7px 10px; font-size:12px; }
        label { display:grid; gap:7px; color:#35433e; font-size:12px; font-weight:720; }
        input,select,textarea { width:100%; padding:11px 12px; border:1px solid #d2dad6; border-radius:9px; outline:none; color:var(--ink); background:#fff; transition:border-color .15s ease,box-shadow .15s ease; }
        input:focus,select:focus,textarea:focus { border-color:#72a893; box-shadow:0 0 0 3px rgba(25,107,80,.09); }
        textarea { min-height:105px; resize:vertical; line-height:1.55; }
        form.stack,.stack { display:grid; gap:15px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }
        .wide { grid-column:1/-1; }
        .field-help { color:var(--muted); font-size:11px; font-weight:450; line-height:1.45; }
        .check-row { display:flex; align-items:center; gap:8px; }
        .check-row input { width:auto; }
        table { width:100%; border-collapse:collapse; font-size:13px; }
        .table-scroll { overflow-x:auto; }
        th,td { padding:12px 10px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; }
        th { color:#73807a; font-size:10px; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
        tr:last-child td { border-bottom:0; }
        .badge { display:inline-flex; align-items:center; border-radius:999px; padding:5px 9px; color:#2a5c49; background:#e7f2ed; font-size:10px; font-weight:800; white-space:nowrap; }
        .badge.priority { color:#914231; background:#fae8e3; }
        .alert { margin-bottom:18px; padding:13px 15px; border:1px solid #bad8ca; border-radius:10px; color:#22533f; background:#edf8f2; }
        .alert.errors { border-color:#efc4bb; color:#873624; background:#fff0ed; }
        .actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .actions form { display:flex; gap:6px; }
        .empty-state { padding:30px 18px; border:1px dashed #ccd6d1; border-radius:11px; text-align:center; color:var(--muted); background:#fafcfb; }
        .section-tabs { display:flex; gap:4px; margin:-5px 0 24px; padding:5px; overflow-x:auto; border:1px solid var(--line); border-radius:11px; background:#eef2ef; }
        .section-tabs a { flex:0 0 auto; padding:8px 12px; border-radius:7px; color:#607069; font-size:12px; font-weight:700; }
        .section-tabs a:hover,.section-tabs a.active { color:var(--ink); background:#fff; box-shadow:0 1px 3px rgba(20,44,35,.08); }
        .cms-list { display:grid; gap:10px; }
        .cms-item { border:1px solid var(--line); border-radius:11px; background:#fbfcfb; overflow:hidden; }
        .cms-item summary { display:grid; grid-template-columns:minmax(0,1fr) auto auto; gap:12px; align-items:center; padding:14px; cursor:pointer; list-style:none; }
        .cms-item summary::-webkit-details-marker { display:none; }
        .cms-item summary strong,.cms-item summary small { display:block; }
        .cms-item summary small { margin-top:3px; color:var(--muted); }
        .cms-item[open] summary { border-bottom:1px solid var(--line); background:#fff; }
        .cms-edit { padding:18px; }
        .cms-delete { margin-top:12px; padding-top:12px; border-top:1px solid var(--line); }
        .payment-form { display:grid; grid-template-columns:minmax(120px,1fr) 145px 110px auto; gap:6px; min-width:520px; }
        .promotion-row { display:grid; grid-template-columns:minmax(220px,1fr) minmax(180px,.5fr) auto; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid var(--line); }
        .progress-filter { margin-bottom:18px; }.progress-filter form{display:flex;gap:12px;justify-content:flex-end}.progress-filter label{min-width:210px}
        .student-dashboard { display:grid; grid-template-columns:280px minmax(0,1fr); gap:18px; align-items:start; }.progress-list{display:grid;gap:18px}.student-heading{display:flex;justify-content:space-between;gap:14px;align-items:flex-start}.student-heading h2{font-size:23px;margin:3px 0 4px}.eyebrow{color:var(--accent);font-size:10px;font-weight:800;letter-spacing:.09em;text-transform:uppercase}.level-badge{border-radius:999px;padding:7px 11px;background:#dcece5;color:#245747;font-size:12px;font-weight:800;white-space:nowrap}.level-badge.priority{background:#f7ded8;color:#873624}
        .point-grid,.attendance-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:18px}.point-grid div,.attendance-grid div{border:1px solid var(--line);border-radius:9px;padding:12px}.point-grid span,.attendance-grid span{display:block;color:var(--muted);font-size:11px}.point-grid strong,.attendance-grid strong{display:block;margin-bottom:2px;font-size:22px}.level-progress{margin-top:16px}.progress-copy{display:flex;justify-content:space-between;font-size:12px;font-weight:750}.progress-track{height:9px;margin:7px 0 5px;overflow:hidden;border-radius:999px;background:#e4e8e5}.progress-track span{display:block;height:100%;border-radius:inherit;background:var(--accent)}.progress-details{display:grid;grid-template-columns:1.35fr .65fr;gap:22px;margin-top:22px;padding-top:18px;border-top:1px solid var(--line)}
        .timeline{display:grid}.timeline-item{display:grid;grid-template-columns:14px minmax(0,1fr) auto;gap:9px;padding:9px 0;align-items:start}.timeline-dot{width:9px;height:9px;margin-top:5px;border-radius:50%;background:var(--accent)}.timeline-item strong,.timeline-item small{display:block}.timeline-item small{color:var(--muted);margin-top:2px}.point-change{color:var(--accent);font-weight:800}.point-change.negative{color:var(--danger)}.class-history{display:grid;gap:8px}.class-history div{padding:10px;background:#f5f7f5;border-radius:8px}.class-history strong,.class-history span{display:block}.class-history span{margin-top:3px;color:var(--muted);font-size:12px}
        .permission-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.role-list{display:grid;gap:7px;margin-top:14px}.role-list details{padding:9px;border-radius:8px;background:#f5f7f5}.role-list summary{cursor:pointer}.role-list summary span{color:var(--muted);font-size:12px;margin-left:5px}.role-list details form{margin-top:12px}.attendance-success{display:flex;align-items:flex-start;gap:12px;padding:13px;border-radius:8px;background:#edf6ef}.attendance-success p{margin:4px 0}.status-icon{display:grid;place-items:center;width:30px;height:30px;flex:0 0 auto;border-radius:50%;color:#fff;background:var(--accent);font-weight:900}.geo-status:empty{display:none}
        .login{min-height:100dvh;display:grid;grid-template-columns:1.1fr .9fr}.login-hero{padding:8vw;background:#eff3ef;display:flex;flex-direction:column;justify-content:space-between}.login-card{display:flex;align-items:center;justify-content:center;padding:32px}.login-card .panel{width:min(430px,100%)}
        @media(max-width:1000px){.grid.stats{grid-template-columns:repeat(3,minmax(0,1fr))}.grid.two{grid-template-columns:1fr}}
        @media(max-width:760px){.shell,.login{grid-template-columns:1fr}.sidebar{position:relative;height:auto;padding:14px}.brand{margin-bottom:12px}.nav{grid-template-columns:repeat(2,minmax(0,1fr))}.nav-label,.sidebar-foot{display:none}.subnav{grid-column:1/-1}.main{padding:24px 16px 44px}.topbar,.page-heading{display:block}.topbar>.btn,.page-heading>.btn{margin-top:14px}.grid.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.form-grid{grid-template-columns:1fr}.wide{grid-column:auto}.promotion-row,.student-dashboard,.progress-details{grid-template-columns:1fr}.point-grid,.attendance-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.progress-filter form{display:grid;grid-template-columns:1fr}.permission-grid{grid-template-columns:1fr}.cms-item summary{grid-template-columns:1fr auto}.cms-item summary .badge:first-of-type{display:none}}
    </style>
</head>
<body>
@auth
    <div class="shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark">A</span>
                <span>Absensi Sekolah<small>Ruang administrasi</small></span>
            </a>
            <nav class="nav" aria-label="Navigasi utama">
                <div class="nav-label">Utama</div>
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="nav-icon">⌂</span>Ringkasan</a>
                @if(auth()->user()->hasRole('superadmin'))
                    <div class="nav-label">Website sekolah</div>
                    <a class="nav-parent {{ request()->routeIs('admin.landing.*') ? 'active' : '' }}" href="{{ route('admin.landing.index') }}"><span class="nav-icon">◫</span>Landing Page</a>
                    <div class="subnav">
                        <a class="{{ request()->routeIs('admin.landing.index') ? 'active' : '' }}" href="{{ route('admin.landing.index') }}">Ringkasan</a>
                        <a class="{{ request()->routeIs('admin.landing.hero') ? 'active' : '' }}" href="{{ route('admin.landing.hero') }}">Hero & tombol</a>
                        <a class="{{ request()->routeIs('admin.landing.profile') ? 'active' : '' }}" href="{{ route('admin.landing.profile') }}">Profil & visi-misi</a>
                        <a class="{{ request()->routeIs('admin.landing.admission') ? 'active' : '' }}" href="{{ route('admin.landing.admission') }}">Admisi & kontak</a>
                        <a class="{{ request()->routeIs('admin.landing.content') ? 'active' : '' }}" href="{{ route('admin.landing.content', 'program') }}">Konten</a>
                        <a class="{{ request()->routeIs('admin.landing.tuition') ? 'active' : '' }}" href="{{ route('admin.landing.tuition') }}">Biaya pendidikan</a>
                    </div>
                @endif
                <div class="nav-label">Operasional</div>
                @if(auth()->user()->canDo('users.manage'))
                    <a class="nav-parent {{ request()->routeIs('admin.users', 'admin.users.*', 'admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.users') }}"><span class="nav-icon">◎</span>Akun & akses</a>
                    <div class="subnav">
                        <a class="{{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">Daftar akun</a>
                        <a class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">Buat akun</a>
                        <a class="{{ request()->routeIs('admin.users.import') ? 'active' : '' }}" href="{{ route('admin.users.import') }}">Import akun</a>
                        <a class="{{ request()->routeIs('admin.users.roles') ? 'active' : '' }}" href="{{ route('admin.users.roles') }}">Role & akses</a>
                        <a class="{{ request()->routeIs('admin.users.parents') ? 'active' : '' }}" href="{{ route('admin.users.parents') }}">Relasi orang tua</a>
                    </div>
                @endif
                @if(auth()->user()->canDo('school.manage'))
                    <a class="nav-parent {{ request()->routeIs('admin.settings', 'admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings') }}"><span class="nav-icon">◇</span>Sekolah & IoT</a>
                    <div class="subnav">
                        <a class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">Profil & absensi</a>
                        <a class="{{ request()->routeIs('admin.settings.classes') ? 'active' : '' }}" href="{{ route('admin.settings.classes') }}">Kelas</a>
                        <a class="{{ request()->routeIs('admin.settings.academic') ? 'active' : '' }}" href="{{ route('admin.settings.academic') }}">Periode akademik</a>
                        <a class="{{ request()->routeIs('admin.settings.iot') ? 'active' : '' }}" href="{{ route('admin.settings.iot') }}">Perangkat IoT</a>
                    </div>
                @endif
                @if(auth()->user()->canDo('school.manage') || auth()->user()->canDo('assessments.manage'))
                    <a class="nav-parent {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}"><span class="nav-icon">▦</span>Kelas & pengajaran</a>
                    <div class="subnav">
                        <a class="{{ request()->routeIs('classes.index') ? 'active' : '' }}" href="{{ route('classes.index') }}">Daftar kelas</a>
                        <a class="{{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">Daftar siswa</a>
                        @if(auth()->user()->canDo('school.manage'))
                            <a class="{{ request()->routeIs('classes.subjects') ? 'active' : '' }}" href="{{ route('classes.subjects') }}">Mata pelajaran</a>
                        <a class="{{ request()->routeIs('classes.teaching') ? 'active' : '' }}" href="{{ route('classes.teaching') }}">Penugasan guru</a>
                        <a class="{{ request()->routeIs('classes.promotions') ? 'active' : '' }}" href="{{ route('classes.promotions') }}">Kenaikan kelas</a>
                        @endif
                    </div>
                @endif
                @if(auth()->user()->canDo('assessments.manage'))
                    <a class="nav-parent {{ request()->routeIs('teacher.assessments', 'teacher.assessments.*', 'teacher.grades.*') ? 'active' : '' }}" href="{{ route('teacher.assessments') }}"><span class="nav-icon">✓</span>Penilaian</a>
                    <div class="subnav"><a class="{{ request()->routeIs('teacher.grades.*') ? 'active' : '' }}" href="{{ route('teacher.grades.index') }}">Nilai mata pelajaran</a><a class="{{ request()->routeIs('teacher.assessments') ? 'active' : '' }}" href="{{ route('teacher.assessments') }}">Penilaian sikap</a><a class="{{ request()->routeIs('teacher.assessments.achievements') ? 'active' : '' }}" href="{{ route('teacher.assessments.achievements') }}">Prestasi & pelanggaran</a></div>
                @endif
                @if(auth()->user()->canDo('attendance.manage'))
                    <a class="nav-parent {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}"><span class="nav-icon">◷</span>Absensi</a>
                    <div class="subnav"><a class="{{ request()->routeIs('attendance.index') ? 'active' : '' }}" href="{{ route('attendance.index') }}">Semua absensi</a><a class="{{ request()->routeIs('attendance.today') ? 'active' : '' }}" href="{{ route('attendance.today') }}">Hari ini</a></div>
                @endif
                @if(auth()->user()->canDo('finance.manage') || auth()->user()->canDo('finance.propose') || auth()->user()->hasAnyRole(['student', 'parent']))
                    <a class="nav-parent {{ request()->routeIs('finance.*') ? 'active' : '' }}" href="{{ route('finance.index') }}"><span class="nav-icon">▤</span>Keuangan</a>
                    <div class="subnav">
                        @if(auth()->user()->canDo('finance.manage'))
                            <a class="{{ request()->routeIs('finance.index') ? 'active' : '' }}" href="{{ route('finance.index') }}">Daftar tagihan</a>
                            <a class="{{ request()->routeIs('finance.record-payment') ? 'active' : '' }}" href="{{ route('finance.record-payment') }}">Catat pembayaran</a>
                            <a class="{{ request()->routeIs('finance.confirmations') ? 'active' : '' }}" href="{{ route('finance.confirmations') }}">Verifikasi pembayaran</a>
                            <a class="{{ request()->routeIs('finance.issue') ? 'active' : '' }}" href="{{ route('finance.issue') }}">Terbitkan tagihan</a>
                            <a class="{{ request()->routeIs('finance.fee-types') ? 'active' : '' }}" href="{{ route('finance.fee-types') }}">Jenis tagihan</a>
                            <a class="{{ request()->routeIs('finance.proposals') ? 'active' : '' }}" href="{{ route('finance.proposals') }}">Usulan biaya</a>
                            <a class="{{ request()->routeIs('finance.payment-history') ? 'active' : '' }}" href="{{ route('finance.payment-history') }}">Riwayat pembayaran</a>
                        @elseif(auth()->user()->hasAnyRole(['student', 'parent']))
                            <a class="{{ request()->routeIs('finance.index') ? 'active' : '' }}" href="{{ route('finance.index') }}">{{ auth()->user()->hasRole('parent') ? 'Tagihan anak' : 'Tagihan saya' }}</a>
                            <a class="{{ request()->routeIs('finance.payment-history') ? 'active' : '' }}" href="{{ route('finance.payment-history') }}">Riwayat pembayaran</a>
                        @else
                            <a class="{{ request()->routeIs('finance.index') ? 'active' : '' }}" href="{{ route('finance.index') }}">Ajukan biaya</a>
                            <a class="{{ request()->routeIs('finance.proposals') ? 'active' : '' }}" href="{{ route('finance.proposals') }}">Riwayat usulan</a>
                        @endif
                    </div>
                @endif
                <div class="nav-label">Lainnya</div>
                <a href="{{ route('landing') }}" target="_blank"><span class="nav-icon">↗</span>Lihat situs publik</a>
            </nav>
            <div class="sidebar-foot">
                <div class="user-card">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span style="min-width:0;flex:1"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->role }}</span></span>
                    <form method="post" action="{{ route('logout') }}">@csrf<button title="Keluar" aria-label="Keluar">↪</button></form>
                </div>
            </div>
        </aside>
        <main class="main">
            @if(session('status'))<div class="alert flash-inline" role="status">{{ session('status') }}</div>@endif
            @if($errors->any())<div class="alert errors flash-inline" role="alert">{{ $errors->first() }}</div>@endif
            {{ $slot }}
        </main>
    </div>
@else
    {{ $slot }}
@endauth
@auth
<script>
    (() => {
        const parents = Array.from(document.querySelectorAll('.nav-parent'));
        const setExpandedState = () => parents.forEach((parent) => {
            const submenu = parent.nextElementSibling;
            parent.setAttribute('aria-expanded', submenu && getComputedStyle(submenu).display !== 'none' ? 'true' : 'false');
        });

        parents.forEach((parent) => parent.addEventListener('click', (event) => {
            const submenu = parent.nextElementSibling;
            if (!submenu?.classList.contains('subnav')) return;
            event.preventDefault();
            const wasOpen = getComputedStyle(submenu).display !== 'none';

            parents.forEach((item) => {
                item.classList.remove('expanded');
                item.classList.add('collapsed');
            });

            if (!wasOpen) {
                parent.classList.remove('collapsed');
                parent.classList.add('expanded');
            }
            setExpandedState();
        }));
        setExpandedState();
    })();
</script>
@endauth
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (() => {
        const success = @json(session('status'));
        const error = @json($errors->any() ? $errors->first() : null);
        if (typeof Swal === 'undefined') return;
        document.querySelectorAll('.flash-inline').forEach((element) => { element.hidden = true; });
        if (success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: success,
                toast: true,
                position: 'top-end',
                timer: 3200,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        } else if (error) {
            Swal.fire({
                icon: 'error',
                title: 'Belum tersimpan',
                text: error,
                toast: true,
                position: 'top-end',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }
    })();
</script>
</body>
</html>

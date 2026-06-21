<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Absensi Sekolah' }}</title>
    <style>
        :root { color-scheme: light; --ink:#202124; --muted:#687078; --line:#dde2e7; --bg:#f7f8f6; --surface:#ffffff; --accent:#2f6f5e; --accent-2:#c8523b; }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--bg); color:var(--ink); font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; letter-spacing:0; }
        a { color:inherit; text-decoration:none; }
        .shell { min-height:100dvh; display:grid; grid-template-columns:260px 1fr; }
        .sidebar { background:#f0f2ef; border-right:1px solid var(--line); padding:24px 18px; position:sticky; top:0; height:100dvh; }
        .brand { font-weight:800; font-size:21px; line-height:1.05; margin-bottom:28px; }
        .nav { display:grid; gap:6px; }
        .nav a, .nav button { width:100%; border:0; background:transparent; color:#39413f; text-align:left; padding:10px 12px; border-radius:8px; font:inherit; cursor:pointer; }
        .nav a.active, .nav a:hover, .nav button:hover { background:#dfe7e2; }
        .main { padding:28px; max-width:1400px; width:100%; margin:0 auto; }
        .topbar { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:24px; }
        h1 { font-size:32px; line-height:1.1; margin:0; letter-spacing:0; }
        h2 { font-size:18px; margin:0 0 14px; }
        .muted { color:var(--muted); }
        .grid { display:grid; gap:16px; }
        .grid.stats { grid-template-columns:repeat(5, minmax(0, 1fr)); }
        .grid.two { grid-template-columns:1.2fr .8fr; align-items:start; }
        .panel { background:var(--surface); border:1px solid var(--line); border-radius:8px; padding:18px; }
        .metric { border-top:3px solid var(--accent); padding-top:12px; }
        .metric strong { display:block; font-size:28px; font-variant-numeric:tabular-nums; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; border:1px solid #bdc8c2; background:#fff; color:#1f2a27; padding:10px 12px; border-radius:8px; font-weight:650; cursor:pointer; min-height:42px; transition:transform .2s ease, background .2s ease; }
        .btn:active { transform:translateY(1px) scale(.99); }
        .btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
        .btn.warn { background:var(--accent-2); border-color:var(--accent-2); color:#fff; }
        label { display:grid; gap:7px; font-weight:650; font-size:13px; color:#35403c; }
        input, select, textarea { width:100%; border:1px solid #cdd5d0; border-radius:8px; padding:10px 11px; background:#fff; color:var(--ink); font:inherit; }
        textarea { min-height:88px; resize:vertical; }
        form.stack { display:grid; gap:13px; }
        .form-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:13px; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th, td { padding:11px 10px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; }
        th { color:#56615d; font-size:12px; text-transform:uppercase; letter-spacing:.04em; }
        .badge { display:inline-flex; align-items:center; border-radius:999px; padding:4px 9px; background:#e9eee9; color:#31423c; font-size:12px; font-weight:700; }
        .badge.priority { background:#f7ded8; color:#873624; }
        .alert { border:1px solid #cad7ce; background:#edf6ef; border-radius:8px; padding:12px 14px; margin-bottom:16px; }
        .errors { border-color:#efc4bb; background:#fff0ed; }
        .login { min-height:100dvh; display:grid; grid-template-columns:1.1fr .9fr; }
        .login-hero { padding:8vw; background:#eff3ef; display:flex; flex-direction:column; justify-content:space-between; }
        .login-card { display:flex; align-items:center; justify-content:center; padding:32px; }
        .login-card .panel { width:min(430px,100%); }
        @media (max-width: 920px) {
            .shell, .login { grid-template-columns:1fr; }
            .sidebar { position:relative; height:auto; }
            .grid.stats, .grid.two, .form-grid { grid-template-columns:1fr; }
            .main { padding:18px; }
        }
    </style>
</head>
<body>
    @auth
        <div class="shell">
            <aside class="sidebar">
                <div class="brand">Absensi<br>Sekolah</div>
                <nav class="nav">
                    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    @if(auth()->user()->role === 'admin')
                        <a class="{{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">Akun</a>
                        <a class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">Sekolah & IoT</a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'teacher'], true))
                        <a class="{{ request()->routeIs('teacher.assessments') ? 'active' : '' }}" href="{{ route('teacher.assessments') }}">Penilaian</a>
                        <a class="{{ request()->routeIs('attendance.index') ? 'active' : '' }}" href="{{ route('attendance.index') }}">Absensi</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}">@csrf<button>Keluar</button></form>
                </nav>
            </aside>
            <main class="main">
                @if(session('status')) <div class="alert">{{ session('status') }}</div> @endif
                @if($errors->any()) <div class="alert errors">{{ $errors->first() }}</div> @endif
                {{ $slot }}
            </main>
        </div>
    @else
        {{ $slot }}
    @endauth
</body>
</html>

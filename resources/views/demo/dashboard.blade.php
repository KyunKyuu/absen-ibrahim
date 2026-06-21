<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo Dashboard Absensi Sekolah</title>
    <style>
        :root {
            --bg: #f7f8f6;
            --surface: #ffffff;
            --ink: #202521;
            --muted: #68736e;
            --line: #dfe5df;
            --accent: #2f6f5e;
            --accent-soft: #e4eee9;
            --warn: #b94c38;
            --warn-soft: #f7ded8;
            --gold: #b78b2e;
            --gold-soft: #f5ecd8;
            --shadow: 0 24px 70px -45px rgba(31, 50, 42, .42);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
        }
        a { color: inherit; text-decoration: none; }
        .page { min-height: 100dvh; }
        .top-nav {
            position: sticky;
            top: 0;
            z-index: 5;
            background: rgba(247, 248, 246, .88);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(223, 229, 223, .72);
        }
        .nav-inner {
            width: min(1420px, calc(100% - 40px));
            margin: 0 auto;
            min-height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 850;
            letter-spacing: -.01em;
        }
        .brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--accent);
            color: #fff;
            display: grid;
            place-items: center;
        }
        .brand-mark svg { width: 19px; height: 19px; }
        .nav-links {
            display: flex;
            gap: 6px;
            align-items: center;
            color: #3e4844;
            font-size: 14px;
        }
        .nav-links a {
            padding: 9px 11px;
            border-radius: 8px;
            transition: background .24s cubic-bezier(.16, 1, .3, 1), transform .24s cubic-bezier(.16, 1, .3, 1);
        }
        .nav-links a:hover { background: var(--accent-soft); transform: translateY(-1px); }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 14px;
            border: 1px solid #bac8c0;
            border-radius: 8px;
            background: #fff;
            font-weight: 750;
            cursor: pointer;
            transition: transform .24s cubic-bezier(.16, 1, .3, 1), background .24s cubic-bezier(.16, 1, .3, 1);
        }
        .btn:active { transform: translateY(1px) scale(.98); }
        .btn.primary {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }
        .wrap {
            width: min(1420px, calc(100% - 40px));
            margin: 0 auto;
        }
        .hero {
            min-height: calc(100dvh - 68px);
            display: grid;
            grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr);
            gap: 28px;
            align-items: center;
            padding: 34px 0 24px;
        }
        .hero-copy {
            display: grid;
            gap: 22px;
            align-content: center;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            gap: 8px;
            color: #304940;
            background: var(--accent-soft);
            border: 1px solid #c8d8d0;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 13px;
            font-weight: 800;
        }
        .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse 1.8s cubic-bezier(.16, 1, .3, 1) infinite;
        }
        h1 {
            margin: 0;
            max-width: 790px;
            font-size: clamp(38px, 5vw, 76px);
            line-height: .98;
            letter-spacing: 0;
            font-weight: 880;
        }
        .lead {
            margin: 0;
            max-width: 620px;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.65;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        .hero-note {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            max-width: 700px;
        }
        .note {
            border-top: 1px solid var(--line);
            padding-top: 12px;
        }
        .note strong {
            display: block;
            font-size: 22px;
            font-variant-numeric: tabular-nums;
        }
        .note span { color: var(--muted); font-size: 13px; }
        .demo-board {
            background: #fdfefd;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: rise .7s cubic-bezier(.16, 1, .3, 1) both;
        }
        .board-head {
            min-height: 62px;
            padding: 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .board-title { display: grid; gap: 3px; }
        .board-title strong { font-size: 16px; }
        .board-title span { color: var(--muted); font-size: 12px; }
        .segmented {
            display: flex;
            background: #eef2ee;
            padding: 3px;
            border-radius: 8px;
        }
        .segmented span {
            padding: 7px 9px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 800;
            color: #65716c;
        }
        .segmented span:first-child { background: #fff; color: var(--ink); }
        .board-grid {
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            gap: 0;
        }
        .board-main {
            padding: 18px;
            border-right: 1px solid var(--line);
            display: grid;
            gap: 14px;
        }
        .mini-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }
        .mini-stat {
            background: #f7f9f7;
            border: 1px solid #e4e9e4;
            border-radius: 8px;
            padding: 12px;
        }
        .mini-stat span {
            display: block;
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 6px;
        }
        .mini-stat strong {
            font-size: 23px;
            font-variant-numeric: tabular-nums;
        }
        .signal-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .signal {
            min-height: 148px;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e0e7e1;
            background: #fff;
            display: grid;
            align-content: space-between;
            overflow: hidden;
            position: relative;
        }
        .signal::after {
            content: "";
            position: absolute;
            inset: auto -20% -32px 16%;
            height: 70px;
            background: repeating-linear-gradient(90deg, rgba(47,111,94,.15) 0 10px, transparent 10px 22px);
            transform: skewX(-20deg);
        }
        .signal.warn::after { background: repeating-linear-gradient(90deg, rgba(185,76,56,.16) 0 10px, transparent 10px 22px); }
        .signal h3 { margin: 0 0 5px; font-size: 15px; }
        .signal p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.5; }
        .status-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: relative;
            z-index: 1;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            min-height: 25px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 850;
            background: var(--accent-soft);
            color: #28594d;
        }
        .badge.warn { background: var(--warn-soft); color: #873624; }
        .badge.gold { background: var(--gold-soft); color: #765918; }
        .student-list {
            display: grid;
            gap: 8px;
        }
        .student {
            display: grid;
            grid-template-columns: 38px 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 10px;
            border: 1px solid #e5eae5;
            border-radius: 9px;
            background: #fff;
            animation: rise .6s cubic-bezier(.16, 1, .3, 1) both;
            animation-delay: calc(var(--i) * 70ms);
        }
        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #dfe9e3;
            display: grid;
            place-items: center;
            font-weight: 850;
            color: #2e584d;
        }
        .student strong { display: block; font-size: 14px; }
        .student span { display: block; color: var(--muted); font-size: 12px; margin-top: 2px; }
        .points { text-align: right; font-weight: 850; font-variant-numeric: tabular-nums; }
        .board-side {
            padding: 18px;
            display: grid;
            gap: 14px;
            align-content: start;
            background: #f8faf8;
        }
        .phone-preview {
            width: min(270px, 100%);
            margin: 0 auto;
            border: 1px solid #dce5df;
            border-radius: 24px;
            background: #fff;
            padding: 12px;
            box-shadow: 0 18px 50px -36px rgba(31, 50, 42, .65);
        }
        .phone-bar {
            width: 70px;
            height: 5px;
            border-radius: 999px;
            background: #d8ded9;
            margin: 0 auto 14px;
        }
        .checkin {
            border-radius: 16px;
            background: var(--accent);
            color: #fff;
            padding: 16px;
            display: grid;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }
        .checkin::after {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            border: 1px solid rgba(255,255,255,.35);
            border-radius: 50%;
            right: -38px;
            top: -26px;
            animation: float 5.5s ease-in-out infinite;
        }
        .checkin strong { font-size: 22px; position: relative; z-index: 1; }
        .checkin span { color: rgba(255,255,255,.76); font-size: 13px; position: relative; z-index: 1; }
        .role-stack {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin: 26px 0 34px;
        }
        .role {
            border: 1px solid var(--line);
            background: var(--surface);
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 18px 40px -34px rgba(31, 50, 42, .45);
        }
        .role svg { width: 24px; height: 24px; color: var(--accent); margin-bottom: 16px; }
        .role strong { display: block; font-size: 16px; margin-bottom: 7px; }
        .role p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.55; }
        .section { padding: 30px 0 48px; }
        .section-head {
            display: grid;
            grid-template-columns: .8fr 1.2fr;
            gap: 26px;
            align-items: end;
            margin-bottom: 18px;
        }
        .section-head h2 { margin: 0; font-size: clamp(28px, 3vw, 46px); line-height: 1.05; }
        .section-head p { margin: 0; color: var(--muted); line-height: 1.65; max-width: 720px; }
        .feature-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr 1fr;
            gap: 14px;
        }
        .feature {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px;
            min-height: 220px;
            display: grid;
            align-content: space-between;
            box-shadow: 0 18px 45px -38px rgba(31, 50, 42, .55);
        }
        .feature.tall { min-height: 454px; }
        .feature h3 { margin: 0 0 8px; font-size: 19px; }
        .feature p { margin: 0; color: var(--muted); line-height: 1.55; font-size: 14px; }
        .timeline {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }
        .timeline-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 12px;
            align-items: start;
        }
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 5px;
            background: var(--accent);
            box-shadow: 0 0 0 6px var(--accent-soft);
        }
        .timeline-item strong { display: block; font-size: 14px; }
        .timeline-item span { color: var(--muted); font-size: 13px; }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 12px;
        }
        .report-table th,
        .report-table td {
            padding: 10px 8px;
            border-bottom: 1px solid var(--line);
            text-align: left;
        }
        .report-table th {
            color: #65716c;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .progress {
            height: 9px;
            background: #e9eee9;
            border-radius: 999px;
            overflow: hidden;
        }
        .progress span {
            display: block;
            height: 100%;
            width: var(--w);
            background: var(--accent);
            border-radius: inherit;
        }
        .footer-band {
            margin: 20px 0 42px;
            border: 1px solid #cddbd3;
            background: #edf4ef;
            border-radius: 16px;
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .footer-band h2 { margin: 0 0 5px; font-size: 24px; }
        .footer-band p { margin: 0; color: var(--muted); }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(47,111,94,.28); }
            50% { box-shadow: 0 0 0 8px rgba(47,111,94,0); }
        }
        @keyframes rise {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translate3d(0,0,0); }
            50% { transform: translate3d(-10px,12px,0); }
        }
        @media (max-width: 1080px) {
            .hero,
            .board-grid,
            .section-head,
            .feature-grid {
                grid-template-columns: 1fr;
            }
            .board-main { border-right: 0; border-bottom: 1px solid var(--line); }
            .role-stack { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 720px) {
            .wrap, .nav-inner { width: min(100% - 28px, 1420px); }
            .nav-links { display: none; }
            .hero { padding-top: 24px; }
            .hero-note,
            .mini-stats,
            .signal-row,
            .role-stack {
                grid-template-columns: 1fr;
            }
            h1 { font-size: 38px; }
            .student { grid-template-columns: 36px 1fr; }
            .points { text-align: left; grid-column: 2; }
            .footer-band { display: grid; }
        }
    </style>
</head>
<body>
<div class="page">
    <header class="top-nav">
        <div class="nav-inner">
            <a class="brand" href="#top">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M6 6.8 12 4l6 2.8v4.8c0 3.9-2.4 7.4-6 8.7-3.6-1.3-6-4.8-6-8.7V6.8Z" stroke="currentColor" stroke-width="2"/><path d="m9 12 2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                SikapTrack
            </a>
            <nav class="nav-links">
                <a href="#role">Role</a>
                <a href="#absensi">Absensi</a>
                <a href="#penilaian">Penilaian</a>
                <a href="#laporan">Laporan</a>
            </nav>
            <a class="btn primary" href="{{ route('login') }}">Masuk Sistem</a>
        </div>
    </header>

    <main id="top">
        <section class="wrap hero">
            <div class="hero-copy">
                <div class="eyebrow"><span class="pulse"></span> Demo dashboard untuk presentasi client</div>
                <h1>Absensi, sikap, poin, dan perhatian siswa dalam satu dashboard.</h1>
                <p class="lead">Tampilan ini menunjukkan bagaimana admin, guru, siswa, dan orang tua memantau disiplin, perkembangan soft skill, pencapaian, pelanggaran, serta integrasi fingerprint IoT.</p>
                <div class="hero-actions">
                    <a class="btn primary" href="#absensi">Lihat Alur Absensi</a>
                    <a class="btn" href="#penilaian">Lihat Penilaian</a>
                </div>
                <div class="hero-note">
                    <div class="note"><strong>428</strong><span>Siswa aktif</span></div>
                    <div class="note"><strong>93.4%</strong><span>Kehadiran minggu ini</span></div>
                    <div class="note"><strong>17</strong><span>Siswa perlu perhatian</span></div>
                </div>
            </div>

            <div class="demo-board" aria-label="Preview dashboard sekolah">
                <div class="board-head">
                    <div class="board-title">
                        <strong>Dashboard Guru Piket</strong>
                        <span>Senin, 22 Juni 2026 - live preview</span>
                    </div>
                    <div class="segmented"><span>Hari ini</span><span>Minggu</span><span>Semester</span></div>
                </div>
                <div class="board-grid">
                    <div class="board-main">
                        <div class="mini-stats">
                            <div class="mini-stat"><span>Hadir</span><strong>384</strong></div>
                            <div class="mini-stat"><span>Terlambat</span><strong>21</strong></div>
                            <div class="mini-stat"><span>Izin/Sakit</span><strong>13</strong></div>
                            <div class="mini-stat"><span>IoT Scan</span><strong>247</strong></div>
                        </div>
                        <div class="signal-row">
                            <div class="signal">
                                <div>
                                    <h3>Absensi GPS radius sekolah</h3>
                                    <p>Siswa hanya bisa absen saat berada dalam area yang ditentukan admin.</p>
                                </div>
                                <div class="status-line"><span class="badge">Radius 100 m</span><strong>valid</strong></div>
                            </div>
                            <div class="signal warn">
                                <div>
                                    <h3>Label perhatian otomatis</h3>
                                    <p>Siswa dengan poin minus akan muncul di prioritas guru dan wali kelas.</p>
                                </div>
                                <div class="status-line"><span class="badge warn">-50 poin</span><strong>aware</strong></div>
                            </div>
                        </div>
                        <div class="student-list">
                            <div class="student" style="--i:1"><div class="avatar">KR</div><div><strong>Kirana Ratri</strong><span>X IPA 1 - Teladan</span></div><div class="points">128</div></div>
                            <div class="student" style="--i:2"><div class="avatar">BD</div><div><strong>Bagas Danendra</strong><span>XI IPS 2 - Berkembang Baik</span></div><div class="points">74</div></div>
                            <div class="student" style="--i:3"><div class="avatar">NA</div><div><strong>Nabila Aksara</strong><span>X IPA 3 - Perlu Pembinaan</span></div><div class="points">-24</div></div>
                            <div class="student" style="--i:4"><div class="avatar">RY</div><div><strong>Rizky Yudhistira</strong><span>IX B - Prioritas Perhatian</span></div><div class="points">-58</div></div>
                        </div>
                    </div>
                    <aside class="board-side">
                        <div class="phone-preview">
                            <div class="phone-bar"></div>
                            <div class="checkin">
                                <span>Absensi siswa</span>
                                <strong>Tepat waktu</strong>
                                <span>Jarak 42 m dari sekolah, poin absen +5.</span>
                            </div>
                            <table class="report-table">
                                <tr><th>Poin</th><th>Nilai</th></tr>
                                <tr><td>General</td><td>86</td></tr>
                                <tr><td>Sikap</td><td>34</td></tr>
                                <tr><td>Absen</td><td>27</td></tr>
                                <tr><td>Prestasi</td><td>25</td></tr>
                            </table>
                        </div>
                        <div class="badge gold">Achievement: Hadir 7 hari berturut-turut</div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="wrap" id="role">
            <div class="role-stack">
                <div class="role">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 4 7v6c0 4 3 7 8 8 5-1 8-4 8-8V7l-8-4Z" stroke="currentColor" stroke-width="2"/><path d="M9 12h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <strong>Admin</strong>
                    <p>Membuat akun, kelas, lokasi sekolah, relasi orang tua, dan perangkat fingerprint.</p>
                </div>
                <div class="role">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M5 20V7l7-3 7 3v13" stroke="currentColor" stroke-width="2"/><path d="M8 20v-8h8v8" stroke="currentColor" stroke-width="2"/></svg>
                    <strong>Guru</strong>
                    <p>Memberi penilaian sikap, pencapaian, pelanggaran, dan memantau siswa prioritas.</p>
                </div>
                <div class="role">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/><path d="M4 21c1.2-4 4-6 8-6s6.8 2 8 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <strong>Siswa</strong>
                    <p>Melakukan absen mandiri dan melihat poin, label, achievement, serta histori perkembangan.</p>
                </div>
                <div class="role">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M8 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM17 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2"/><path d="M2 21c1-4 3-6 6-6s5 2 6 6M14 20c.7-2.6 2-4 4-4 1.6 0 2.8.9 4 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <strong>Orang Tua</strong>
                    <p>Memantau absensi, poin, label, dan catatan perkembangan anak dari sekolah.</p>
                </div>
            </div>
        </section>

        <section class="wrap section" id="absensi">
            <div class="section-head">
                <h2>Absensi siap untuk GPS dan fingerprint.</h2>
                <p>Sistem punya dua jalur absensi: siswa absen sendiri dari browser dengan validasi radius sekolah, atau perangkat fingerprint mengirim data scan ke API. Keduanya masuk ke tabel absensi yang sama.</p>
            </div>
            <div class="feature-grid">
                <article class="feature tall">
                    <div>
                        <span class="badge">GPS Browser</span>
                        <h3 style="margin-top:18px">Validasi radius sekolah</h3>
                        <p>Admin menentukan koordinat sekolah dan radius. Jika siswa berada di luar area, absen ditolak dan guru bisa melihat alasan validasi.</p>
                    </div>
                    <div>
                        <div class="progress"><span style="--w:84%"></span></div>
                        <p style="margin-top:10px">84% absensi web valid dalam radius 100 meter.</p>
                    </div>
                </article>
                <article class="feature">
                    <div>
                        <span class="badge">IoT Fingerprint</span>
                        <h3 style="margin-top:18px">Endpoint perangkat</h3>
                        <p>Perangkat mengirim device identifier, token, fingerprint ID, dan waktu scan ke API Laravel.</p>
                    </div>
                    <code>POST /api/iot/attendance</code>
                </article>
                <article class="feature">
                    <div>
                        <span class="badge">Poin Otomatis</span>
                        <h3 style="margin-top:18px">Ontime dan rajin</h3>
                        <p>Hadir tepat waktu mendapatkan poin. Terlambat atau alfa dapat mengurangi perhitungan kedisiplinan.</p>
                    </div>
                    <div class="badge gold">+5 ontime</div>
                </article>
                <article class="feature">
                    <div>
                        <span class="badge warn">Audit</span>
                        <h3 style="margin-top:18px">Log request IoT</h3>
                        <p>Request fingerprint disimpan sehingga sekolah bisa menelusuri perangkat, fingerprint ID, dan status mapping siswa.</p>
                    </div>
                </article>
                <article class="feature">
                    <div>
                        <span class="badge">Admin</span>
                        <h3 style="margin-top:18px">Token perangkat</h3>
                        <p>Admin mendaftarkan perangkat dan sistem membuat token yang disimpan di konfigurasi mesin fingerprint.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="wrap section" id="penilaian">
            <div class="section-head">
                <h2>Penilaian sikap berubah menjadi sinyal pembinaan.</h2>
                <p>Guru dapat memberi skor soft skill dan poin pencapaian. Sistem menggabungkannya dengan absensi untuk menghasilkan label yang mudah dipahami.</p>
            </div>
            <div class="feature-grid">
                <article class="feature">
                    <div>
                        <span class="badge">Sikap</span>
                        <h3 style="margin-top:18px">Disiplin, tanggung jawab, kerja sama</h3>
                        <p>Skor 1 sampai 5 dikonversi menjadi poin positif atau negatif.</p>
                    </div>
                    <table class="report-table">
                        <tr><td>Sangat baik</td><td>+5</td></tr>
                        <tr><td>Cukup</td><td>+1</td></tr>
                        <tr><td>Buruk</td><td>-5</td></tr>
                    </table>
                </article>
                <article class="feature tall">
                    <div>
                        <span class="badge gold">Gamifikasi</span>
                        <h3 style="margin-top:18px">Poin general, sikap, absen, prestasi</h3>
                        <p>Leaderboard bisa dipakai untuk achievement positif. Data negatif dipakai sebagai peringatan internal guru, bukan untuk mempermalukan siswa.</p>
                    </div>
                    <div class="timeline">
                        <div class="timeline-item"><span class="dot"></span><div><strong>Teladan</strong><span>Point general 100 ke atas.</span></div></div>
                        <div class="timeline-item"><span class="dot"></span><div><strong>Perlu Dipantau</strong><span>Point general 0 sampai 49.</span></div></div>
                        <div class="timeline-item"><span class="dot"></span><div><strong>Prioritas Perhatian</strong><span>Point general -50 ke bawah.</span></div></div>
                    </div>
                </article>
                <article class="feature">
                    <div>
                        <span class="badge">Prestasi</span>
                        <h3 style="margin-top:18px">Pencapaian kelas</h3>
                        <p>Guru bisa memberi poin untuk keaktifan, lomba, presentasi, atau perkembangan positif.</p>
                    </div>
                    <div class="badge">+20 lomba sekolah</div>
                </article>
                <article class="feature">
                    <div>
                        <span class="badge warn">Pembinaan</span>
                        <h3 style="margin-top:18px">Pelanggaran terukur</h3>
                        <p>Poin minus membantu guru dan orang tua melihat pola, bukan hanya kejadian tunggal.</p>
                    </div>
                    <div class="badge warn">-10 sampai -50</div>
                </article>
            </div>
        </section>

        <section class="wrap section" id="laporan">
            <div class="section-head">
                <h2>Laporan siap dibawa ke rapat sekolah.</h2>
                <p>Versi awal menyediakan export CSV untuk absensi dan raport sikap. Fase berikutnya bisa dinaikkan menjadi Excel format lengkap dan PDF raport sikap.</p>
            </div>
            <div class="demo-board">
                <div class="board-head">
                    <div class="board-title">
                        <strong>Preview Raport Sikap</strong>
                        <span>Export Excel-compatible CSV</span>
                    </div>
                    <div class="badge">Siap export</div>
                </div>
                <div style="padding:18px; overflow:auto">
                    <table class="report-table">
                        <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>General</th>
                            <th>Sikap</th>
                            <th>Absen</th>
                            <th>Prestasi</th>
                            <th>Label</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td>Kirana Ratri</td><td>X IPA 1</td><td>128</td><td>54</td><td>39</td><td>35</td><td><span class="badge gold">Teladan</span></td></tr>
                        <tr><td>Bagas Danendra</td><td>XI IPS 2</td><td>74</td><td>31</td><td>28</td><td>15</td><td><span class="badge">Berkembang Baik</span></td></tr>
                        <tr><td>Nabila Aksara</td><td>X IPA 3</td><td>-24</td><td>-11</td><td>-8</td><td>-5</td><td><span class="badge warn">Perlu Pembinaan</span></td></tr>
                        <tr><td>Rizky Yudhistira</td><td>IX B</td><td>-58</td><td>-22</td><td>-16</td><td>-20</td><td><span class="badge warn">Prioritas Perhatian</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="wrap">
            <div class="footer-band">
                <div>
                    <h2>Demo ini bisa langsung ditunjukkan ke client.</h2>
                    <p>Halaman ini memakai data contoh dan tidak memerlukan login.</p>
                </div>
                <a class="btn primary" href="{{ route('login') }}">Masuk ke Aplikasi</a>
            </div>
        </section>
    </main>
</div>
</body>
</html>

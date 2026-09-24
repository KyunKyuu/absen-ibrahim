<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $page->intro }}">
    <title>{{ $school->school_name }} — {{ $page->eyebrow }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,500;6..72,600&display=swap');
        :root { --pine:#123f36; --pine-2:#1e5b4e; --leaf:#b9cf72; --sand:#f2eadb; --paper:#fbfaf6; --ink:#17201d; --muted:#68736e; --line:#d9ddd5; --orange:#d96e3d; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; color:var(--ink); background:var(--paper); font-family:"DM Sans",sans-serif; -webkit-font-smoothing:antialiased; }
        body::before { content:""; position:fixed; inset:0; z-index:-1; opacity:.22; pointer-events:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.07'/%3E%3C/svg%3E"); }
        a { color:inherit; text-decoration:none; }
        .container { width:min(1180px,calc(100% - 40px)); margin-inline:auto; }
        .notice { padding:9px 20px; color:#fff; background:var(--pine); text-align:center; font-size:12px; letter-spacing:.035em; }
        .notice a { border-bottom:1px solid rgba(255,255,255,.5); margin-left:7px; }
        .site-header { position:sticky; top:0; z-index:20; background:rgba(251,250,246,.92); border-bottom:1px solid rgba(18,63,54,.1); backdrop-filter:blur(14px); }
        .nav-wrap { min-height:78px; display:flex; align-items:center; justify-content:space-between; gap:24px; }
        .identity { display:flex; align-items:center; gap:11px; font-weight:700; line-height:1.05; }
        .identity-mark { width:38px; height:42px; display:grid; place-items:center; color:#fff; background:var(--pine); border-radius:18px 18px 7px 7px; font-family:"Newsreader",serif; font-size:21px; }
        .identity small { display:block; margin-top:5px; color:var(--muted); font-size:9px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; }
        .nav-links { display:flex; align-items:center; gap:27px; font-size:13px; font-weight:600; }
        .nav-links a:hover { color:var(--pine-2); }
        .nav-login { padding:11px 17px; border:1px solid var(--pine); border-radius:999px; }
        .hero { min-height:690px; padding:70px 0 62px; overflow:hidden; }
        .hero-grid { display:grid; grid-template-columns:.92fr 1.08fr; gap:64px; align-items:center; }
        .eyebrow { display:flex; align-items:center; gap:10px; margin:0 0 22px; color:var(--pine-2); font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; }
        .eyebrow::before { content:""; width:30px; height:1px; background:currentColor; }
        h1,h2,h3,p { margin-top:0; }
        h1,h2,.quote { font-family:"Newsreader",Georgia,serif; }
        h1 { max-width:680px; margin-bottom:24px; font-size:clamp(52px,6.5vw,91px); font-weight:500; line-height:.92; letter-spacing:-.045em; }
        h1 em { color:var(--pine-2); font-style:italic; }
        .hero-copy > p:not(.eyebrow) { max-width:560px; color:var(--muted); font-size:17px; line-height:1.75; }
        .hero-actions { display:flex; gap:12px; margin-top:34px; flex-wrap:wrap; }
        .button { min-height:49px; display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:0 20px; border:1px solid var(--pine); border-radius:999px; font-size:13px; font-weight:700; transition:.2s ease; }
        .button.primary { color:#fff; background:var(--pine); }
        .button:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(18,63,54,.13); }
        .button .arrow { font-size:17px; }
        .hero-art { position:relative; min-height:570px; }
        .hero-photo { position:absolute; inset:0 18px 15px 50px; background:linear-gradient(145deg,#d9e2bc,#769c78); background-size:cover; background-position:center; border-radius:220px 220px 20px 20px; box-shadow:0 32px 70px rgba(18,63,54,.18); }
        .hero-photo::after { content:""; position:absolute; inset:0; border-radius:inherit; background:linear-gradient(to top,rgba(10,47,39,.42),transparent 45%); }
        .hero-pattern { position:absolute; width:180px; height:180px; right:-55px; top:20px; opacity:.45; background-image:linear-gradient(45deg,transparent 47%,var(--leaf) 48% 52%,transparent 53%),linear-gradient(-45deg,transparent 47%,var(--leaf) 48% 52%,transparent 53%); background-size:30px 30px; transform:rotate(8deg); }
        .hero-seal { position:absolute; left:0; bottom:0; width:130px; aspect-ratio:1; display:grid; place-content:center; padding:20px; color:#fff; background:var(--orange); border:7px solid var(--paper); border-radius:50%; text-align:center; font-family:"Newsreader",serif; font-size:18px; line-height:1.05; transform:rotate(-8deg); }
        .hero-note { position:absolute; right:0; bottom:38px; width:205px; padding:17px 18px; background:var(--paper); border:1px solid var(--line); box-shadow:0 16px 36px rgba(28,45,39,.14); font-size:12px; line-height:1.5; }
        .hero-note strong { display:block; margin-bottom:4px; color:var(--pine); font-size:15px; }
        .school-video { width:100%; padding:0 0 85px; }
        .school-video-frame { position:relative; overflow:hidden; width:100%; height:min(78vh,820px); min-height:520px; background:#102e28; }
        .school-video-frame iframe,.school-video-frame video { position:absolute; inset:0; width:100%; height:100%; border:0; }
        .school-video-title { margin:0 0 22px; font-size:clamp(32px,4vw,52px); font-weight:500; }
        .vision-mission-section { position:relative; overflow:hidden; background:#eef1e8; }
        .vision-mission-layout { display:grid; grid-template-columns:.72fr 1.28fr; gap:70px; align-items:center; }
        .vision-intro .eyebrow { color:var(--pine-2); }
        .vision-intro h2 { margin:0 0 18px; color:var(--pine); font-size:clamp(42px,5vw,68px); font-weight:500; line-height:.98; }
        .vision-intro p { color:var(--muted); line-height:1.8; }
        .vision-cards { display:grid; grid-template-columns:.8fr 1.2fr; gap:14px; }
        .vision-card,.mission-card { position:relative; min-height:260px; padding:30px; border-radius:8px; box-shadow:0 16px 40px rgba(18,63,54,.08); }
        .vision-card { display:flex; flex-direction:column; justify-content:space-between; color:#fff; background:var(--pine); }
        .mission-card { background:var(--paper); border:1px solid #d3dbcf; }
        .vision-mission h3 { margin-bottom:22px; color:var(--orange); font-size:11px; letter-spacing:.15em; text-transform:uppercase; }
        .vision-card h3 { color:var(--leaf); }
        .vision-card p { margin:0; font-family:"Newsreader",Georgia,serif; font-size:clamp(25px,2.5vw,35px); line-height:1.25; }
        .mission-card ol { display:grid; gap:13px; margin:0; padding:0; list-style:none; counter-reset:mission; }
        .mission-card li { display:grid; grid-template-columns:27px 1fr; gap:10px; color:#53625c; font-size:14px; line-height:1.65; counter-increment:mission; }
        .mission-card li::before { content:counter(mission,decimal-leading-zero); color:var(--pine-2); font-size:11px; font-weight:800; }
        .champions { position:relative; overflow:hidden; background:#f2eadb; }
        .champions .section-head h2 { color:var(--pine); }
        .champion-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; }
        .champion-card { overflow:hidden; background:var(--paper); border:1px solid #ded9ca; border-radius:8px; transition:transform .22s ease,box-shadow .22s ease; }
        .champion-card:hover { transform:translateY(-5px); box-shadow:0 18px 40px rgba(18,63,54,.12); }
        .champion-image { aspect-ratio:16/10; background:linear-gradient(135deg,#749283,#cad5aa) center/cover; }
        .champion-copy { padding:23px; }
        .champion-copy small { display:inline-flex; padding:6px 9px; color:var(--pine); background:#e6eddd; border-radius:999px; font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
        .champion-copy h3 { margin:14px 0 9px; color:var(--pine); font-family:"Newsreader",serif; font-size:27px; }
        .champion-copy p { margin:0; color:var(--muted); font-size:13px; line-height:1.7; }
        .achievement-empty { display:grid; grid-template-columns:auto 1fr; gap:18px; align-items:center; max-width:760px; padding:28px; border:1px dashed #b8c5b1; border-radius:10px; background:rgba(255,255,255,.52); }
        .achievement-mark { width:54px;height:54px;display:grid;place-items:center;border-radius:50%;color:#6e4812;background:#f3dc97;font-size:25px; }
        .achievement-empty strong { display:block;margin-bottom:5px;color:var(--pine);font-family:"Newsreader",serif;font-size:24px; }
        .achievement-empty p { margin:0;color:var(--muted);line-height:1.7; }
        .stats { display:grid; grid-template-columns:repeat(4,1fr); border-block:1px solid var(--line); }
        .stat { padding:38px 20px; text-align:center; border-right:1px solid var(--line); }
        .stat:last-child { border-right:0; }
        .stat strong { display:block; color:var(--pine); font-family:"Newsreader",serif; font-size:42px; font-weight:600; }
        .stat span { color:var(--muted); font-size:12px; letter-spacing:.08em; text-transform:uppercase; }
        .section { padding:110px 0; }
        .section-head { display:flex; align-items:end; justify-content:space-between; gap:30px; margin-bottom:48px; }
        .section-head h2 { max-width:690px; margin-bottom:0; font-size:clamp(40px,5vw,64px); font-weight:500; line-height:.98; letter-spacing:-.035em; }
        .section-head p { max-width:380px; margin-bottom:3px; color:var(--muted); line-height:1.7; }
        .program-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
        .program-card { min-height:320px; display:flex; flex-direction:column; padding:27px; background:#fff; border:1px solid var(--line); border-radius:3px; transition:.25s ease; }
        .program-card:hover { transform:translateY(-5px); border-color:#aab8af; box-shadow:0 18px 48px rgba(18,63,54,.08); }
        .program-number { width:41px; height:41px; display:grid; place-items:center; color:var(--pine); background:var(--sand); border-radius:50%; font-family:"Newsreader",serif; }
        .program-card h3 { margin:70px 0 13px; font-family:"Newsreader",serif; font-size:27px; font-weight:600; }
        .program-card p { color:var(--muted); line-height:1.7; font-size:14px; }
        .program-card .more { margin-top:auto; color:var(--pine); font-size:12px; font-weight:700; }
        .tuition { background:#eef1e8; }
        .tuition-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; align-items:stretch; }
        .tuition-card { position:relative; display:flex; flex-direction:column; min-height:440px; padding:30px; background:var(--paper); border:1px solid #d3dbcf; }
        .tuition-card.featured { color:#fff; background:var(--pine); border-color:var(--pine); transform:translateY(-10px); box-shadow:0 25px 55px rgba(18,63,54,.16); }
        .tuition-ribbon { position:absolute; top:18px; right:18px; padding:5px 9px; color:var(--pine); background:var(--leaf); border-radius:999px; font-size:9px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
        .tuition-card h3 { margin:0 0 8px; font-family:"Newsreader",serif; font-size:28px; }
        .tuition-card .tuition-desc { min-height:48px; color:var(--muted); font-size:13px; line-height:1.6; }
        .tuition-card.featured .tuition-desc { color:rgba(255,255,255,.65); }
        .price { margin:25px 0 8px; font-family:"Newsreader",serif; font-size:40px; font-weight:600; letter-spacing:-.03em; }
        .price sup { font-family:"DM Sans",sans-serif; font-size:12px; font-weight:700; vertical-align:top; }
        .price-period { padding-bottom:22px; border-bottom:1px solid var(--line); color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:.08em; }
        .tuition-card.featured .price-period { color:rgba(255,255,255,.55); border-color:rgba(255,255,255,.16); }
        .feature-list { display:grid; gap:11px; margin:24px 0 30px; padding:0; list-style:none; font-size:13px; }
        .feature-list li { display:flex; gap:9px; line-height:1.45; }
        .feature-list li::before { content:"✓"; color:var(--pine-2); font-weight:800; }
        .tuition-card.featured .feature-list li::before { color:var(--leaf); }
        .tuition-card .button { margin-top:auto; }
        .tuition-card.featured .button { color:var(--pine); background:#fff; border-color:#fff; }
        .tuition-note { margin:28px 0 0; color:var(--muted); font-size:12px; text-align:center; }
        .philosophy { position:relative; overflow:hidden; color:#fff; background:var(--pine); }
        .philosophy::after { content:""; position:absolute; width:540px; height:540px; right:-150px; top:-270px; border:1px solid rgba(255,255,255,.12); border-radius:50%; box-shadow:0 0 0 60px rgba(255,255,255,.035),0 0 0 120px rgba(255,255,255,.025); }
        .philosophy-grid { position:relative; z-index:1; display:grid; grid-template-columns:.7fr 1.3fr; gap:110px; align-items:start; }
        .arabic-mark { width:180px; height:210px; display:grid; place-items:center; border:1px solid rgba(255,255,255,.27); border-radius:90px 90px 5px 5px; font-family:serif; font-size:65px; }
        .philosophy h2 { margin-bottom:25px; font-size:clamp(43px,5vw,68px); font-weight:500; line-height:1; }
        .philosophy p { max-width:650px; color:rgba(255,255,255,.72); font-size:17px; line-height:1.8; }
        .activities { background:var(--sand); }
        .activity-grid { display:grid; grid-template-columns:repeat(12,1fr); gap:18px; }
        .activity-card { grid-column:span 4; background:var(--paper); }
        .activity-card:first-child { grid-column:span 7; }
        .activity-card:nth-child(2) { grid-column:span 5; }
        .activity-image { aspect-ratio:16/10; background:linear-gradient(135deg,#749283,#cad5aa); background-size:cover; background-position:center; filter:saturate(.82); }
        .activity-body { padding:23px; }
        .activity-body small { color:var(--orange); font-size:10px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; }
        .activity-body h3 { margin:10px 0 10px; font-family:"Newsreader",serif; font-size:25px; line-height:1.08; }
        .activity-body p { margin-bottom:0; color:var(--muted); font-size:13px; line-height:1.7; }
        .voices-grid { display:grid; grid-template-columns:.65fr 1.35fr; gap:80px; align-items:center; }
        .voices-label h2 { font-size:53px; line-height:1; font-weight:500; }
        .voices-label p { color:var(--muted); line-height:1.7; }
        .quote-card { position:relative; padding:55px; background:#fff; border:1px solid var(--line); }
        .quote-card::before { content:"“"; position:absolute; top:9px; left:30px; color:var(--leaf); font-family:"Newsreader",serif; font-size:90px; line-height:1; }
        .quote { position:relative; font-size:clamp(27px,3.2vw,40px); line-height:1.28; }
        .quote-by { margin-top:28px; font-size:13px; font-weight:700; }
        .quote-by span { display:block; margin-top:4px; color:var(--muted); font-weight:400; }
        .admission { padding:55px 0; }
        .admission-box { position:relative; overflow:hidden; display:grid; grid-template-columns:1.25fr .75fr; gap:40px; align-items:center; padding:64px; color:#fff; background:var(--orange); }
        .admission-box::after { content:""; position:absolute; width:250px; height:250px; right:-45px; bottom:-130px; border:45px solid rgba(255,255,255,.13); border-radius:50%; }
        .admission h2 { margin-bottom:15px; font-size:55px; font-weight:500; line-height:.98; }
        .admission p { max-width:650px; margin-bottom:0; color:rgba(255,255,255,.83); line-height:1.7; }
        .admission .button { position:relative; z-index:1; justify-self:end; color:var(--pine); background:#fff; border-color:#fff; }
        footer { padding:65px 0 30px; color:rgba(255,255,255,.72); background:#0c2f28; }
        .footer-grid { display:grid; grid-template-columns:1.2fr .8fr .8fr; gap:60px; }
        footer .identity { color:#fff; }
        footer .identity small { color:rgba(255,255,255,.5); }
        footer h3 { color:#fff; font-size:12px; letter-spacing:.12em; text-transform:uppercase; }
        footer p,footer a { font-size:13px; line-height:1.7; }
        footer a:hover { color:#fff; }
        .copyright { margin-top:55px; padding-top:22px; border-top:1px solid rgba(255,255,255,.12); font-size:11px; }
        @media(max-width:900px){ .nav-links a:not(.nav-login){display:none}.hero-grid,.philosophy-grid,.vision-mission-layout,.voices-grid,.admission-box{grid-template-columns:1fr}.hero{padding-top:45px}.hero-art{min-height:480px}.stats{grid-template-columns:repeat(2,1fr)}.stat:nth-child(2){border-right:0}.program-grid,.tuition-grid{grid-template-columns:1fr 1fr}.champion-grid{grid-template-columns:1fr 1fr}.activity-card,.activity-card:first-child,.activity-card:nth-child(2){grid-column:span 6}.philosophy-grid{gap:50px}.vision-mission-layout{gap:32px}.section-head{display:block}.section-head p{margin-top:18px}.admission .button{justify-self:start}.footer-grid{grid-template-columns:1fr 1fr}.footer-grid>div:first-child{grid-column:1/-1} }
        @media(max-width:620px){ .container{width:min(100% - 28px,1180px)}.notice{font-size:10px}.nav-wrap{min-height:68px}.identity{font-size:13px}.identity-mark{width:34px;height:38px}.nav-login{padding:9px 13px}.hero{min-height:auto;padding-bottom:45px}.hero-grid{gap:38px}h1{font-size:52px}.hero-art{min-height:390px}.hero-photo{inset:0 0 15px 25px}.hero-note{display:none}.hero-seal{width:105px;font-size:14px}.school-video{padding-bottom:60px}.school-video-frame{height:52vh;min-height:350px}.stats{margin-inline:-14px}.stat{padding:25px 8px}.stat strong{font-size:32px}.stat span{font-size:9px}.section{padding:76px 0}.section-head h2,.voices-label h2{font-size:42px}.program-grid,.tuition-grid,.champion-grid,.vision-cards{grid-template-columns:1fr}.vision-card,.mission-card{min-height:auto;padding:25px}.vision-card p{font-size:30px}.achievement-empty{grid-template-columns:1fr;padding:23px}.program-card{min-height:260px}.program-card h3{margin-top:43px}.tuition-card.featured{transform:none}.arabic-mark{width:130px;height:150px;font-size:48px}.activity-card,.activity-card:first-child,.activity-card:nth-child(2){grid-column:1/-1}.quote-card{padding:48px 27px 30px}.admission{padding:20px 0}.admission-box{padding:40px 27px}.admission h2{font-size:43px}.footer-grid{grid-template-columns:1fr;gap:30px}.footer-grid>div:first-child{grid-column:auto} }
    </style>
</head>
<body>
    <div class="notice">Penerimaan peserta didik baru telah dibuka. <a href="{{ $page->primary_cta_url }}">Lihat informasi →</a></div>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="identity" href="{{ route('landing') }}" aria-label="Beranda {{ $school->school_name }}">
                <span class="identity-mark">إ</span>
                <span>{{ $school->school_name }}<small>Sekolah Islam</small></span>
            </a>
            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="#tentang">Tentang</a><a href="#visi-misi">Visi & Misi</a><a href="#program">Program</a><a href="#juara">Prestasi</a><a href="#pendaftaran">Pendaftaran</a>
                <a class="nav-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Portal Sekolah' }}</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <p class="eyebrow">{{ $page->eyebrow }}</p>
                    <h1>{!! preg_replace('/(iman|ilmu|adab)/i', '<em>$1</em>', e($page->headline), 1) !!}</h1>
                    <p>{{ $page->intro }}</p>
                    <div class="hero-actions">
                        <a class="button primary" href="{{ $page->primary_cta_url }}">{{ $page->primary_cta_label }} <span class="arrow">↗</span></a>
                        <a class="button" href="{{ $page->secondary_cta_url }}">{{ $page->secondary_cta_label }}</a>
                    </div>
                </div>
                <div class="hero-art" aria-label="Suasana belajar di {{ $school->school_name }}">
                    <div class="hero-pattern"></div>
                    <div class="hero-photo" @if($page->hero_image_url) style="background-image:url('{{ $page->hero_image_url }}')" @endif></div>
                    <div class="hero-seal">Ilmu yang<br>bermanfaat</div>
                    <div class="hero-note"><strong>Berakar, lalu bertumbuh.</strong>Pendidikan yang menjaga nilai dan menyiapkan masa depan.</div>
                </div>
            </div>
        </section>

        @php
                $videoUrl = $page->video_url ?: 'https://www.youtube.com/watch?v=aqz-KE-bpKQ';
                $videoTitle = $page->video_title ?: 'Video Profil Sekolah (Contoh)';
                $videoEmbed = null;
                if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~', $videoUrl, $videoMatch)) {
                    $videoEmbed = 'https://www.youtube-nocookie.com/embed/'.$videoMatch[1];
                } elseif (preg_match('~vimeo\.com/(?:video/)?([0-9]+)~', $videoUrl, $videoMatch)) {
                    $videoEmbed = 'https://player.vimeo.com/video/'.$videoMatch[1];
                }
        @endphp
        <section class="school-video" aria-label="Video sekolah">
                <div class="container"><h2 class="school-video-title">{{ $videoTitle }}</h2></div>
                <div class="school-video-frame">
                    @if($videoEmbed)<iframe src="{{ $videoEmbed }}" title="{{ $videoTitle }}" loading="eager" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    @elseif(str_ends_with(strtolower(parse_url($videoUrl, PHP_URL_PATH) ?? ''), '.mp4'))<video controls preload="metadata"><source src="{{ $videoUrl }}" type="video/mp4">Browser Anda tidak mendukung video.</video>
                    @else<a href="{{ $videoUrl }}" target="_blank" rel="noopener" style="position:absolute;inset:0;display:grid;place-items:center;color:white">Tonton video sekolah ↗</a>@endif
                </div>
        </section>

        @if($statistics->isNotEmpty())
            <div class="container stats" aria-label="Statistik sekolah">
                @foreach($statistics->take(4) as $stat)
                    <div class="stat"><strong>{{ $stat->title }}</strong><span>{{ $stat->kicker }}</span></div>
                @endforeach
            </div>
        @endif

        <section class="section" id="program">
            <div class="container">
                <div class="section-head"><h2>Belajar utuh, bertumbuh dengan arah.</h2><p>Program dirancang agar ilmu, kebiasaan baik, dan keberanian anak berkembang dalam keseharian.</p></div>
                <div class="program-grid">
                    @forelse($programs as $program)
                        <article class="program-card"><span class="program-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $program->title }}</h3><p>{{ $program->body }}</p>@if($program->link_url)<a class="more" href="{{ $program->link_url }}">Pelajari program →</a>@endif</article>
                    @empty
                        <article class="program-card"><span class="program-number">01</span><h3>Program sekolah sedang disiapkan</h3><p>Informasi lengkap akan segera hadir.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

        @if($tuitionPackages->isNotEmpty())
            <section class="section tuition" id="biaya">
                <div class="container">
                    <div class="section-head"><h2>Biaya pendidikan yang jelas sejak awal.</h2><p>Pilih skema yang paling sesuai. Rincian akhir tetap mengikuti jenjang dan tahun ajaran berjalan.</p></div>
                    <div class="tuition-grid">
                        @foreach($tuitionPackages as $package)
                            <article class="tuition-card {{ $package->is_featured ? 'featured' : '' }}">
                                @if($package->is_featured)<span class="tuition-ribbon">Paling diminati</span>@endif
                                <h3>{{ $package->name }}</h3>
                                <p class="tuition-desc">{{ $package->description }}</p>
                                <div class="price"><sup>Rp</sup>{{ number_format($package->price, 0, ',', '.') }}</div>
                                <div class="price-period">{{ $package->billing_period }}</div>
                                <ul class="feature-list">@foreach($package->features ?? [] as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                                <a class="button" href="{{ $package->cta_url ?: $page->primary_cta_url }}">{{ $package->cta_label }} <span class="arrow">↗</span></a>
                            </article>
                        @endforeach
                    </div>
                    <p class="tuition-note">Nominal dapat berubah sesuai kebijakan sekolah. Hubungi tim admisi untuk simulasi dan rincian lengkap.</p>
                </div>
            </section>
        @endif

        <section class="section philosophy" id="tentang">
            <div class="container philosophy-grid">
                <div class="arabic-mark" aria-hidden="true">اقرأ</div>
                <div><p class="eyebrow" style="color:var(--leaf)">Tentang kami</p><h2>{{ $page->about_title }}</h2><p>{{ $page->about_body }}</p></div>
            </div>
        </section>

        @php
            $visionText = $page->vision ?: \App\Models\LandingPage::defaults()['vision'];
            $missionText = $page->mission ?: \App\Models\LandingPage::defaults()['mission'];
        @endphp
        <section class="section vision-mission-section" id="visi-misi">
            <div class="container vision-mission-layout">
                <div class="vision-intro"><p class="eyebrow">Arah pendidikan</p><h2>Berjalan dengan tujuan.</h2><p>Setiap proses belajar punya arah yang jelas: membantu anak bertumbuh dengan iman, ilmu, dan kepedulian.</p></div>
                <div class="vision-cards">
                    <article class="vision-card"><div><h3>Visi sekolah</h3><p>{{ $visionText }}</p></div><span aria-hidden="true" style="align-self:end;color:var(--leaf);font-size:28px">✳</span></article>
                    <article class="mission-card"><h3>Misi kami</h3><ol>@foreach(preg_split('/\r\n|\r|\n/', $missionText) as $missionLine) @if(trim($missionLine))<li>{{ trim($missionLine) }}</li>@endif @endforeach</ol></article>
                </div>
            </div>
        </section>

        @if($activities->isNotEmpty())
            <section class="section activities" id="kegiatan">
                <div class="container">
                    <div class="section-head"><h2>Kabar dari lingkungan belajar.</h2><p>Catatan kecil tentang karya, perjalanan, dan keseharian warga sekolah.</p></div>
                    <div class="activity-grid">
                        @foreach($activities->take(5) as $activity)
                            <article class="activity-card">
                                <div class="activity-image" @if($activity->image_url) style="background-image:url('{{ $activity->image_url }}')" @endif></div>
                                <div class="activity-body"><small>{{ $activity->kicker ?: $activity->published_at?->translatedFormat('d F Y') }}</small><h3>{{ $activity->title }}</h3><p>{{ $activity->body }}</p></div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="section champions" id="juara">
            <div class="container">
                <div class="section-head"><h2>Prestasi siswa.</h2><p>Merayakan proses, karya, dan pencapaian siswa di bidang akademik maupun nonakademik.</p></div>
                @if($champions->isNotEmpty())
                    <div class="champion-grid">
                        @foreach($champions->take(6) as $champion)
                            <article class="champion-card">
                                @if($champion->image_url)<div class="champion-image" style="background-image:url('{{ $champion->image_url }}')"></div>@endif
                                <div class="champion-copy"><small>{{ $champion->kicker ?: 'Prestasi siswa' }}</small><h3>{{ $champion->title }}</h3><p>{{ $champion->body }}</p></div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="achievement-empty"><span class="achievement-mark" aria-hidden="true">✦</span><div><strong>Ruang untuk cerita prestasi berikutnya</strong><p>Prestasi dan karya siswa akan ditampilkan di sini. Tim sekolah dapat menambahkan pencapaian terbaru melalui dashboard.</p></div></div>
                @endif
            </div>
        </section>

        @if($testimonials->isNotEmpty())
            <section class="section">
                <div class="container voices-grid">
                    <div class="voices-label"><p class="eyebrow">Suara keluarga</p><h2>Kepercayaan yang kami jaga.</h2><p>Sekolah dan keluarga berjalan beriringan dalam setiap proses tumbuh anak.</p></div>
                    <div class="quote-card"><p class="quote">{{ $testimonials->first()->body }}</p><div class="quote-by">{{ $testimonials->first()->title }}<span>{{ $testimonials->first()->kicker }}</span></div></div>
                </div>
            </section>
        @endif

        <section class="admission" id="pendaftaran">
            <div class="container admission-box">
                <div><h2>{{ $page->admission_title }}</h2><p>{{ $page->admission_body }}</p></div>
                @if($page->whatsapp)<a class="button" href="https://wa.me/{{ preg_replace('/\D/', '', $page->whatsapp) }}">Hubungi admisi <span class="arrow">↗</span></a>@else<a class="button" href="{{ $page->primary_cta_url }}">{{ $page->primary_cta_label }} <span class="arrow">↗</span></a>@endif
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div><div class="identity"><span class="identity-mark">إ</span><span>{{ $school->school_name }}<small>Sekolah Islam</small></span></div><p style="max-width:390px;margin-top:20px">{{ $page->intro }}</p></div>
                <div><h3>Alamat</h3><p>{{ $page->address ?: 'Alamat sekolah dapat diperbarui melalui panel superadmin.' }}</p>@if($page->email)<a href="mailto:{{ $page->email }}">{{ $page->email }}</a>@endif</div>
                <div><h3>Tautan</h3><p><a href="#program">Program sekolah</a><br><a href="#kegiatan">Kabar & kegiatan</a><br><a href="{{ route('login') }}">Portal sekolah</a>@if($page->instagram_url)<br><a href="{{ $page->instagram_url }}">Instagram ↗</a>@endif</p></div>
            </div>
            <div class="copyright">© {{ date('Y') }} {{ $school->school_name }}. Pendidikan, adab, dan masa depan.</div>
        </div>
    </footer>
</body>
</html>

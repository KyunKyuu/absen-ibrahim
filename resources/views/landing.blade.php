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
        /* School Video Section - Cinema Pavilion */
        .school-video-section { position:relative; overflow:hidden; padding:90px 0 115px; background:radial-gradient(ellipse 80% 50% at 50% 15%, rgba(185,207,114,.14) 0%, rgba(242,234,219,.32) 50%, transparent 80%); border-bottom:1px solid var(--line); }
        .school-video-container { width:min(1280px,calc(100% - 36px)); margin-inline:auto; }
        .school-video-head { display:flex; align-items:flex-end; justify-content:space-between; gap:40px; margin-bottom:38px; }
        .school-video-title { margin:0; font-size:clamp(36px,4.5vw,56px); font-weight:500; line-height:1.02; letter-spacing:-.035em; color:var(--pine); max-width:620px; }
        .school-video-desc { max-width:440px; margin:0; color:var(--muted); line-height:1.75; font-size:14.5px; }

        .cinema-theater { position:relative; width:100%; background:#fff; border:1px solid var(--line); border-radius:28px; padding:12px; box-shadow:0 30px 80px rgba(18,63,54,.11),0 6px 20px rgba(18,63,54,.04); }
        .cinema-console-bar { display:flex; align-items:center; justify-content:space-between; padding:10px 16px 12px; }
        .console-left { display:flex; align-items:center; gap:8px; font-size:11px; font-weight:700; color:var(--muted); letter-spacing:.06em; }
        .console-dot { width:9px; height:9px; border-radius:50%; display:inline-block; }
        .console-dot.red { background:#ff5f56; }
        .console-dot.yellow { background:#ffbd2e; }
        .console-dot.green { background:#27c93f; }
        .console-title { margin-left:6px; color:var(--pine); font-size:11px; text-transform:uppercase; letter-spacing:.09em; }

        .school-video-frame { position:relative; width:100%; aspect-ratio:16/9; overflow:hidden; border-radius:18px; background:#0b221d; box-shadow:inset 0 0 0 1px rgba(255,255,255,.08); }
        .school-video-frame iframe,.school-video-frame video { position:absolute; inset:0; width:100%; height:100%; border:0; display:block; }
        .school-video-fallback { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:24px; text-align:center; color:#fff; background:linear-gradient(145deg,var(--pine),var(--pine-2)); }
        .school-video-fallback p { margin:0; color:rgba(255,255,255,.85); font-size:15px; }
        .school-video-fallback .button { background:#fff; color:var(--pine); border-color:#fff; }

        .cinema-features-strip { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; padding:16px 8px 6px; }
        .cinema-feature-item { display:flex; align-items:center; gap:14px; padding:12px 16px; background:#faf9f5; border:1px solid rgba(217,221,213,.75); border-radius:14px; }
        .cinema-feature-item .feature-num { width:30px; height:30px; border-radius:8px; background:var(--sand); color:var(--pine); display:grid; place-items:center; font-size:11px; font-weight:800; flex-shrink:0; }
        .cinema-feature-item strong { display:block; color:var(--pine); font-size:13px; font-weight:700; line-height:1.2; }
        .cinema-feature-item small { display:block; color:var(--muted); font-size:11.5px; margin-top:2px; line-height:1.4; }

        .cinema-seal { position:absolute; right:-20px; top:-24px; width:94px; height:94px; border-radius:50%; background:var(--orange); color:#fff; border:5px solid var(--paper); box-shadow:0 12px 30px rgba(217,110,61,.28); display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; font-family:"Newsreader",serif; font-size:12px; line-height:1.05; transform:rotate(12deg); z-index:3; pointer-events:none; }
        .cinema-seal .seal-icon { font-size:15px; margin-bottom:2px; color:var(--leaf); }
        .vision-mission-section { position:relative; overflow:hidden; background:#eef1e8; }
        .vision-mission-layout { display:grid; grid-template-columns:.75fr 1.25fr; gap:56px; align-items:center; }
        .vision-intro .eyebrow { color:var(--pine-2); }
        .vision-intro h2 { margin:0 0 18px; color:var(--pine); font-size:clamp(42px,5vw,68px); font-weight:500; line-height:.98; }
        .vision-intro p { color:var(--muted); line-height:1.8; margin-bottom:0; }
        .vision-cards { display:grid; grid-template-columns:1fr 1.08fr; gap:20px; align-items:stretch; }
        .vision-card,.mission-card { position:relative; padding:34px 30px; border-radius:22px; display:flex; flex-direction:column; justify-content:space-between; }
        .vision-card { color:#fff; background:linear-gradient(150deg,#123f36 0%,#0d2f28 100%); border:1px solid rgba(185,207,114,.25); box-shadow:0 20px 48px rgba(18,63,54,.14); }
        .vision-card p { margin:24px 0 28px; font-family:"Newsreader",Georgia,serif; font-size:clamp(22px,2vw,28px); line-height:1.4; color:#fff; }
        .vision-card-footer { display:flex; align-items:center; justify-content:space-between; margin-top:auto; padding-top:18px; border-top:1px solid rgba(255,255,255,.12); }
        .vision-seal { color:var(--leaf); font-size:26px; line-height:1; }
        .vision-tagline { color:rgba(255,255,255,.6); font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }

        .mission-card { background:#fff; border:1px solid var(--line); box-shadow:0 20px 48px rgba(18,63,54,.07); }
        .mission-list { display:grid; gap:12px; margin:22px 0 20px; padding:0; list-style:none; }
        .mission-item { display:flex; align-items:flex-start; gap:14px; padding:13px 15px; background:#faf9f5; border:1px solid rgba(217,221,213,.7); border-radius:14px; transition:transform .2s ease,border-color .2s ease,background .2s ease; }
        .mission-item:hover { transform:translateX(3px); background:#fff; border-color:var(--pine-2); }
        .mission-number { min-width:28px; height:28px; border-radius:8px; background:var(--sand); color:var(--pine); font-size:11px; font-weight:800; display:grid; place-items:center; }
        .mission-text { color:#354540; font-size:13.5px; line-height:1.55; font-weight:500; }
        .mission-card-footer { margin-top:auto; padding-top:14px; border-top:1px solid var(--line); }
        .mission-subnote { color:var(--muted); font-size:11px; line-height:1.5; font-style:italic; }

        .card-pill { display:inline-flex; align-items:center; gap:7px; padding:5px 12px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; width:fit-content; }
        .card-pill.leaf { color:var(--leaf); background:rgba(185,207,114,.15); border:1px solid rgba(185,207,114,.3); }
        .card-pill.pine { color:var(--pine); background:rgba(18,63,54,.08); border:1px solid rgba(18,63,54,.15); }
        .card-pill.orange { color:var(--orange); background:rgba(217,110,61,.12); border:1px solid rgba(217,110,61,.25); }
        .card-pill .pill-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

        .champions { position:relative; overflow:hidden; background:var(--paper); border-top:1px solid var(--line); }
        .champions .section-head h2 { color:var(--pine); }
        .champion-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; }
        .champion-card { display:flex; flex-direction:column; overflow:hidden; background:#fff; border:1px solid var(--line); border-radius:20px; box-shadow:0 12px 32px rgba(18,63,54,.06); transition:transform .22s ease,box-shadow .22s ease; }
        .champion-card:hover { transform:translateY(-4px); box-shadow:0 20px 44px rgba(18,63,54,.12); }
        .champion-image { aspect-ratio:16/10; background:linear-gradient(135deg,#749283,#cad5aa) center/cover; }
        .champion-copy { padding:22px 24px; display:flex; flex-direction:column; flex:1; }
        .champion-copy small { display:inline-flex; width:fit-content; padding:5px 10px; color:var(--pine); background:#e6eddd; border-radius:999px; font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:12px; }
        .champion-copy h3 { margin:0 0 8px; color:var(--pine); font-family:"Newsreader",serif; font-size:25px; line-height:1.2; }
        .champion-copy p { margin:0; color:var(--muted); font-size:13px; line-height:1.7; }

        /* Interactive Turning Book Section */
        .book-showcase-section { position:relative; overflow:hidden; background:linear-gradient(180deg, #f7f3ea 0%, #eee5d3 50%, #f7f3ea 100%); border-top:1px solid var(--line); border-bottom:1px solid var(--line); padding:95px 0 105px; }
        .book-showcase-head { display:flex; align-items:flex-end; justify-content:space-between; gap:28px; margin-bottom:34px; }
        .book-showcase-head h2 { color:var(--pine); margin:0; font-size:clamp(36px,4.5vw,56px); font-weight:500; line-height:1; }
        .book-controls-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .book-badge-pill { display:inline-flex; align-items:center; gap:7px; padding:6px 14px; background:#fff; border:1px solid #dcd4c3; border-radius:999px; font-size:11.5px; font-weight:700; color:var(--pine); box-shadow:0 3px 10px rgba(0,0,0,0.03); }
        .book-badge-pill .pulse-live { width:7px; height:7px; border-radius:50%; background:#2bb673; box-shadow:0 0 0 3px rgba(43,182,115,.2); }
        .book-btn-toggle { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; background:#fff; border:1px solid #dcd4c3; border-radius:999px; font-size:11.5px; font-weight:700; color:var(--pine); cursor:pointer; transition:all .2s ease; box-shadow:0 3px 10px rgba(0,0,0,0.03); }
        .book-btn-toggle:hover { background:var(--pine); color:#fff; border-color:var(--pine); transform:translateY(-1px); }

        .book-stage-wrapper { position:relative; max-width:1080px; margin:0 auto; perspective:2000px; }
        .grand-book-folio {
            position:relative;
            background:#143e35;
            border-radius:24px;
            padding:16px;
            box-shadow:0 38px 95px rgba(18,63,54,.25), 0 14px 35px rgba(0,0,0,.12), 0 0 35px rgba(45,226,166,0.18);
            border:2px solid #caa867;
            transform-style:preserve-3d;
            transition:transform 0.15s ease-out;
            will-change:transform;
        }
        .grand-book-folio::before { content:""; position:absolute; inset:6px; border:1px dashed rgba(202,168,103,.4); border-radius:18px; pointer-events:none; }
        
        /* 3D Floating Stickers */
        .book-3d-sticker {
            position:absolute;
            z-index:20;
            pointer-events:none;
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 14px;
            border-radius:999px;
            font-size:11px;
            font-weight:800;
            letter-spacing:.05em;
            text-transform:uppercase;
            box-shadow:0 8px 18px rgba(0,0,0,.25);
            transform:translateZ(30px);
        }
        .book-3d-sticker.badge-gold { top:-14px; right:40px; background:#f4c444; color:#2b1d03; transform:translateZ(32px) rotate(3deg); }
        .book-3d-sticker.badge-mint { top:-14px; left:40px; background:#2de2a6; color:#061512; transform:translateZ(32px) rotate(-4deg); }

        .book-corner-seal { position:absolute; width:24px; height:24px; border:2.5px solid #caa867; pointer-events:none; z-index:12; transform:translateZ(10px); }
        .book-corner-seal.tl { top:8px; left:8px; border-right:none; border-bottom:none; border-top-left-radius:10px; }
        .book-corner-seal.tr { top:8px; right:8px; border-left:none; border-bottom:none; border-top-right-radius:10px; }
        .book-corner-seal.bl { bottom:8px; left:8px; border-right:none; border-top:none; border-bottom-left-radius:10px; }
        .book-corner-seal.br { bottom:8px; right:8px; border-left:none; border-top:none; border-bottom-right-radius:10px; }

        /* 3D Center Spine with Metallic Rings */
        .book-3d-rings-spine {
            position:absolute;
            top:18px;
            bottom:18px;
            left:50%;
            width:34px;
            transform:translateX(-50%) translateZ(12px);
            z-index:15;
            pointer-events:none;
            display:flex;
            flex-direction:column;
            justify-content:space-evenly;
            align-items:center;
        }
        .book-ring-metal {
            width:28px;
            height:10px;
            border-radius:5px;
            background:linear-gradient(180deg, #ffffff 0%, #b8bfc4 45%, #4a545a 100%);
            box-shadow:0 3px 8px rgba(0,0,0,0.4), inset 0 1px 2px rgba(255,255,255,0.8);
            border:1px solid #78858e;
            transform:rotate(-3deg);
        }

        .book-center-spine { position:absolute; top:16px; bottom:16px; left:50%; width:28px; transform:translateX(-50%); background:linear-gradient(90deg, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0.52) 50%, rgba(0,0,0,0.18) 100%); z-index:10; pointer-events:none; border-radius:4px; box-shadow:inset 0 0 6px rgba(0,0,0,0.3); }

        .book-spreads-viewport { position:relative; min-height:580px; background:#fbf9f4; border-radius:14px; overflow:hidden; box-shadow:inset 0 0 35px rgba(0,0,0,.04); transform-style:preserve-3d; }
        .book-spread-card { position:absolute; inset:0; display:grid; grid-template-columns:1fr 1fr; opacity:0; pointer-events:none; transform:translateX(30px) scale(0.98); transition:opacity .45s cubic-bezier(0.2,0.8,0.2,1), transform .45s cubic-bezier(0.2,0.8,0.2,1); }
        .book-spread-card.active { opacity:1; pointer-events:auto; transform:translateX(0) scale(1); z-index:4; position:relative; }
        .book-spread-card.flip-out-left { opacity:0; transform:translateX(-40px) scale(0.96); }
        .book-spread-card.flip-in-right { opacity:1; transform:translateX(0) scale(1); }

        .book-page-leaf { position:relative; padding:38px 42px; display:flex; flex-direction:column; justify-content:space-between; background:#fdfbf7; }
        .book-page-leaf.leaf-left { border-right:1px solid rgba(0,0,0,.07); background:linear-gradient(90deg, #fdfbf7 0%, #fbf7ee 95%, #ebddc6 100%); }
        .book-page-leaf.leaf-right { background:linear-gradient(90deg, #ebddc6 0%, #fbf7ee 5%, #fdfbf7 100%); }

        .leaf-top-bar { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; padding-bottom:12px; border-bottom:1px dashed #ded4bf; }
        .leaf-kicker-tag { display:inline-flex; align-items:center; gap:6px; padding:4px 11px; border-radius:999px; font-size:10.5px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; background:rgba(217,110,61,.12); color:var(--orange); border:1px solid rgba(217,110,61,.25); }
        .leaf-kicker-tag.gold { background:rgba(212,163,75,.15); color:#966a1a; border-color:rgba(212,163,75,.35); }
        .leaf-kicker-tag.pine { background:rgba(18,63,54,.09); color:var(--pine); border-color:rgba(18,63,54,.2); }
        .leaf-kicker-tag.leaf { background:rgba(185,207,114,.22); color:#4f631b; border-color:rgba(185,207,114,.45); }
        .leaf-crest-mark { font-size:13px; color:#b89355; font-family:"Newsreader",serif; font-style:italic; }

        .leaf-photo-frame { position:relative; border-radius:14px; overflow:hidden; aspect-ratio:16/9; margin-bottom:18px; background:linear-gradient(135deg,#749283,#cad5aa) center/cover; border:2px solid #ebdcc5; box-shadow:0 8px 20px rgba(18,63,54,.07); transition:transform .3s ease; }
        .leaf-photo-frame:hover { transform:scale(1.02); }
        .leaf-photo-frame .photo-stamp { position:absolute; bottom:10px; right:10px; padding:4px 10px; background:rgba(18,63,54,.88); color:#fff; border-radius:999px; font-size:10px; font-weight:700; backdrop-filter:blur(4px); }
        
        .leaf-seal-art { aspect-ratio:16/9; margin-bottom:18px; border-radius:14px; background:linear-gradient(135deg, #18463c 0%, #0d2c25 100%); border:1px solid rgba(202,168,103,.35); display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:18px; color:#fff; box-shadow:0 8px 20px rgba(18,63,54,.09); }
        .leaf-seal-art .seal-symbol { font-size:38px; line-height:1; margin-bottom:8px; filter:drop-shadow(0 4px 10px rgba(0,0,0,.25)); }
        .leaf-seal-art strong { font-family:"Newsreader",serif; font-size:20px; color:#f6e5c5; font-weight:500; }
        .leaf-seal-art small { font-size:11px; color:rgba(255,255,255,.65); letter-spacing:.05em; text-transform:uppercase; margin-top:3px; }

        .leaf-story-title { margin:0 0 10px; color:var(--pine); font-family:"Newsreader",serif; font-size:clamp(22px,2vw,27px); font-weight:600; line-height:1.2; }
        .leaf-story-body { margin:0 0 16px; color:#495752; font-size:13.5px; line-height:1.7; flex:1; }

        .leaf-bottom-bar { display:flex; align-items:center; justify-content:space-between; margin-top:auto; padding-top:14px; border-top:1px solid #ebdcc5; }
        .leaf-page-num { font-family:"Newsreader",serif; font-style:italic; font-size:12px; color:#8c9994; }
        .leaf-cheer-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 13px; background:#fff; border:1px solid #d9d0be; border-radius:999px; font-size:11.5px; font-weight:700; color:var(--pine); cursor:pointer; transition:all .2s ease; box-shadow:0 2px 6px rgba(0,0,0,.03); }
        .leaf-cheer-btn:hover { background:var(--sand); border-color:var(--orange); color:var(--orange); transform:scale(1.04); }

        .book-float-arrow { position:absolute; top:50%; transform:translateY(-50%); width:48px; height:48px; border-radius:50%; background:#fff; border:2px solid #caa867; color:var(--pine); font-size:24px; font-weight:bold; display:grid; place-items:center; cursor:pointer; z-index:15; box-shadow:0 10px 25px rgba(0,0,0,.15); transition:all .2s ease; }
        .book-float-arrow:hover { background:var(--pine); color:#caa867; transform:translateY(-50%) scale(1.08); box-shadow:0 15px 32px rgba(18,63,54,.25); }
        .book-float-arrow.prev { left:-24px; }
        .book-float-arrow.next { right:-24px; }

        .book-footer-nav { display:flex; align-items:center; justify-content:space-between; margin-top:24px; flex-wrap:wrap; gap:16px; }
        .book-dots-container { display:flex; align-items:center; gap:8px; }
        .book-page-dot { width:10px; height:10px; border-radius:50%; background:#d2c6b3; border:none; cursor:pointer; transition:all .24s ease; padding:0; }
        .book-page-dot.active { width:28px; border-radius:999px; background:var(--pine); }
        .book-counter-label { font-size:12.5px; font-weight:700; color:#5b6964; }

        @media(max-width:850px){
            .book-showcase-head { display:block; }
            .book-controls-bar { margin-top:16px; justify-content:flex-start; }
            .grand-book-folio { padding:10px; border-radius:18px; }
            .book-center-spine { display:none; }
            .book-spread-card { grid-template-columns:1fr; }
            .book-page-leaf.leaf-left { border-right:none; border-bottom:1px solid #ebdcc5; }
            .book-float-arrow { width:38px; height:38px; font-size:18px; }
            .book-float-arrow.prev { left:-10px; }
            .book-float-arrow.next { right:-10px; }
            .book-page-leaf { padding:24px 20px; }
        }

        .achievement-showcase { background:#fff; border:1px solid var(--line); border-radius:24px; padding:38px 40px; box-shadow:0 20px 50px rgba(18,63,54,.07); }
        .achievement-showcase-grid { display:grid; grid-template-columns:1.35fr .85fr; gap:44px; align-items:center; }
        .achievement-showcase-copy h3 { margin:16px 0 12px; color:var(--pine); font-family:"Newsreader",serif; font-size:clamp(27px,2.8vw,36px); font-weight:500; line-height:1.15; letter-spacing:-.03em; }
        .achievement-showcase-copy p { color:var(--muted); font-size:14.5px; line-height:1.75; margin:0 0 26px; }
        .achievement-pillars { display:grid; gap:12px; }
        .achievement-pillar { display:flex; align-items:center; gap:14px; padding:12px 16px; background:#faf9f5; border:1px solid rgba(217,221,213,.7); border-radius:14px; }
        .pillar-symbol { width:34px; height:34px; border-radius:10px; background:var(--sand); font-size:16px; display:grid; place-items:center; flex-shrink:0; }
        .achievement-pillar strong { display:block; color:var(--pine); font-size:13.5px; font-weight:700; }
        .achievement-pillar small { display:block; color:var(--muted); font-size:12px; margin-top:2px; }

        .achievement-showcase-aside { height:100%; display:flex; }
        .achievement-badge-card { width:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:32px 26px; background:linear-gradient(155deg,#faf8f3 0%,#f1ebe0 100%); border:1px solid #ded6c5; border-radius:20px; }
        .trophy-mark { width:64px; height:64px; border-radius:50%; background:#fff; color:var(--orange); display:grid; place-items:center; font-size:28px; box-shadow:0 10px 24px rgba(217,110,61,.15); margin-bottom:18px; }
        .achievement-badge-card h4 { margin:0 0 8px; color:var(--pine); font-family:"Newsreader",serif; font-size:23px; font-weight:600; }
        .achievement-badge-card p { margin:0 0 20px; color:var(--muted); font-size:13px; line-height:1.65; max-width:260px; }
        .achievement-status { display:inline-flex; align-items:center; gap:8px; padding:7px 14px; background:#fff; border:1px solid #ded6c5; border-radius:999px; font-size:11px; font-weight:700; color:var(--pine); }
        .pulse-dot { width:8px; height:8px; border-radius:50%; background:#2bb673; box-shadow:0 0 0 3px rgba(43,182,115,.2); }
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
        .activity-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; }
        .activity-card { display:flex; flex-direction:column; background:#fff; border:1px solid #ded9ca; border-radius:20px; overflow:hidden; box-shadow:0 12px 30px rgba(18,63,54,.06); transition:transform .22s ease,box-shadow .22s ease; }
        .activity-card:hover { transform:translateY(-4px); box-shadow:0 20px 44px rgba(18,63,54,.12); }
        .activity-image { aspect-ratio:16/10; background:linear-gradient(135deg,#749283,#cad5aa) center/cover; }
        .activity-body { padding:22px 24px; display:flex; flex-direction:column; flex:1; }
        .activity-body small { color:var(--orange); font-size:10px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; margin-bottom:8px; }
        .activity-body h3 { margin:0 0 10px; font-family:"Newsreader",serif; font-size:24px; line-height:1.2; color:var(--pine); }
        .activity-body p { margin-top:auto; margin-bottom:0; color:var(--muted); font-size:13px; line-height:1.7; }
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
        @media(max-width:900px){ .nav-links a:not(.nav-login){display:none}.hero-grid,.philosophy-grid,.vision-mission-layout,.voices-grid,.admission-box{grid-template-columns:1fr}.hero{padding-top:45px}.hero-art{min-height:480px}.stats{grid-template-columns:repeat(2,1fr)}.stat:nth-child(2){border-right:0}.program-grid,.tuition-grid{grid-template-columns:1fr 1fr}.champion-grid{grid-template-columns:1fr 1fr}.activity-grid{grid-template-columns:1fr 1fr}.achievement-showcase-grid{grid-template-columns:1fr;gap:32px}.school-video-head{display:block}.school-video-head p{margin-top:18px;max-width:100%}.cinema-features-strip{grid-template-columns:1fr;gap:10px}.cinema-seal{display:none}.philosophy-grid{gap:50px}.vision-mission-layout{gap:32px}.section-head{display:block}.section-head p{margin-top:18px}.admission .button{justify-self:start}.footer-grid{grid-template-columns:1fr 1fr}.footer-grid>div:first-child{grid-column:1/-1} }
        @media(max-width:620px){ .container{width:min(100% - 28px,1180px)}.school-video-container{width:min(100% - 24px,1280px)}.notice{font-size:10px}.nav-wrap{min-height:68px}.identity{font-size:13px}.identity-mark{width:34px;height:38px}.nav-login{padding:9px 13px}.hero{min-height:auto;padding-bottom:45px}.hero-grid{gap:38px}h1{font-size:52px}.hero-art{min-height:390px}.hero-photo{inset:0 0 15px 25px}.hero-note{display:none}.hero-seal{width:105px;font-size:14px}.school-video-section{padding:50px 0 70px}.school-video-head{margin-bottom:22px}.cinema-theater{padding:8px;border-radius:20px}.cinema-console-bar{padding:6px 10px 8px}.console-title{display:none}.school-video-frame{border-radius:13px}.cinema-features-strip{padding:10px 4px 4px}.cinema-feature-item{padding:10px 12px}.stats{margin-inline:-14px}.stat{padding:25px 8px}.stat strong{font-size:32px}.stat span{font-size:9px}.section{padding:76px 0}.section-head h2,.voices-label h2{font-size:42px}.program-grid,.tuition-grid,.champion-grid,.vision-cards{grid-template-columns:1fr}.vision-card,.mission-card{padding:24px 20px;border-radius:18px}.achievement-showcase{padding:24px 18px;border-radius:18px}.achievement-showcase-grid{gap:24px}.activity-grid{grid-template-columns:1fr}.program-card{min-height:260px}.program-card h3{margin-top:43px}.tuition-card.featured{transform:none}.arabic-mark{width:130px;height:150px;font-size:48px}.quote-card{padding:48px 27px 30px}.admission{padding:20px 0}.admission-box{padding:40px 27px}.admission h2{font-size:43px}.footer-grid{grid-template-columns:1fr;gap:30px}.footer-grid>div:first-child{grid-column:auto} }
    </style>
</head>
<body>
    <div class="notice">
        <span>Penerimaan peserta didik baru telah dibuka. <a href="{{ $page->primary_cta_url }}">Lihat informasi →</a></span>
        <a href="{{ route('landing.story') }}" style="display:inline-flex;align-items:center;gap:6px;margin-left:14px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);padding:3px 12px;border-radius:999px;font-weight:700;color:#fff;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.28)'" onmouseout="this.style.background='rgba(255,255,255,.18)'">📖 Mode Buku Cerita & Petualangan ✨</a>
    </div>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="identity" href="{{ route('landing') }}" aria-label="Beranda {{ $school->school_name }}">
                <span class="identity-mark">إ</span>
                <span>{{ $school->school_name }}<small>Sekolah Islam</small></span>
            </a>
            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="#tentang">Tentang</a><a href="#visi-misi">Visi & Misi</a><a href="#program">Program</a><a href="#juara">Prestasi</a><a href="#pendaftaran">Pendaftaran</a>
                <a href="{{ route('landing.story') }}" style="color:var(--orange);font-weight:700;">📖 Buku Cerita</a>
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

        @if($statistics->isNotEmpty())
            <div class="container stats" aria-label="Statistik sekolah">
                @foreach($statistics->take(4) as $stat)
                    <div class="stat"><strong>{{ $stat->title }}</strong><span>{{ $stat->kicker }}</span></div>
                @endforeach
            </div>
        @endif

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
        <section class="school-video-section" id="video-profil" aria-label="Video profil sekolah">
            <div class="school-video-container">
                <div class="section-head school-video-head">
                    <div>
                        <p class="eyebrow">Profil Sekolah</p>
                        <h2 class="school-video-title">{{ $videoTitle }}</h2>
                    </div>
                    <p class="school-video-desc">Saksikan sekilas denyut keseharian di {{ $school->school_name }}—mulai dari pembiasaan adab, pembelajaran aktif, hingga kehangatan interaksi guru dan para murid.</p>
                </div>
                <div class="cinema-theater">
                    <div class="cinema-console-bar">
                        <div class="console-left">
                            <span class="console-dot red"></span>
                            <span class="console-dot yellow"></span>
                            <span class="console-dot green"></span>
                            <span class="console-title">{{ $school->school_name }} &bull; Profil Sekolah</span>
                        </div>
                    </div>
                    <div class="school-video-frame">
                        @if($videoEmbed)
                            <iframe src="{{ $videoEmbed }}" title="{{ $videoTitle }}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        @elseif(str_ends_with(strtolower(parse_url($videoUrl, PHP_URL_PATH) ?? ''), '.mp4'))
                            <video controls preload="metadata"><source src="{{ $videoUrl }}" type="video/mp4">Browser Anda tidak mendukung video.</video>
                        @else
                            <div class="school-video-fallback">
                                <p>Video profil sekolah dapat disaksikan melalui tautan resmi:</p>
                                <a href="{{ $videoUrl }}" target="_blank" rel="noopener" class="button">Tonton video profil sekolah <span class="arrow">↗</span></a>
                            </div>
                        @endif
                    </div>
                    <div class="cinema-features-strip">
                        <div class="cinema-feature-item">
                            <span class="feature-num">01</span>
                            <div class="feature-info">
                                <strong>Pembiasaan Karakter</strong>
                                <small>Adab harian &amp; ibadah berjamaah</small>
                            </div>
                        </div>
                        <div class="cinema-feature-item">
                            <span class="feature-num">02</span>
                            <div class="feature-info">
                                <strong>Ruang Kelas Interaktif</strong>
                                <small>Eksplorasi sains, bahasa &amp; tahfidz</small>
                            </div>
                        </div>
                        <div class="cinema-feature-item">
                            <span class="feature-num">03</span>
                            <div class="feature-info">
                                <strong>Lingkungan Tumbuh Aman</strong>
                                <small>Guru sebagai teladan &amp; sahabat anak</small>
                            </div>
                        </div>
                    </div>
                    <div class="cinema-seal" aria-hidden="true">
                        <span class="seal-icon">✦</span>
                        <span>Dokumenter<br>Resmi</span>
                    </div>
                </div>
            </div>
        </section>

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
                <div class="vision-intro">
                    <p class="eyebrow">Arah pendidikan</p>
                    <h2>Berjalan dengan tujuan.</h2>
                    <p>Setiap proses belajar punya arah yang jelas: membantu anak bertumbuh dengan iman, ilmu, dan kepedulian.</p>
                </div>
                <div class="vision-cards">
                    <article class="vision-card">
                        <div>
                            <div class="card-pill leaf"><span class="pill-dot"></span> Visi Sekolah</div>
                            <p>{{ $visionText }}</p>
                        </div>
                        <div class="vision-card-footer">
                            <span class="vision-seal" aria-hidden="true">✳</span>
                            <span class="vision-tagline">Landasan Nilai</span>
                        </div>
                    </article>
                    <article class="mission-card">
                        <div>
                            <div class="card-pill pine"><span class="pill-dot"></span> Misi Kami</div>
                            <ol class="mission-list">
                                @foreach(preg_split('/\r\n|\r|\n/', $missionText) as $missionLine)
                                    @if(trim($missionLine))
                                        <li class="mission-item">
                                            <span class="mission-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                            <span class="mission-text">{{ trim($missionLine) }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                        <div class="mission-card-footer">
                            <span class="mission-subnote">Diterapkan dalam kurikulum dan pembiasaan adab harian.</span>
                        </div>
                    </article>
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

        @php
            // Kumpulkan konten dinamis untuk lembaran buku interaktif
            $bookDynamicEntries = collect();

            foreach ($champions as $champ) {
                $bookDynamicEntries->push([
                    'category' => 'Prestasi & Juara',
                    'kicker' => $champ->kicker ?: 'Prestasi Siswa',
                    'icon' => '🏆',
                    'title' => $champ->title,
                    'body' => $champ->body,
                    'image_url' => $champ->image_url,
                    'is_champion' => true,
                ]);
            }

            foreach ($activities as $act) {
                $bookDynamicEntries->push([
                    'category' => 'Momen & Kegiatan',
                    'kicker' => $act->kicker ?: ($act->published_at ? $act->published_at->translatedFormat('d F Y') : 'Kabar Sekolah'),
                    'icon' => '📸',
                    'title' => $act->title,
                    'body' => $act->body,
                    'image_url' => $act->image_url,
                    'is_champion' => false,
                ]);
            }

            if ($bookDynamicEntries->isEmpty()) {
                $bookDynamicEntries->push([
                    'category' => 'Prestasi & Juara',
                    'kicker' => 'Olimpiade Sains & Riset',
                    'icon' => '🔬',
                    'title' => 'Penghargaan Riset & Olimpiade Sains',
                    'body' => 'Pembinaan intensif logika sains dan daya cipta berbasis adab dan pemikiran ilmiah.',
                    'image_url' => null,
                    'is_champion' => true,
                ]);
                $bookDynamicEntries->push([
                    'category' => 'Prestasi & Juara',
                    'kicker' => 'Tahfidz Al-Qur\'an',
                    'icon' => '📖',
                    'title' => 'Musabaqah Hifzhil Qur\'an Mutqin',
                    'body' => 'Mencetak generasi hafidz bersanad dengan tartil, pemahaman makna, dan akhlak Al-Qur\'an.',
                    'image_url' => null,
                    'is_champion' => true,
                ]);
                $bookDynamicEntries->push([
                    'category' => 'Momen & Kegiatan',
                    'kicker' => 'Ekosistem Belajar',
                    'icon' => '🌱',
                    'title' => 'Pembiasaan Adab & Kepemimpinan',
                    'body' => 'Melatih empati, kejujuran, dan kemandirian ananda melalui proyek kolaborasi sehari-hari.',
                    'image_url' => null,
                    'is_champion' => false,
                ]);
            }

            // Susun ke dalam Spreads (setiap spread berisi halaman kiri & kanan)
            $mainBookSpreads = [];

            // Spread 1: Cover Prolog di Halaman Kiri + Item Pertama di Halaman Kanan
            $firstChampOrItem = $bookDynamicEntries->first();
            $otherItems = $bookDynamicEntries->slice(1)->values();

            $mainBookSpreads[] = [
                'left' => [
                    'type' => 'cover',
                    'kicker' => 'Kitab Jejak Ananda',
                    'title' => 'Buku Prestasi & Rekam Jejak Santri',
                    'body' => 'Menghimpun dedikasi, piala kejuaraan, dan dokumentasi momen berharga para santri di ' . $school->school_name . '. Buka dan balik lembaran ini untuk menjelajahi jejak karya mereka.',
                ],
                'right' => [
                    'type' => 'entry',
                    'data' => $firstChampOrItem,
                ]
            ];

            // Pasangkan item-item berikutnya 2 per spread (kiri & kanan)
            $itemChunks = $otherItems->chunk(2);
            foreach ($itemChunks as $chunk) {
                $pair = $chunk->values();
                $mainBookSpreads[] = [
                    'left' => [
                        'type' => 'entry',
                        'data' => $pair->get(0),
                    ],
                    'right' => [
                        'type' => $pair->has(1) ? 'entry' : 'epilogue',
                        'data' => $pair->get(1),
                    ]
                ];
            }

            // Jika spread terakhir berisi 2 item penuh, tambahkan spread penutup (Epilogue)
            $lastCreatedSpread = end($mainBookSpreads);
            if ($lastCreatedSpread['right']['type'] !== 'epilogue') {
                $mainBookSpreads[] = [
                    'left' => [
                        'type' => 'summary',
                        'kicker' => 'Nilai & Pembinaan',
                        'title' => 'Membangun Mental Juara Berakar Adab',
                        'body' => 'Setiap perlombaan dan kegiatan adalah sarana menanamkan ketulusan, sportivitas, dan keteguhan hati. Kami mendampingi setiap ananda menemukan potensi terbaiknya.',
                    ],
                    'right' => [
                        'type' => 'epilogue',
                        'kicker' => 'Langkah Berikutnya',
                        'title' => 'Ukir Kisah Sukses Ananda Bersama Kami',
                        'body' => 'Pintu gerbang pendidikan karakter dan prestasi telah dibuka. Mari menjadi bagian dari keluarga besar ' . $school->school_name . '.',
                    ]
                ];
            }
        @endphp

        <section class="section book-showcase-section" id="juara" aria-label="Buku Prestasi dan Rekam Jejak Siswa">
            <div class="container">
                <div class="book-showcase-head">
                    <div>
                        <p class="eyebrow" style="color:var(--orange);">Buku Interaktif Sekolah</p>
                        <h2>Prestasi &amp; rekam jejak santri.</h2>
                    </div>
                    <div class="book-controls-bar">
                        <div class="book-badge-pill">
                            <span class="pulse-live"></span>
                            <span>Mode Balik Buku (Loop)</span>
                        </div>
                        <button class="book-btn-toggle" id="btn-book-3d" onclick="toggleBook3DMode()" title="Aktifkan / matikan efek tilt 3D">
                            <span id="book-3d-icon">🎛️</span> <span id="book-3d-text">3D: Aktif</span>
                        </button>
                        <button class="book-btn-toggle" id="btn-auto-flip" onclick="toggleBookAutoPlay()" title="Mulai / jeda putar otomatis lembaran buku">
                            <span id="auto-flip-icon">▶</span> <span id="auto-flip-text">Putar Otomatis</span>
                        </button>
                        <button class="book-btn-toggle" id="btn-book-sound" onclick="toggleMainBookSound()" title="Aktifkan / bisukan efek suara lembaran kertas">
                            <span id="book-sound-icon">🔊</span> <span id="book-sound-text">Suara: On</span>
                        </button>
                    </div>
                </div>

                <!-- Grand Open Book Stage -->
                <div class="book-stage-wrapper" id="main-book-stage">
                    <button class="book-float-arrow prev" onclick="prevMainBookSpread()" aria-label="Halaman Sebelumnya">‹</button>
                    <button class="book-float-arrow next" onclick="nextMainBookSpread()" aria-label="Halaman Berikutnya">›</button>

                    <div class="grand-book-folio" id="grand-book-folio">
                        <div class="book-3d-sticker badge-mint">⚡ Rekam Jejak 3D</div>
                        <div class="book-3d-sticker badge-gold">🏆 Prestasi &amp; Medali</div>

                        <div class="book-corner-seal tl"></div>
                        <div class="book-corner-seal tr"></div>
                        <div class="book-corner-seal bl"></div>
                        <div class="book-corner-seal br"></div>
                        
                        <!-- 3D Metallic Ring Spine -->
                        <div class="book-3d-rings-spine">
                            <div class="book-ring-metal"></div>
                            <div class="book-ring-metal"></div>
                            <div class="book-ring-metal"></div>
                            <div class="book-ring-metal"></div>
                            <div class="book-ring-metal"></div>
                            <div class="book-ring-metal"></div>
                            <div class="book-ring-metal"></div>
                        </div>

                        <div class="book-spreads-viewport" id="book-spreads-viewport">
                            @foreach($mainBookSpreads as $spreadIdx => $spread)
                                <div class="book-spread-card {{ $spreadIdx === 0 ? 'active' : '' }}" data-spread="{{ $spreadIdx }}">
                                    
                                    {{-- HALAMAN KIRI --}}
                                    <div class="book-page-leaf leaf-left">
                                        <div class="leaf-top-bar">
                                            @if($spread['left']['type'] === 'cover')
                                                <span class="leaf-kicker-tag gold">📖 {{ $spread['left']['kicker'] }}</span>
                                            @elseif($spread['left']['type'] === 'summary')
                                                <span class="leaf-kicker-tag pine">🌟 {{ $spread['left']['kicker'] }}</span>
                                            @else
                                                <span class="leaf-kicker-tag {{ $spread['left']['data']['is_champion'] ? 'gold' : 'pine' }}">
                                                    {{ $spread['left']['data']['icon'] }} {{ $spread['left']['data']['kicker'] }}
                                                </span>
                                            @endif
                                            <span class="leaf-crest-mark">{{ $school->school_name }}</span>
                                        </div>

                                        @if($spread['left']['type'] === 'cover')
                                            <div class="leaf-seal-art">
                                                <span class="seal-symbol">إ</span>
                                                <strong>Mahkota Ilmu &amp; Adab</strong>
                                                <small>Catatan Prestasi Terpilih</small>
                                            </div>
                                            <h3 class="leaf-story-title">{{ $spread['left']['title'] }}</h3>
                                            <p class="leaf-story-body">{{ $spread['left']['body'] }}</p>
                                        @elseif($spread['left']['type'] === 'summary')
                                            <div class="leaf-seal-art" style="background:linear-gradient(135deg, #1f4f44 0%, #12332c 100%);">
                                                <span class="seal-symbol">🏛️</span>
                                                <strong>Ekosistem Berprestasi</strong>
                                                <small>Karakter &bull; Ibadah &bull; Karya</small>
                                            </div>
                                            <h3 class="leaf-story-title">{{ $spread['left']['title'] }}</h3>
                                            <p class="leaf-story-body">{{ $spread['left']['body'] }}</p>
                                        @else
                                            @php $leftData = $spread['left']['data']; @endphp
                                            @if(!empty($leftData['image_url']))
                                                <div class="leaf-photo-frame" style="background-image:url('{{ $leftData['image_url'] }}')">
                                                    <span class="photo-stamp">{{ $leftData['category'] }}</span>
                                                </div>
                                            @else
                                                <div class="leaf-seal-art">
                                                    <span class="seal-symbol">{{ $leftData['icon'] }}</span>
                                                    <strong>{{ $leftData['category'] }}</strong>
                                                    <small>{{ $leftData['kicker'] }}</small>
                                                </div>
                                            @endif
                                            <h3 class="leaf-story-title">{{ $leftData['title'] }}</h3>
                                            <p class="leaf-story-body">{{ $leftData['body'] }}</p>
                                        @endif

                                        <div class="leaf-bottom-bar">
                                            <span class="leaf-page-num">— Lembar {{ $spreadIdx * 2 + 1 }} —</span>
                                            <button type="button" class="leaf-cheer-btn" onclick="cheerEntry(this, event)">
                                                <span>👏</span> <span>Apresiasi</span> <strong class="cheer-num">{{ 12 + ($spreadIdx * 7) }}</strong>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- HALAMAN KANAN --}}
                                    <div class="book-page-leaf leaf-right">
                                        <div class="leaf-top-bar">
                                            @if($spread['right']['type'] === 'epilogue')
                                                <span class="leaf-kicker-tag leaf">✨ Langkah Berikutnya</span>
                                            @else
                                                <span class="leaf-kicker-tag {{ $spread['right']['data']['is_champion'] ? 'gold' : 'pine' }}">
                                                    {{ $spread['right']['data']['icon'] }} {{ $spread['right']['data']['kicker'] }}
                                                </span>
                                            @endif
                                            <span class="leaf-crest-mark">{{ $school->school_name }}</span>
                                        </div>

                                        @if($spread['right']['type'] === 'epilogue')
                                            <div class="leaf-seal-art" style="background:linear-gradient(135deg, #a2512a 0%, #d96e3d 100%);">
                                                <span class="seal-symbol">🎓</span>
                                                <strong>Generasi Pemimpin Beradab</strong>
                                                <small>Penerimaan Santri Baru</small>
                                            </div>
                                            <h3 class="leaf-story-title">{{ $spread['right']['title'] ?? 'Ukir Kisah Ananda Bersama Kami' }}</h3>
                                            <p class="leaf-story-body">{{ $spread['right']['body'] ?? 'Pintu gerbang belajar, hafalan, dan petualangan ilmu terbuka lebar. Mari bergabung bersama keluarga besar ' . $school->school_name . '.' }}</p>
                                            
                                            <div style="margin-top:auto;padding-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
                                                <a href="#pendaftaran" class="button" style="padding:10px 18px;font-size:12px;background:var(--pine);color:#fff;border-color:var(--pine);">
                                                    Daftar Sekarang →
                                                </a>
                                                <button type="button" class="button button-subtle" onclick="goToMainBookSpread(0)" style="padding:10px 14px;font-size:12px;">
                                                    ↺ Balik ke Awal
                                                </button>
                                            </div>
                                        @else
                                            @php $rightData = $spread['right']['data']; @endphp
                                            @if(!empty($rightData['image_url']))
                                                <div class="leaf-photo-frame" style="background-image:url('{{ $rightData['image_url'] }}')">
                                                    <span class="photo-stamp">{{ $rightData['category'] }}</span>
                                                </div>
                                            @else
                                                <div class="leaf-seal-art">
                                                    <span class="seal-symbol">{{ $rightData['icon'] }}</span>
                                                    <strong>{{ $rightData['category'] }}</strong>
                                                    <small>{{ $rightData['kicker'] }}</small>
                                                </div>
                                            @endif
                                            <h3 class="leaf-story-title">{{ $rightData['title'] }}</h3>
                                            <p class="leaf-story-body">{{ $rightData['body'] }}</p>
                                        @endif

                                        <div class="leaf-bottom-bar">
                                            <span class="leaf-page-num">— Lembar {{ $spreadIdx * 2 + 2 }} —</span>
                                            @if($spread['right']['type'] !== 'epilogue')
                                                <button type="button" class="leaf-cheer-btn" onclick="cheerEntry(this, event)">
                                                    <span>⭐</span> <span>Kagum</span> <strong class="cheer-num">{{ 18 + ($spreadIdx * 9) }}</strong>
                                                </button>
                                            @else
                                                <span style="font-size:11px;font-weight:700;color:var(--pine);">Terus Berputar ↺</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Strip Kontrol Navigasi Bawah -->
                    <div class="book-footer-nav">
                        <div class="book-dots-container" id="book-dots-list">
                            @foreach($mainBookSpreads as $spreadIdx => $spread)
                                <button type="button" class="book-page-dot {{ $spreadIdx === 0 ? 'active' : '' }}" onclick="goToMainBookSpread({{ $spreadIdx }})" aria-label="Menuju lembar {{ $spreadIdx + 1 }}"></button>
                            @endforeach
                        </div>

                        <div class="book-counter-label" id="book-spread-counter">
                            Lembar <span id="current-spread-text">1</span> dari {{ count($mainBookSpreads) }} &nbsp;&bull;&nbsp; <span style="color:var(--orange);">Klik panah untuk terus membalik (loop)</span>
                        </div>
                    </div>
                </div>
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

    <!-- Interactive Book Engine Script -->
    <script>
        (function() {
            let currentMainSpread = 0;
            const spreadCards = document.querySelectorAll('.book-spread-card');
            const totalMainSpreads = spreadCards.length;
            const spreadDots = document.querySelectorAll('.book-page-dot');
            const spreadCounter = document.getElementById('current-spread-text');
            const bookStage = document.getElementById('main-book-stage');

            let bookAudioCtx = null;
            let bookSoundOn = true;
            let bookAutoPlayTimer = null;
            let isAutoPlayActive = false;

            function initAudio() {
                if (!bookAudioCtx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) bookAudioCtx = new AudioContext();
                }
                if (bookAudioCtx && bookAudioCtx.state === 'suspended') {
                    bookAudioCtx.resume();
                }
            }

            function playTone(freq, type = 'sine', duration = 0.15, vol = 0.1) {
                if (!bookSoundOn) return;
                try {
                    initAudio();
                    if (!bookAudioCtx) return;
                    const osc = bookAudioCtx.createOscillator();
                    const gain = bookAudioCtx.createGain();
                    osc.type = type;
                    osc.frequency.setValueAtTime(freq, bookAudioCtx.currentTime);
                    gain.gain.setValueAtTime(vol, bookAudioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, bookAudioCtx.currentTime + duration);
                    osc.connect(gain);
                    gain.connect(bookAudioCtx.destination);
                    osc.start();
                    osc.stop(bookAudioCtx.currentTime + duration);
                } catch(e) {}
            }

            function playPaperSound() {
                // Dual flutter tone
                playTone(320, 'triangle', 0.12, 0.07);
                setTimeout(() => playTone(440, 'sine', 0.14, 0.05), 50);
            }

            function playCheerChime() {
                playTone(523.25, 'sine', 0.2, 0.12);
                setTimeout(() => playTone(659.25, 'sine', 0.2, 0.12), 70);
                setTimeout(() => playTone(783.99, 'sine', 0.28, 0.15), 140);
            }

            window.toggleMainBookSound = function() {
                bookSoundOn = !bookSoundOn;
                const icon = document.getElementById('book-sound-icon');
                const text = document.getElementById('book-sound-text');
                if (bookSoundOn) {
                    icon.textContent = '🔊';
                    text.textContent = 'Suara: On';
                    playPaperSound();
                } else {
                    icon.textContent = '🔇';
                    text.textContent = 'Suara: Off';
                }
            };

            function showMainSpread(index, direction = 'next') {
                if (totalMainSpreads <= 0) return;
                const prevIndex = currentMainSpread;
                currentMainSpread = (index + totalMainSpreads) % totalMainSpreads;

                spreadCards.forEach((card, idx) => {
                    card.classList.remove('active', 'flip-out-left', 'flip-in-right');
                    if (idx === currentMainSpread) {
                        card.classList.add('active');
                        if (direction === 'next') card.classList.add('flip-in-right');
                    } else if (idx === prevIndex) {
                        if (direction === 'next') card.classList.add('flip-out-left');
                    }
                });

                spreadDots.forEach((dot, idx) => {
                    dot.classList.toggle('active', idx === currentMainSpread);
                });

                if (spreadCounter) {
                    spreadCounter.textContent = (currentMainSpread + 1);
                }

                playPaperSound();
            }

            window.nextMainBookSpread = function() {
                showMainSpread(currentMainSpread + 1, 'next');
            };

            window.prevMainBookSpread = function() {
                showMainSpread(currentMainSpread - 1, 'prev');
            };

            window.goToMainBookSpread = function(index) {
                const dir = index >= currentMainSpread ? 'next' : 'prev';
                showMainSpread(index, dir);
            };

            // Autoplay toggle
            window.toggleBookAutoPlay = function() {
                isAutoPlayActive = !isAutoPlayActive;
                const icon = document.getElementById('auto-flip-icon');
                const text = document.getElementById('auto-flip-text');

                if (isAutoPlayActive) {
                    icon.textContent = '⏸';
                    text.textContent = 'Jeda Putar';
                    startAutoPlayTimer();
                } else {
                    icon.textContent = '▶';
                    text.textContent = 'Putar Otomatis';
                    stopAutoPlayTimer();
                }
            };

            function startAutoPlayTimer() {
                stopAutoPlayTimer();
                bookAutoPlayTimer = setInterval(() => {
                    if (isAutoPlayActive) {
                        nextMainBookSpread();
                    }
                }, 7000);
            }

            function stopAutoPlayTimer() {
                if (bookAutoPlayTimer) {
                    clearInterval(bookAutoPlayTimer);
                    bookAutoPlayTimer = null;
                }
            }

            // Pause on hover
            if (bookStage) {
                bookStage.addEventListener('mouseenter', () => {
                    if (isAutoPlayActive) stopAutoPlayTimer();
                });
                bookStage.addEventListener('mouseleave', () => {
                    if (isAutoPlayActive) startAutoPlayTimer();
                });
            }

            // Interactive cheer button
            window.cheerEntry = function(btn, event) {
                initAudio();
                playCheerChime();

                const numEl = btn.querySelector('.cheer-num');
                if (numEl) {
                    const currentVal = parseInt(numEl.textContent, 10) || 0;
                    numEl.textContent = currentVal + 1;
                }

                btn.style.transform = 'scale(1.15)';
                setTimeout(() => { btn.style.transform = ''; }, 200);

                // Floating particle
                const particle = document.createElement('span');
                particle.textContent = '⭐ +1';
                particle.style.position = 'fixed';
                particle.style.left = (event.clientX || window.innerWidth / 2) + 'px';
                particle.style.top = (event.clientY || window.innerHeight / 2) + 'px';
                particle.style.fontFamily = 'sans-serif';
                particle.style.fontSize = '15px';
                particle.style.fontWeight = 'bold';
                particle.style.color = '#d96e3d';
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '9999';
                particle.style.transition = 'all 0.75s ease-out';
                particle.style.textShadow = '0 2px 8px rgba(0,0,0,0.15)';
                document.body.appendChild(particle);

                requestAnimationFrame(() => {
                    particle.style.transform = 'translateY(-35px) scale(1.25)';
                    particle.style.opacity = '0';
                });
                setTimeout(() => particle.remove(), 750);
            };

            // 3D Parallax Tilt Engine
            let book3DActive = true;
            const grandBook = document.getElementById('grand-book-folio');
            let targetRotX = 2;
            let targetRotY = 0;
            let currentRotX = 2;
            let currentRotY = 0;

            if (bookStage) {
                bookStage.addEventListener('mousemove', (e) => {
                    if (!book3DActive || !grandBook) return;
                    const rect = bookStage.getBoundingClientRect();
                    const cx = rect.left + rect.width / 2;
                    const cy = rect.top + rect.height / 2;
                    const normX = (e.clientX - cx) / (rect.width / 2);
                    const normY = (e.clientY - cy) / (rect.height / 2);
                    targetRotX = normY * -8 + 2;
                    targetRotY = normX * 10;
                });
                bookStage.addEventListener('mouseleave', () => {
                    targetRotX = 2;
                    targetRotY = 0;
                });
            }

            function animateBook3D() {
                if (book3DActive && grandBook) {
                    currentRotX += (targetRotX - currentRotX) * 0.08;
                    currentRotY += (targetRotY - currentRotY) * 0.08;
                    grandBook.style.transform = `rotateX(${currentRotX.toFixed(2)}deg) rotateY(${currentRotY.toFixed(2)}deg)`;
                }
                requestAnimationFrame(animateBook3D);
            }
            animateBook3D();

            window.toggleBook3DMode = function() {
                book3DActive = !book3DActive;
                const icon = document.getElementById('book-3d-icon');
                const text = document.getElementById('book-3d-text');
                if (book3DActive) {
                    icon.textContent = '🎛️';
                    text.textContent = '3D: Aktif';
                } else {
                    icon.textContent = '📐';
                    text.textContent = '3D: Mati';
                    if (grandBook) grandBook.style.transform = 'none';
                }
            };

            // Touch swipe support
            let touchStartX = 0;
            if (bookStage) {
                bookStage.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });

                bookStage.addEventListener('touchend', (e) => {
                    const touchEndX = e.changedTouches[0].screenX;
                    const diff = touchEndX - touchStartX;
                    if (Math.abs(diff) > 45) {
                        if (diff < 0) nextMainBookSpread();
                        else prevMainBookSpread();
                    }
                }, { passive: true });
            }
        })();
    </script>
</body>
</html>

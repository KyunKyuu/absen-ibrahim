<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="Petualangan 3D Siswa Third-Person &amp; Buku Petualangan Cerita Interaktif Kampus Islami {{ $school->school_name }}">
    <title>Jelajah 3D Kampus Islami Modern — {{ $school->school_name }} | Buku Petualangan Cerita Interaktif</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;1,6..72,500&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Three.js Engine -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <style>
        :root {
            --mint: #2de2a6;
            --mint-glow: rgba(45, 226, 166, 0.45);
            --gold: #f4c444;
            --gold-glow: rgba(244, 196, 68, 0.5);
            --orange: #ff7644;
            --pine: #144d40;
            --pine-deep: #0a2620;
            --cyan: #38bdf8;
            --sky: #87ceeb;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: "Plus Jakarta Sans", sans-serif;
            background: #081a16;
            color: #fff;
            user-select: none;
            -webkit-user-select: none;
        }

        /* 3D WebGL Canvas Layer */
        #game-canvas-container {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            touch-action: none;
            cursor: grab;
        }
        #game-canvas-container:active {
            cursor: grabbing;
        }

        /* Loading Screen */
        #loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 10000;
            background: radial-gradient(circle at 50% 35%, #16463c 0%, #0d2c25 60%, #05120f 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: opacity 0.5s ease;
        }
        .loader-crest {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            background: linear-gradient(135deg, var(--mint) 0%, var(--pine) 100%);
            display: grid;
            place-items: center;
            font-size: 42px;
            font-weight: 800;
            color: #061512;
            box-shadow: 0 0 35px var(--mint-glow);
            margin-bottom: 20px;
            animation: pulseCrest 1.8s infinite ease-in-out;
        }
        @keyframes pulseCrest { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        .loader-bar-wrap {
            width: 260px;
            height: 7px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 14px;
        }
        .loader-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--mint), var(--gold));
            border-radius: 999px;
            transition: width 0.3s ease;
        }

        /* Top HUD Bar */
        .hud-top-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 22px;
            background: linear-gradient(180deg, rgba(8, 26, 22, 0.92) 0%, rgba(8, 26, 22, 0) 100%);
            pointer-events: none;
        }
        .hud-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            pointer-events: auto;
            text-decoration: none;
            color: #fff;
        }
        .hud-crest {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--mint) 0%, var(--pine) 100%);
            display: grid;
            place-items: center;
            font-size: 24px;
            font-weight: 800;
            color: #061512;
            box-shadow: 0 0 16px var(--mint-glow);
        }
        .hud-brand-text strong {
            display: block;
            font-family: "Outfit", sans-serif;
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
        }
        .hud-brand-text small {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            color: var(--mint);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--mint);
            box-shadow: 0 0 8px var(--mint);
            animation: pulseDot 2s infinite ease-in-out;
        }
        @keyframes pulseDot { 0%, 100% { opacity: 0.4; } 50% { opacity: 1; transform: scale(1.2); } }

        .hud-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: auto;
            flex-wrap: wrap;
        }
        .hud-btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 999px;
            font-family: "Outfit", sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            background: rgba(10, 38, 32, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(45, 226, 166, 0.35);
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .hud-btn-pill:hover {
            background: var(--mint);
            color: #061512;
            border-color: var(--mint);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px var(--mint-glow);
        }
        .hud-btn-pill.gold {
            background: rgba(244, 196, 68, 0.2);
            border-color: rgba(244, 196, 68, 0.5);
            color: var(--gold);
        }
        .hud-btn-pill.mint {
            background: rgba(45, 226, 166, 0.18);
            border-color: rgba(45, 226, 166, 0.5);
            color: var(--mint);
        }
        .hud-avatar-thumb {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid var(--gold);
            margin-right: -2px;
            box-shadow: 0 0 6px rgba(244, 196, 68, 0.4);
        }

        /* Minimap Radar Corner HUD */
        #radar-container {
            position: absolute;
            top: 76px;
            right: 20px;
            z-index: 22;
            width: 140px;
            height: 140px;
            background: rgba(10, 38, 32, 0.88);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(45, 226, 166, 0.45);
            border-radius: 50%;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5), 0 0 15px var(--mint-glow);
            overflow: hidden;
            pointer-events: auto;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        #radar-container:hover {
            transform: scale(1.05);
            border-color: var(--gold);
        }
        #radar-canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .radar-badge-label {
            position: absolute;
            bottom: 6px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 9.5px;
            font-weight: 800;
            background: rgba(6, 21, 18, 0.92);
            color: var(--mint);
            padding: 1px 7px;
            border-radius: 999px;
            letter-spacing: 0.05em;
            pointer-events: none;
            white-space: nowrap;
        }

        /* Floating Pet Hearts Animation */
        .cat-heart-particle {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            font-size: 26px;
            animation: floatHeart 1.4s cubic-bezier(0.2, 0.8, 0.3, 1) forwards;
        }
        @keyframes floatHeart {
            0% { transform: translate(-50%, -50%) scale(0.5) rotate(0deg); opacity: 1; }
            50% { transform: translate(calc(-50% + 18px), -90px) scale(1.3) rotate(15deg); opacity: 1; }
            100% { transform: translate(calc(-50% - 10px), -160px) scale(0.9) rotate(-10deg); opacity: 0; }
        }

        /* Exploration Toast Notification */
        #campus-toast {
            position: fixed;
            top: 76px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: linear-gradient(135deg, rgba(10, 38, 32, 0.96) 0%, rgba(20, 77, 64, 0.96) 100%);
            border: 1.5px solid var(--gold);
            color: #fff;
            padding: 10px 22px;
            border-radius: 999px;
            font-family: "Outfit", sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6), 0 0 20px rgba(244, 196, 68, 0.35);
            z-index: 2100;
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #campus-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Proximity Prompt (Grounded UI) */
        #proximity-prompt {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            z-index: 25;
            background: rgba(10, 38, 32, 0.95);
            backdrop-filter: blur(16px);
            border: 2px solid var(--mint);
            border-radius: 999px;
            padding: 12px 28px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), 0 0 25px var(--mint-glow);
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            font-family: "Outfit", sans-serif;
            cursor: pointer;
        }
        #proximity-prompt.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }
        .key-badge {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--mint);
            color: #061512;
            font-weight: 800;
            display: grid;
            place-items: center;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        /* Virtual Touch D-Pad for Kiosks & Mobile */
        .touch-controls-pad {
            position: absolute;
            bottom: 24px;
            left: 24px;
            z-index: 25;
            display: grid;
            grid-template-columns: repeat(3, 52px);
            grid-template-rows: repeat(3, 52px);
            gap: 6px;
            opacity: 0.88;
            transition: opacity 0.2s ease;
        }
        .touch-btn {
            background: rgba(14, 46, 38, 0.85);
            border: 1.5px solid rgba(45, 226, 166, 0.4);
            border-radius: 14px;
            color: #fff;
            font-size: 20px;
            display: grid;
            place-items: center;
            cursor: pointer;
            backdrop-filter: blur(6px);
            touch-action: manipulation;
            user-select: none;
        }
        .touch-btn:active {
            background: var(--mint);
            color: #061512;
            transform: scale(0.92);
        }

        /* Right Touch Action Buttons (Sprint, Jump, Emote, Interact) */
        .touch-actions-group {
            position: absolute;
            bottom: 24px;
            right: 24px;
            z-index: 25;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
        }
        .action-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .action-circle-btn {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(14, 46, 38, 0.88);
            border: 2px solid var(--mint);
            color: #fff;
            font-size: 20px;
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4), 0 0 15px var(--mint-glow);
            backdrop-filter: blur(8px);
            touch-action: manipulation;
            transition: transform 0.15s ease, background 0.15s ease;
        }
        .action-circle-btn:active, .action-circle-btn.active {
            background: var(--mint);
            color: #061512;
            transform: scale(0.92);
        }
        .action-circle-btn.gold {
            border-color: var(--gold);
            box-shadow: 0 0 15px var(--gold-glow);
        }
        .action-circle-btn.gold:active {
            background: var(--gold);
            color: #061512;
        }

        /* Modal Overlay For School Content */
        .campus-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(6, 18, 15, 0.88);
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .campus-modal-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }
        .campus-modal-box {
            position: relative;
            width: 100%;
            max-width: 820px;
            max-height: 88vh;
            overflow-y: auto;
            background: #0d2a23;
            border: 2px solid var(--mint);
            border-radius: 28px;
            padding: 34px 38px;
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.8), 0 0 40px var(--mint-glow);
            transform: scale(0.92) translateY(20px);
            transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            color: #fff;
        }
        .campus-modal-overlay.open .campus-modal-box {
            transform: scale(1) translateY(0);
        }
        .modal-close-btn {
            position: absolute;
            top: 20px;
            right: 22px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 20px;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .modal-close-btn:hover {
            background: var(--orange);
            border-color: var(--orange);
            transform: scale(1.1);
        }

        /* Speaker VN Card */
        .speaker-vn-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(45, 226, 166, 0.35);
            border-radius: 20px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .speaker-vn-avatar {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid var(--gold);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }
        .speaker-vn-info h3 {
            font-family: "Outfit", sans-serif;
            font-size: 17px;
            margin: 0 0 3px 0;
            color: #fff;
        }
        .speaker-vn-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 2px;
        }

        /* Persona Grid */
        .persona-pick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
            margin: 18px 0;
        }
        .persona-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s ease;
            color: #fff;
        }
        .persona-btn:hover, .persona-btn.selected {
            background: rgba(45, 226, 166, 0.15);
            border-color: var(--mint);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--mint-glow);
        }
        .persona-avatar-img {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid var(--gold);
        }
        .persona-tag-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(244, 196, 68, 0.2);
            color: var(--gold);
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        /* Interactive Quiz Card */
        .quiz-option-btn {
            display: block;
            width: 100%;
            text-align: left;
            background: rgba(255, 255, 255, 0.06);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 12px 18px;
            color: #fff;
            font-size: 13.5px;
            cursor: pointer;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .quiz-option-btn:hover {
            background: rgba(45, 226, 166, 0.18);
            border-color: var(--mint);
            transform: translateX(4px);
        }
        .quiz-option-btn.correct {
            background: rgba(34, 197, 94, 0.3) !important;
            border-color: #22c55e !important;
            color: #86efac !important;
        }
        .quiz-option-btn.wrong {
            background: rgba(239, 68, 68, 0.3) !important;
            border-color: #ef4444 !important;
            color: #fca5a5 !important;
        }

        /* Virtual Student ID Card */
        .student-id-card-wrap {
            background: linear-gradient(135deg, #092620 0%, #134e42 50%, #0c332b 100%);
            border: 2.5px solid var(--gold);
            border-radius: 24px;
            padding: 24px 28px;
            position: relative;
            box-shadow: 0 15px 45px rgba(0,0,0,0.6), 0 0 30px rgba(244, 196, 68, 0.35);
            margin: 16px 0;
            overflow: hidden;
        }
        .student-id-card-wrap::before {
            content: "إ";
            position: absolute;
            right: -20px;
            bottom: -30px;
            font-size: 180px;
            font-weight: 900;
            color: rgba(244, 196, 68, 0.06);
            pointer-events: none;
            font-family: serif;
        }

        /* Confetti */
        #confetti-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 99999;
        }

        @media(max-width: 768px) {
            .hud-top-bar { padding: 10px 14px; }
            #radar-container { top: 68px; right: 12px; width: 110px; height: 110px; }
            .persona-pick-grid { grid-template-columns: 1fr; }
            .campus-modal-box { padding: 22px 18px; }
            #proximity-prompt { width: min(calc(100% - 32px), 380px); font-size: 13px; padding: 10px 18px; bottom: 85px; }
            .touch-controls-pad { bottom: 18px; left: 14px; }
            .touch-actions-group { bottom: 18px; right: 14px; }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-overlay">
        <div class="loader-crest">إ</div>
        <h2 style="font-family:'Outfit',sans-serif;font-size:22px;letter-spacing:-0.01em;margin-bottom:4px;text-align:center;">
            Memuat Balairung Kampus Islami Modern
        </h2>
        <p style="color:var(--mint);font-size:13px;text-align:center;">{{ $school->school_name }} &bull; Arsitektur Nyata &bull; Bintang Pelajar</p>
        <div class="loader-bar-wrap">
            <div class="loader-fill" id="loader-fill"></div>
        </div>
    </div>

    <!-- 3D Canvas Container -->
    <div id="game-canvas-container"></div>
    <canvas id="confetti-canvas"></canvas>
    <div id="campus-toast"></div>

    <!-- Top Navigation HUD -->
    <header class="hud-top-bar">
        <a href="{{ route('landing.story') }}" class="hud-brand">
            <div class="hud-crest">إ</div>
            <div class="hud-brand-text">
                <strong>{{ $school->school_name }}</strong>
                <small><span class="live-dot"></span> Ekosistem 3D Kampus Islami</small>
            </div>
        </a>

        <div class="hud-actions">
            <div class="hud-btn-pill gold" id="bintang-badge" title="Permata Bintang Terkumpul">
                <span>⭐</span> <span id="hud-star-count">0/15</span> <span>Permata</span>
            </div>
            <div class="hud-btn-pill mint" id="hud-explore-badge" title="Status Penjelajahan Kampus" onclick="openModal('map')" style="cursor:pointer;">
                <span>🗺️</span> <span id="hud-explore-text">0/10 Kawasan</span>
            </div>
            <button type="button" class="hud-btn-pill" onclick="openModal('persona')" title="Ganti karakter santri">
                <img id="char-avatar-hud" src="{{ asset('images/campus/fatih_avatar.jpg') }}" alt="Avatar" class="hud-avatar-thumb">
                <span id="char-name-hud">Fatih (OSIS)</span>
            </button>
            <button type="button" class="hud-btn-pill" onclick="openModal('idcard')" title="Buka Kartu Pelajar & Piagam Saya">
                <span>🪪</span> Kartu Santri
            </button>
            <button type="button" class="hud-btn-pill" onclick="openModal('quiz')" title="Kuis Cerdas Santri Juara">
                <span>🧠</span> Kuis Juara
            </button>
            <button type="button" class="hud-btn-pill" onclick="interactWithCat()" title="Elus / Panggil Kucing Sahabat (Si Oyen) [P]">
                <span>🐱</span> <span id="cat-hud-text">Si Oyen</span>
            </button>
            <button type="button" class="hud-btn-pill" onclick="toggleCameraView()" title="Ganti sudut kamera">
                <span>📷</span> <span id="camera-mode-text">TPV Normal</span>
            </button>
            <button type="button" class="hud-btn-pill" onclick="toggleAudio()" title="Suara langkah & ambiance">
                <span id="audio-icon-box">🔊</span> <span id="audio-text-box">Suara: On</span>
            </button>
            <button type="button" class="hud-btn-pill" onclick="toggleFullScreen()" title="Mode Layar Penuh">
                <span>⛶</span> Fullscreen
            </button>
            <a href="{{ route('landing') }}" class="hud-btn-pill">
                <span>🏛️</span> Mode Web
            </a>
        </div>
    </header>

    <!-- Radar Minimap -->
    <div id="radar-container" onclick="openModal('map')" title="Klik untuk membuka Peta Kampus & Fast Travel">
        <canvas id="radar-canvas" width="140" height="140"></canvas>
        <span class="radar-badge-label">🗺️ PETA KAMPUS</span>
    </div>

    <!-- Grounded Proximity Action Prompt -->
    <div id="proximity-prompt" onclick="triggerActiveCheckpoint()">
        <div class="key-badge">E</div>
        <div>
            <strong style="display:block;font-size:13.5px;" id="prompt-action-title">Masuk ke Zona</strong>
            <small style="color:var(--mint);font-size:11.5px;" id="prompt-action-subtitle">Tekan [E] atau Sentuh di sini</small>
        </div>
    </div>

    <!-- Virtual Touch D-Pad for Kiosks & Mobile -->
    <div class="touch-controls-pad" id="touch-pad">
        <div></div>
        <button type="button" class="touch-btn" id="btn-up" aria-label="Maju">▲</button>
        <div></div>
        <button type="button" class="touch-btn" id="btn-left" aria-label="Belok Kiri">◀</button>
        <div class="touch-btn" style="font-size:11px;font-weight:800;color:var(--mint);background:rgba(10,38,32,0.9);">MOVE</div>
        <button type="button" class="touch-btn" id="btn-right" aria-label="Belok Kanan">▶</button>
        <div></div>
        <button type="button" class="touch-btn" id="btn-down" aria-label="Mundur">▼</button>
        <div></div>
    </div>

    <!-- Right Touch Action Buttons (Sprint, Jump, Emote, Interact) -->
    <div class="touch-actions-group">
        <div class="action-row">
            <button type="button" class="action-circle-btn" id="btn-touch-sprint" onclick="toggleSprintMobile()" title="Lari Cepat (Shift)">
                ⚡
            </button>
            <button type="button" class="action-circle-btn" id="btn-touch-jump" onclick="triggerJump()" title="Lompat (Spasi)">
                🦘
            </button>
            <button type="button" class="action-circle-btn" id="btn-touch-cat" onclick="interactWithCat()" title="Elus Kucing (P)" style="background:rgba(245,158,11,0.28);border-color:rgba(245,158,11,0.65);">
                🐱
            </button>
        </div>
        <div class="action-row">
            <button type="button" class="action-circle-btn gold" id="btn-touch-emote" onclick="triggerEmoteCelebration()" title="Tepuk Tangan &amp; Takbir">
                🎉
            </button>
            <button type="button" class="action-circle-btn" id="btn-touch-interact" onclick="triggerActiveCheckpoint()" title="Interaksi (E)" style="width:62px;height:62px;font-size:26px;">
                💬
            </button>
        </div>
    </div>

    <!-- ==============================================================
         MODAL DETAIL CONTENT
         ============================================================== -->
    <div class="campus-modal-overlay" id="campus-modal" onclick="closeModalOnBackdrop(event)">
        <div class="campus-modal-box" id="campus-modal-box">
            <button type="button" class="modal-close-btn" onclick="closeModal()">&times;</button>
            <div id="modal-dynamic-body">
                <!-- Content dynamically injected -->
            </div>
        </div>
    </div>

    <!-- Dynamic Templates -->
    <div style="display:none;" id="modal-templates">
        <!-- 1. Persona Selector -->
        <div id="tpl-persona">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🪪</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Pilih Sahabat Petualangmu Hari Ini</h2>
                    <p style="color:var(--mint);font-size:12px;margin:0;">Karakter 3D Santri &bull; Teladan Pelajar Berakhlak Mulia</p>
                </div>
            </div>
            <p style="color:rgba(255,255,255,0.8);font-size:13.5px;line-height:1.6;">
                Pilih persona karakter santri yang mewakili semangat belajarmu di ekosistem kampus Islami {{ $school->school_name }}:
            </p>
            <div class="persona-pick-grid">
                <button type="button" class="persona-btn selected" onclick="setPlayerPersona('fatih', 'Fatih Al-Ayyubi', '🦁 Ketua OSIS', '#144d40', 'peci', '{{ asset('images/campus/fatih_avatar.jpg') }}')">
                    <img src="{{ asset('images/campus/fatih_avatar.jpg') }}" class="persona-avatar-img" alt="Fatih">
                    <div>
                        <span class="persona-tag-pill">🦁 Kepemimpinan &amp; OSIS</span>
                        <strong style="display:block;font-size:15px;color:#fff;">Fatih Al-Ayyubi</strong>
                        <small style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.4;display:block;">Seragam rompi hijau pinus &bull; Peci hitam santun &bull; Teladan kedisiplinan</small>
                    </div>
                </button>
                <button type="button" class="persona-btn" onclick="setPlayerPersona('alya', 'Alya Nur Hikmah', '🦉 Juara Riset', '#1b6353', 'hijab', '{{ asset('images/campus/alya_avatar.jpg') }}')">
                    <img src="{{ asset('images/campus/alya_avatar.jpg') }}" class="persona-avatar-img" alt="Alya">
                    <div>
                        <span class="persona-tag-pill">🦉 Sains &amp; Riset Terpadu</span>
                        <strong style="display:block;font-size:15px;color:#fff;">Alya Nur Hikmah</strong>
                        <small style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.4;display:block;">Blazer hijau emerald &bull; Jilbab santun &bull; Juara olimpiade sains</small>
                    </div>
                </button>
                <button type="button" class="persona-btn" onclick="setPlayerPersona('zaidan', 'Zaidan Robbani', '🦌 Duta Tahfiz', '#264e36', 'peci-white', '{{ asset('images/campus/zaidan_avatar.jpg') }}')">
                    <img src="{{ asset('images/campus/zaidan_avatar.jpg') }}" class="persona-avatar-img" alt="Zaidan">
                    <div>
                        <span class="persona-tag-pill">🦌 Tahfiz 30 Juz &amp; Tartil</span>
                        <strong style="display:block;font-size:15px;color:#fff;">Zaidan Robbani</strong>
                        <small style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.4;display:block;">Peci putih berseri &bull; Hafizh mutqin &bull; Suara tilawah merdu</small>
                    </div>
                </button>
                <button type="button" class="persona-btn" onclick="setPlayerPersona('nabila', 'Nabila Az-Zahra', '🎨 Kreator Desain', '#245a4a', 'hijab-teal', '{{ asset('images/campus/nabila_avatar.jpg') }}')">
                    <img src="{{ asset('images/campus/nabila_avatar.jpg') }}" class="persona-avatar-img" alt="Nabila">
                    <div>
                        <span class="persona-tag-pill">🎨 Media &amp; Desain Kreatif</span>
                        <strong style="display:block;font-size:15px;color:#fff;">Nabila Az-Zahra</strong>
                        <small style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.4;display:block;">Jilbab toska ceria &bull; Desain grafis kampus &bull; Kreator konten edukasi</small>
                    </div>
                </button>
                <button type="button" class="persona-btn" onclick="setPlayerPersona('rayyan', 'Rayyan Al-Farisi', '⚡ Atlet Sunnah', '#ff7644', 'peci', '{{ asset('images/campus/fatih_avatar.jpg') }}')">
                    <img src="{{ asset('images/campus/fatih_avatar.jpg') }}" class="persona-avatar-img" alt="Rayyan">
                    <div>
                        <span class="persona-tag-pill">⚡ Olahraga Sunnah &amp; Fisik</span>
                        <strong style="display:block;font-size:15px;color:#fff;">Rayyan Al-Farisi</strong>
                        <small style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.4;display:block;">Rompi oranye sporty &bull; Juara panahan &amp; futsal antar pesantren</small>
                    </div>
                </button>
                <button type="button" class="persona-btn" onclick="setPlayerPersona('maryam', 'Maryam Al-Khansa', '🔬 Ilmuwan Cilik', '#38bdf8', 'hijab', '{{ asset('images/campus/alya_avatar.jpg') }}')">
                    <img src="{{ asset('images/campus/alya_avatar.jpg') }}" class="persona-avatar-img" alt="Maryam">
                    <div>
                        <span class="persona-tag-pill">🔬 Robotika &amp; Astronomi</span>
                        <strong style="display:block;font-size:15px;color:#fff;">Maryam Al-Khansa</strong>
                        <small style="color:rgba(255,255,255,0.75);font-size:12px;line-height:1.4;display:block;">Blazer biru sains &bull; Pengembang robot santri &bull; Peneliti muda</small>
                    </div>
                </button>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;padding:10px 22px;">
                    Gunakan Karakter Ini &rarr;
                </button>
            </div>
        </div>

        <!-- 2. Virtual Student ID Card & Certificate -->
        <div id="tpl-idcard">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🪪</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Kartu Santri &amp; Piagam Penjelajah</h2>
                    <p style="color:var(--gold);font-size:12px;margin:0;">Tanda Bukti Penjelajah Ekosistem {{ $school->school_name }}</p>
                </div>
            </div>

            <div class="student-id-card-wrap" id="printable-id-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1.5px solid rgba(244,196,68,0.3);padding-bottom:14px;margin-bottom:16px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:38px;height:38px;border-radius:10px;background:var(--mint);color:#061512;font-size:20px;font-weight:800;display:grid;place-items:center;">إ</div>
                        <div>
                            <strong style="font-family:'Outfit',sans-serif;font-size:16px;color:#fff;display:block;">{{ $school->school_name }}</strong>
                            <small style="color:var(--mint);font-size:11px;letter-spacing:0.06em;">KARTU IDENTITAS SANTRI PENJELAJAH 3D</small>
                        </div>
                    </div>
                    <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--gold);background:rgba(244,196,68,0.15);padding:3px 8px;border-radius:6px;border:1px solid rgba(244,196,68,0.3);">
                        TAPEL 2026/2027
                    </span>
                </div>

                <div style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
                    <img id="card-photo-img" src="{{ asset('images/campus/fatih_avatar.jpg') }}" alt="Foto Santri" style="width:90px;height:105px;object-fit:cover;border-radius:14px;border:2.5px solid var(--gold);box-shadow:0 8px 20px rgba(0,0,0,0.5);">
                    <div style="flex:1;min-width:200px;">
                        <label style="font-size:11px;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.06em;display:block;">NAMA CALON SANTRI:</label>
                        <input type="text" id="card-custom-name" value="Fatih Al-Ayyubi" oninput="updateCardName(this.value)" style="width:100%;max-width:320px;background:rgba(255,255,255,0.1);border:1.5px solid var(--mint);border-radius:8px;padding:6px 12px;color:#fff;font-family:'Outfit',sans-serif;font-size:16px;font-weight:700;margin:4px 0 10px 0;outline:none;">
                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;">
                            <div>
                                <span style="color:rgba(255,255,255,0.6);display:block;font-size:10px;">GELAR PETUALANG:</span>
                                <strong id="card-rank-title" style="color:var(--gold);font-size:13px;">Bintang Pelopor 🌟</strong>
                            </div>
                            <div>
                                <span style="color:rgba(255,255,255,0.6);display:block;font-size:10px;">PERMATA TERKUMPUL:</span>
                                <strong id="card-star-display" style="color:var(--mint);font-size:13px;">0 Permata Bintang</strong>
                            </div>
                            <div>
                                <span style="color:rgba(255,255,255,0.6);display:block;font-size:10px;">ZONA DIJELAJAHI:</span>
                                <strong id="card-zone-display" style="color:#fff;font-size:13px;">0 / 10 Kawasan</strong>
                            </div>
                            <div>
                                <span style="color:rgba(255,255,255,0.6);display:block;font-size:10px;">STATUS REGISTRASI:</span>
                                <strong style="color:#86efac;font-size:13px;">Calon Santri Baru</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:16px;padding-top:12px;border-top:1px dashed rgba(255,255,255,0.15);display:flex;justify-content:space-between;align-items:center;font-size:11px;color:rgba(255,255,255,0.65);">
                    <span>Motto: &ldquo;Berakhlak Qur'ani, Unggul Prestasi, Menguasai Teknologi&rdquo;</span>
                    <span style="font-family:'JetBrains Mono',monospace;color:var(--mint);">VERIFIED-3D-STORY</span>
                </div>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <p style="font-size:12px;color:rgba(255,255,255,0.7);margin:0;">Ketik namamu di kartu lalu simpan atau cetak untuk kenang-kenangan!</p>
                <div style="display:flex;gap:10px;">
                    <button type="button" class="hud-btn-pill gold" onclick="window.print()">
                        🖨️ Cetak / Simpan Kartu
                    </button>
                    <button type="button" class="hud-btn-pill" onclick="closeModal()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. Interactive Quiz Minigame (Kuis Cerdas Santri Juara) -->
        <div id="tpl-quiz">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🧠</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Kuis Cerdas Santri Juara</h2>
                    <p style="color:var(--mint);font-size:12px;margin:0;">Uji Pengetahuan Islam &amp; Sains Bersama Sahabat Kampus</p>
                </div>
            </div>

            <div id="quiz-container" style="background:rgba(255,255,255,0.05);border:1.5px solid var(--mint);border-radius:20px;padding:22px;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <span style="font-size:12px;font-weight:700;color:var(--gold);" id="quiz-question-number">Pertanyaan 1 dari 5</span>
                    <span style="font-size:12px;color:var(--mint);font-weight:700;" id="quiz-score-badge">Skor: 0</span>
                </div>
                <h3 id="quiz-question-text" style="font-family:'Outfit',sans-serif;font-size:18px;margin-bottom:16px;line-height:1.5;color:#fff;">
                    Siapakah tokoh ilmuwan muslim yang dikenal sebagai penemu Aljabar dan dasar algoritma?
                </h3>
                <div id="quiz-options-box">
                    <button type="button" class="quiz-option-btn" onclick="answerQuiz(0)">A. Muhammad bin Musa Al-Khawarizmi</button>
                    <button type="button" class="quiz-option-btn" onclick="answerQuiz(1)">B. Ibnu Sina</button>
                    <button type="button" class="quiz-option-btn" onclick="answerQuiz(2)">C. Al-Biruni</button>
                    <button type="button" class="quiz-option-btn" onclick="answerQuiz(3)">D. Ibnu Khaldun</button>
                </div>
                <div id="quiz-feedback-box" style="display:none;margin-top:14px;padding:12px 16px;border-radius:12px;font-size:13px;line-height:1.5;"></div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" id="quiz-next-btn" class="hud-btn-pill" style="display:none;background:var(--mint);color:#061512;" onclick="nextQuizQuestion()">
                    Soal Berikutnya &rarr;
                </button>
                <button type="button" class="hud-btn-pill" onclick="closeModal()">
                    Tutup Kuis
                </button>
            </div>
        </div>

        <!-- 4. Interactive Campus Map & Fast Travel -->
        <div id="tpl-map">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🗺️</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Peta Kampus &amp; Teleport Cepat</h2>
                    <p style="color:var(--mint);font-size:12px;margin:0;">Jelajahi Setiap Sudut Sekolah {{ $school->school_name }} Sekejap Mata</p>
                </div>
            </div>

            <p style="color:rgba(255,255,255,0.8);font-size:13.5px;line-height:1.6;margin-bottom:16px;">
                Pilih kawasan tujuan untuk berpindah seketika (Fast Travel) ke lokasi yang kamu inginkan:
            </p>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;max-height:55vh;overflow-y:auto;padding-right:6px;">
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(0, 22); closeModal();" style="border-left:4px solid var(--mint);">
                    <strong style="color:var(--mint);display:block;">🏢 Lobi Utama &amp; Admisi</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Pintu masuk utama &amp; meja resepsionis</small>
                </button>
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(0, 8); closeModal();" style="border-left:4px solid var(--gold);">
                    <strong style="color:var(--gold);display:block;">🌟 Medali Balairung Utama</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Pusat aula dengan ornamen bintang delapan</small>
                </button>
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(18, -2); closeModal();" style="border-left:4px solid #10b981;">
                    <strong style="color:#10b981;display:block;">📖 Ruang Kelas Pembelajaran</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Meja siswa, papan tulis &amp; kurikulum</small>
                </button>
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(18, -22); closeModal();" style="border-left:4px solid var(--cyan);">
                    <strong style="color:var(--cyan);display:block;">🤖 Lab Robotika &amp; Sains</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Hologram bumi, robo-santri &amp; teleskop</small>
                </button>
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(-18, 0); closeModal();" style="border-left:4px solid #a855f7;">
                    <strong style="color:#a855f7;display:block;">📚 Perpustakaan Bayt Al-Hikmah</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Rak buku kayu, meja baca &amp; Al-Qur'an</small>
                </button>
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(-18, -22); closeModal();" style="border-left:4px solid var(--gold);">
                    <strong style="color:var(--gold);display:block;">🏆 Balairung Prestasi Santri</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Karpet merah &amp; lemari piala emas</small>
                </button>
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(0, -22); closeModal();" style="border-left:4px solid #14b8a6;">
                    <strong style="color:#14b8a6;display:block;">🕌 Musala Akbar Al-Ibrahim</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Sajadah zamrud, mihrab &amp; muroja'ah</small>
                </button>
                <button type="button" class="quiz-option-btn" onclick="teleportPlayer(-16, 10); closeModal();" style="border-left:4px solid var(--orange);">
                    <strong style="color:var(--orange);display:block;">📌 Mading &amp; Loker Santri</strong>
                    <small style="color:rgba(255,255,255,0.7);font-size:11.5px;">Agenda kegiatan &amp; kabar prestasi</small>
                </button>
            </div>
            
            <div style="display:flex;justify-content:flex-end;margin-top:16px;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()">
                    Tutup Peta
                </button>
            </div>
        </div>

        <!-- 5. Monument (Medali Balairung & Filosofi Keislaman) -->
        <div id="tpl-monument">
            <div class="speaker-vn-card">
                <img src="{{ asset('images/campus/hall_floor_emblem.png') }}" alt="Medali Balairung" class="speaker-vn-avatar" style="border-radius:50%;background:#09211c;">
                <div class="speaker-vn-info">
                    <span class="speaker-vn-badge">🌟 Medali Balairung Kampus &bull; Geometri Islami</span>
                    <h3>Pusat Filosofi &amp; Karakter Kampus</h3>
                    <p style="margin:0;color:rgba(255,255,255,0.8);font-size:12.5px;">Harmoni Akidah, Sains, dan Kemuliaan Adab {{ $school->school_name }}</p>
                </div>
            </div>
            <div style="background:linear-gradient(135deg, rgba(20,77,64,0.45) 0%, rgba(10,38,32,0.45) 100%);border:1.5px solid var(--mint);border-radius:18px;padding:20px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--gold);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">FILOSOFI ARSITEKTUR BALAIRUNG:</span>
                <p style="font-size:13.5px;color:#fff;line-height:1.7;margin-top:6px;">
                    Emblem ornamen geometris bintang delapan (Khatim Sulayman) di lantai balairung utama ini melambangkan keteraturan kosmos ciptaan Allah, keabadian tauhid, dan keseimbangan antara ilmu duniawi dan ukhrawi. Santri dididik berpijak kuat pada akidah dan terbang tinggi menguasai sains dunia.
                </p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;margin-bottom:16px;">
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--gold);font-size:13px;display:block;margin-bottom:4px;">📖 Adab &amp; Al-Qur'an</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Fondasi akidah lurus, akhlak mulia, dan hafalan mutqin bersanad sebagai kompas kehidupan.</p>
                </div>
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--mint);font-size:13px;display:block;margin-bottom:4px;">🌐 Wawasan Sains &amp; Global</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Riset ilmiah, matematika, teknologi komputasi modern, dan kemahiran bilingual Arab &amp; Inggris.</p>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;">
                    Tutup &rarr;
                </button>
            </div>
        </div>

        <!-- 6. Reception Desk (Meja Resepsionis & Profil Sekolah) -->
        <div id="tpl-reception">
            <div class="speaker-vn-card">
                <img src="{{ asset('images/campus/ustadzah_avatar.jpg') }}" alt="Ustadzah Resepsionis" class="speaker-vn-avatar">
                <div class="speaker-vn-info">
                    <span class="speaker-vn-badge">🧕 Hubungan Masyarakat &bull; Front Office Resepsionis</span>
                    <h3>Ustadzah Fatimah Azzahra, S.Pd.I</h3>
                    <p style="margin:0;color:rgba(255,255,255,0.8);font-size:12.5px;">Pusat Layanan Informasi, Profil &amp; Pendaftaran Santri Baru {{ $school->school_name }}</p>
                </div>
            </div>

            <!-- Welcoming Greeting Bubble -->
            <div style="background:linear-gradient(135deg, rgba(20,77,64,0.45) 0%, rgba(10,38,32,0.55) 100%);border:1.5px solid var(--mint);border-radius:18px;padding:18px 20px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--gold);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">SAMBUTAN HANGAT RESEPSIONIS:</span>
                <p style="font-size:14px;color:#fff;line-height:1.7;margin-top:6px;font-style:italic;">
                    &ldquo;Ahlan wa sahlan! Selamat datang di <strong>{{ $school->school_name }}</strong>. Saya siap membantu Ayah, Bunda, dan calon santri untuk mengenal lebih dekat lingkungan belajar, kultur adab, serta program keunggulan sekolah kami.&rdquo;
                </p>
            </div>

            <!-- Headline & Intro -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:12px;margin-bottom:16px;">
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:16px;padding:16px;">
                    <strong style="color:var(--gold);font-size:13.5px;display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                        <span>🌱</span> {{ $page->eyebrow ?: 'Kultur & Nilai Utama' }}
                    </strong>
                    <h4 style="font-family:'Outfit',sans-serif;font-size:16px;color:#fff;margin-bottom:6px;">{{ $page->headline }}</h4>
                    <p style="font-size:12.5px;color:rgba(255,255,255,0.8);line-height:1.6;margin:0;">{{ $page->intro }}</p>
                </div>

                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:16px;padding:16px;">
                    <strong style="color:var(--mint);font-size:13.5px;display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                        <span>📚</span> Pendekatan Pembelajaran
                    </strong>
                    <h4 style="font-family:'Outfit',sans-serif;font-size:16px;color:#fff;margin-bottom:6px;">{{ $page->about_title ?: 'Pendidikan yang Dekat dengan Kehidupan' }}</h4>
                    <p style="font-size:12.5px;color:rgba(255,255,255,0.8);line-height:1.6;margin:0;">{{ $page->about_body }}</p>
                </div>
            </div>

            <!-- Statistics / Angka Prestasi -->
            @if($statistics->isNotEmpty())
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:10px;margin-bottom:16px;">
                    @foreach($statistics as $stat)
                        <div style="background:rgba(45,226,166,0.08);border:1px solid rgba(45,226,166,0.25);border-radius:14px;padding:12px 14px;text-align:center;">
                            <span style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:800;color:var(--gold);display:block;">{{ $stat->title }}</span>
                            <small style="color:rgba(255,255,255,0.85);font-size:11px;font-weight:600;">{{ $stat->kicker }}</small>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Kontak & Alamat Sekolah -->
            <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:16px;margin-bottom:20px;">
                <span style="font-size:11px;color:rgba(255,255,255,0.6);font-weight:700;letter-spacing:0.08em;text-transform:uppercase;display:block;margin-bottom:10px;">INFORMASI KONTAK &amp; ALAMAT RESMI:</span>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:10px;font-size:12.5px;">
                    @if($page->address)
                        <div style="color:rgba(255,255,255,0.85);">
                            📍 <strong>Alamat:</strong><br>{{ $page->address }}
                        </div>
                    @endif
                    @if($page->email)
                        <div style="color:rgba(255,255,255,0.85);">
                            ✉️ <strong>Email:</strong><br><a href="mailto:{{ $page->email }}" style="color:var(--mint);text-decoration:none;">{{ $page->email }}</a>
                        </div>
                    @endif
                    @if($page->whatsapp)
                        <div style="color:rgba(255,255,255,0.85);">
                            💬 <strong>WhatsApp:</strong><br><span style="color:var(--gold);">+{{ $page->whatsapp }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Buttons -->
            <div style="display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
                @if($page->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $page->whatsapp) }}?text=Halo%20Ustadzah%20Resepsionis%20Sekolah%20Ibrahim,%20saya%20ingin%20bertanya%20mengenai%20profil%20dan%20pendaftaran%20sekolah" target="_blank" class="hud-btn-pill" style="background:var(--mint);color:#061512;font-size:13.5px;padding:10px 20px;">
                        💬 Hubungi Resepsionis via WhatsApp &rarr;
                    </a>
                @endif
                <button type="button" class="hud-btn-pill" onclick="openModal('admission')" style="background:rgba(244,196,68,0.2);color:var(--gold);border:1px solid rgba(244,196,68,0.4);font-size:13.5px;padding:10px 20px;">
                    🎓 Paket Biaya &amp; Admisi
                </button>
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:rgba(255,255,255,0.1);font-size:13.5px;padding:10px 20px;">
                    Tutup
                </button>
            </div>
        </div>

        <!-- 7. Admission Gate (Papan Rincian Paket Biaya & Admisi) -->
        <div id="tpl-admission">
            <div style="text-align:center;padding:10px 0;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--gold) 0%,var(--orange) 100%);display:grid;place-items:center;font-size:34px;margin:0 auto 16px;box-shadow:0 10px 25px rgba(244,196,68,0.45);">
                    🎓
                </div>
                <h2 style="font-family:'Outfit',sans-serif;font-size:26px;margin-bottom:10px;">Penerimaan Santri Baru Telah Dibuka!</h2>
                <p style="color:rgba(255,255,255,0.8);font-size:14px;max-width:540px;margin:0 auto 20px;line-height:1.6;">
                    Bergabunglah bersama keluarga besar {{ $school->school_name }}. Dampingi ananda bertumbuh menjadi generasi beriman, berilmu, dan berakhlak mulia.
                </p>

                @if($tuitionPackages->isNotEmpty())
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;margin-bottom:24px;text-align:left;">
                        @foreach($tuitionPackages as $pkg)
                            <div style="background:rgba(255,255,255,0.06);border:1.5px solid rgba(202,168,103,0.4);border-radius:16px;padding:16px;">
                                <strong style="display:block;font-size:14px;color:#fff;">{{ $pkg->name }}</strong>
                                <span style="font-family:'Outfit',sans-serif;font-weight:800;color:var(--gold);font-size:16px;display:block;margin:6px 0;">
                                    Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                                <small style="color:rgba(255,255,255,0.65);font-size:11px;">{{ $pkg->billing_period }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
                    @if($page->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $page->whatsapp) }}?text=Halo%20Admin%20Sekolah,%20saya%20tertarik%20mendaftar%20calon%20santri%20baru" target="_blank" class="hud-btn-pill" style="background:var(--mint);color:#061512;font-size:14px;padding:12px 24px;">
                            💬 Konsultasi Pendaftaran WhatsApp &rarr;
                        </a>
                    @else
                        <a href="{{ $page->primary_cta_url }}" class="hud-btn-pill" style="background:var(--mint);color:#061512;font-size:14px;padding:12px 24px;">
                            {{ $page->primary_cta_label }} &rarr;
                        </a>
                    @endif
                    <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:rgba(255,255,255,0.1);">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- 8. Cinema Screen (Video Profil) -->
        <div id="tpl-cinema">
            <h2 style="font-family:'Outfit',sans-serif;font-size:22px;margin-bottom:12px;">{{ $page->video_title }}</h2>
            @php
                $videoUrl = $page->video_url ?: 'https://www.youtube.com/watch?v=aqz-KE-bpKQ';
                $videoEmbed = null;
                if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~', (string)$videoUrl, $vMatch)) {
                    $videoEmbed = 'https://www.youtube-nocookie.com/embed/'.$vMatch[1];
                } elseif (preg_match('~vimeo\.com/(?:video/)?([0-9]+)~', (string)$videoUrl, $vMatch)) {
                    $videoEmbed = 'https://player.vimeo.com/video/'.$vMatch[1];
                }
            @endphp
            <div style="aspect-ratio:16/9;background:#000;border-radius:18px;overflow:hidden;border:2px solid var(--mint);margin-bottom:16px;">
                @if($videoEmbed)
                    <iframe src="{{ $videoEmbed }}" title="{{ $page->video_title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%;height:100%;border:none;"></iframe>
                @else
                    <div style="height:100%;display:grid;place-items:center;color:#fff;">Video Profil Kampus Siap Tayang</div>
                @endif
            </div>
            <p style="color:rgba(255,255,255,0.8);font-size:13.5px;line-height:1.6;margin:0;">
                Saksikan denyut pembelajaran terpadu, hafalan Al-Qur'an, dan pembiasaan adab harian di {{ $school->school_name }}.
            </p>
        </div>

        <!-- 9. Notice Board (Mading & Agenda Santri) -->
        <div id="tpl-notice">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">📌</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Papan Pengumuman &amp; Mading Sekolah</h2>
                    <p style="color:var(--mint);font-size:12px;margin:0;">Pusat Informasi &bull; Agenda Akademik &bull; Kabar Santri</p>
                </div>
            </div>
            <div style="background:linear-gradient(135deg, rgba(20,77,64,0.45) 0%, rgba(10,38,32,0.45) 100%);border:1.5px solid var(--mint);border-radius:18px;padding:20px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--gold);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">AGENDA TERBARU SANTRI:</span>
                <h3 style="font-family:'Newsreader',serif;font-size:20px;color:#fff;margin-top:6px;line-height:1.4;">
                    Kalender Kegiatan Belajar, Ujian Muroja'ah &amp; Perlombaan Bulan Ini
                </h3>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:12px;margin-bottom:16px;">
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <span style="display:inline-block;padding:2px 8px;border-radius:999px;background:rgba(45,226,166,0.2);color:var(--mint);font-size:10px;font-weight:700;margin-bottom:6px;">Akademik &amp; Tahfiz</span>
                    <strong style="color:#fff;font-size:13.5px;display:block;margin-bottom:4px;">Tasmi' Akbar 5 &amp; 10 Juz</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Penyetoran hafalan santri secara maraton di hadapan dewan asatidz dan wali santri.</p>
                </div>
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <span style="display:inline-block;padding:2px 8px;border-radius:999px;background:rgba(244,196,68,0.2);color:var(--gold);font-size:10px;font-weight:700;margin-bottom:6px;">Prestasi</span>
                    <strong style="color:#fff;font-size:13.5px;display:block;margin-bottom:4px;">Bintang Prestasi &bull; Olimpiade Sains</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Pemberian penghargaan medali dan beasiswa apresiasi santri berprestasi.</p>
                </div>
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <span style="display:inline-block;padding:2px 8px;border-radius:999px;background:rgba(255,118,68,0.2);color:var(--orange);font-size:10px;font-weight:700;margin-bottom:6px;">Ekstrakurikuler</span>
                    <strong style="color:#fff;font-size:13.5px;display:block;margin-bottom:4px;">Festival Seni &amp; Budaya Islami</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Pameran inovasi STEM santri dan kaligrafi kontemporer tingkat provinsi.</p>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;">
                    Tutup &rarr;
                </button>
            </div>
        </div>

        <!-- 10. Academic Programs (Ruang Kelas Pembelajaran) -->
        <div id="tpl-program">
            <div class="speaker-vn-card">
                <img src="{{ asset('images/campus/alya_avatar.jpg') }}" alt="Alya Nur Hikmah" class="speaker-vn-avatar">
                <div class="speaker-vn-info">
                    <span class="speaker-vn-badge">🦉 Duta Riset Santri &bull; Juara Olimpiade Sains</span>
                    <h3>Alya Nur Hikmah</h3>
                    <p style="margin:0;color:rgba(255,255,255,0.8);font-size:12.5px;">Selamat datang di Ruang Kelas Pembelajaran &bull; Kurikulum Terpadu {{ $school->school_name }}</p>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:12px;">
                @forelse($programs as $prog)
                    <div style="background:rgba(255,255,255,0.06);border:1.5px solid rgba(45,226,166,0.3);border-radius:16px;padding:18px;">
                        <span style="font-size:26px;display:block;margin-bottom:6px;">📖</span>
                        <h4 style="font-family:'Outfit',sans-serif;font-size:16px;color:#fff;margin-bottom:6px;">{{ $prog->title }}</h4>
                        <p style="font-size:12.5px;color:rgba(255,255,255,0.75);line-height:1.5;margin:0;">{{ $prog->body }}</p>
                    </div>
                @empty
                    <div style="background:rgba(255,255,255,0.06);border:1.5px solid rgba(45,226,166,0.3);border-radius:16px;padding:18px;">
                        <span style="font-size:26px;display:block;margin-bottom:6px;">📖</span>
                        <h4 style="font-size:16px;color:#fff;margin-bottom:6px;">Tahfiz Cilik Gemilang</h4>
                        <p style="font-size:12.5px;color:rgba(255,255,255,0.75);margin:0;">Pembinaan hafalan mutqin bersanad dengan tartil dan pemahaman adab.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 11. STEM, Robotics & Astronomy Lab (Pojok Robotika Al-Khawarizmi) -->
        <div id="tpl-robotics">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🤖</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Laboratorium Sains &amp; Robotika Islam</h2>
                    <p style="color:var(--cyan);font-size:12px;margin:0;">Pojok Sains Al-Khawarizmi &bull; Riset Terpadu &amp; Coding Qur'ani</p>
                </div>
            </div>

            <div style="background:linear-gradient(135deg, rgba(56,189,248,0.15) 0%, rgba(10,38,32,0.45) 100%);border:1.5px solid var(--cyan);border-radius:18px;padding:20px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--cyan);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">SAINS MODERN DENGAN CAHAYA AL-QUR'AN:</span>
                <p style="font-size:13.5px;color:#fff;line-height:1.75;margin-top:6px;">
                    Santri tidak hanya diajarkan menghafal Al-Qur'an, tetapi juga mengkaji ayat-ayat kauniyah (keajaiban alam semesta). Dilengkapi fasilitas robotika terpadu, 3D printing, pemrograman mikroprosesor, teleskop observasi astronomi syar'i (penentuan awal bulan hijriyah), dan kecerdasan buatan (AI) edukasi.
                </p>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;margin-bottom:16px;">
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--cyan);font-size:13px;display:block;margin-bottom:4px;">💻 Robotika &amp; IoT</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Karya robot penyiram tanaman otomatis, otomasi bel adzan, dan line-follower santri.</p>
                </div>
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--gold);font-size:13px;display:block;margin-bottom:4px;">🔭 Falak &amp; Astronomi</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Praktik hisab rukyat hilal dan observasi perbintangan langit ciptaan Allah SWT.</p>
                </div>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <button type="button" class="hud-btn-pill gold" onclick="openModal('quiz')">
                    🧠 Buka Kuis Sains &amp; Islam &rarr;
                </button>
                <button type="button" class="hud-btn-pill" onclick="closeModal()">
                    Tutup &rarr;
                </button>
            </div>
        </div>

        <!-- 12. Digital Library (Perpustakaan Digital Bayt Al-Hikmah) -->
        <div id="tpl-library">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">📚</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Perpustakaan Digital Bayt Al-Hikmah</h2>
                    <p style="color:#a855f7;font-size:12px;margin:0;">Pusat Khazanah Literasi Islam, Sains &amp; Bilik Belajar Modern</p>
                </div>
            </div>

            <div style="background:linear-gradient(135deg, rgba(168,85,247,0.15) 0%, rgba(10,38,32,0.45) 100%);border:1.5px solid #a855f7;border-radius:18px;padding:20px;margin-bottom:16px;">
                <span style="font-size:11px;color:#c084fc;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">INSPIRASI KEJAYAAN ILMU ISLAM:</span>
                <p style="font-size:13.5px;color:#fff;line-height:1.75;margin-top:6px;">
                    Mengadopsi nama perpustakaan legendaris era keemasan Islam &ldquo;Bayt Al-Hikmah&rdquo;, perpustakaan sekolah ini menyediakan koleksi kitab turots klasik, tafsir Al-Qur'an, ensiklopedia sains, e-book reader digital, serta meja baca kayu yang tenang dan nyaman bagi santri.
                </p>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;margin-bottom:16px;">
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--gold);font-size:13px;display:block;margin-bottom:4px;">📖 Koleksi Kitab &amp; Tafsir</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Mushaf Al-Qur'an terjemah tematik, kitab hadits shahih, sirah nabawiyah, dan karya ulama mu'tabar.</p>
                </div>
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--mint);font-size:13px;display:block;margin-bottom:4px;">📱 E-Library Santri</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Akses ribuan e-book literasi umum, novel edukasi Islami, dan jurnal riset berbahasa Arab &amp; Inggris.</p>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;">
                    Tutup &rarr;
                </button>
            </div>
        </div>

        <!-- 13. Trophy Podium (Prestasi & Champions) -->
        <div id="tpl-trophy">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🏆</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Balairung Prestasi &amp; Medali</h2>
                    <p style="color:var(--gold);font-size:12px;margin:0;">Etalase Juara &bull; Bintang Prestasi Santri</p>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:14px;max-height:55vh;overflow-y:auto;padding-right:6px;">
                @forelse($champions as $champ)
                    <div style="background:rgba(255,255,255,0.06);border:1.5px solid rgba(244,196,68,0.35);border-radius:16px;padding:16px;">
                        <span style="display:inline-block;padding:3px 10px;border-radius:999px;background:rgba(244,196,68,0.2);color:var(--gold);font-size:10px;font-weight:700;margin-bottom:8px;">
                            {{ $champ->kicker ?: 'Juara Utama' }}
                        </span>
                        <h4 style="font-family:'Outfit',sans-serif;font-size:16px;color:#fff;margin-bottom:6px;">{{ $champ->title }}</h4>
                        <p style="font-size:12.5px;color:rgba(255,255,255,0.75);line-height:1.5;margin:0;">{{ $champ->body }}</p>
                    </div>
                @empty
                    <div style="background:rgba(255,255,255,0.06);border:1.5px solid rgba(244,196,68,0.35);border-radius:16px;padding:16px;">
                        <h4 style="font-size:16px;color:var(--gold);margin-bottom:6px;">Juara Sains &amp; Tahfiz Qur'an</h4>
                        <p style="font-size:12.5px;color:rgba(255,255,255,0.75);margin:0;">Catatan medali olimpiade sains, robotika nasional, dan musabaqah tahfidz terbaru santri.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 14. Mosque (Visi & Misi Keislaman) -->
        <div id="tpl-mosque">
            <div class="speaker-vn-card">
                <img src="{{ asset('images/campus/ustadz_avatar.jpg') }}" alt="Ustadz Pembina" class="speaker-vn-avatar">
                <div class="speaker-vn-info">
                    <span class="speaker-vn-badge">🕌 Dewan Pembina Tahfiz &bull; Ruhiyah Santri</span>
                    <h3>Ustadz Salman Al-Farisi, Lc.</h3>
                    <p style="margin:0;color:rgba(255,255,255,0.8);font-size:12.5px;">Pembina Karakter Islami &bull; Musala Kampus {{ $school->school_name }}</p>
                </div>
            </div>
            <div style="background:linear-gradient(135deg, rgba(20,77,64,0.45) 0%, rgba(10,38,32,0.45) 100%);border:1.5px solid var(--mint);border-radius:18px;padding:22px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--gold);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">VISI PENDIDIKAN QURANI:</span>
                <h3 style="font-family:'Newsreader',serif;font-size:23px;color:#fff;font-style:italic;margin-top:6px;line-height:1.4;">
                    &ldquo;{{ $page->vision ?: 'Generasi qurani berkarakter mulia.' }}&rdquo;
                </h3>
            </div>
            <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.15);border-radius:18px;padding:20px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--mint);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;display:block;margin-bottom:10px;">MISI STRATEGIS:</span>
                <p style="font-size:13.5px;color:rgba(255,255,255,0.85);line-height:1.75;margin:0;white-space:pre-line;">{{ $page->mission }}</p>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-top:16px;">
                <button type="button" class="hud-btn-pill gold" onclick="playQuranMurajaahSound()">
                    🎵 Putar Alunan Muroja'ah
                </button>
                <button type="button" class="hud-btn-pill" onclick="closeModal()">
                    Tutup &rarr;
                </button>
            </div>
        </div>
    </div>

    @php
        $packagesJson = $tuitionPackages->map(function($p) {
            return [
                'name' => $p->name,
                'price' => 'Rp ' . number_format($p->price, 0, ',', '.'),
                'billing_period' => $p->billing_period,
                'description' => $p->description,
            ];
        })->values();
    @endphp

    <!-- ==============================================================
         THREE.JS 3D ENGINE — SOLID ARCHITECTURAL ISLAMIC SCHOOL
         ============================================================== -->
    <script>
        let audioSoundOn = true;
        let cameraDistanceMode = 0; // 0: Normal TPV, 1: Close TPV, 2: Bird-Eye
        let activeCheckpointKey = null;
        let audioCtx = null;
        let isSprintActive = false;

        // Sound Synthesis (Web Audio API)
        function initAudio() {
            if (!audioCtx) {
                const AudioClass = window.AudioContext || window.webkitAudioContext;
                if (AudioClass) audioCtx = new AudioClass();
            }
            if (audioCtx && audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
        }

        function playTone(freq, type = 'sine', duration = 0.15, vol = 0.1) {
            if (!audioSoundOn) return;
            try {
                initAudio();
                if (!audioCtx) return;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
                gain.gain.setValueAtTime(vol, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + duration);
            } catch(e) {}
        }

        function playStepSound() {
            playTone(160, 'triangle', 0.05, 0.03);
        }

        function playJumpSound() {
            playTone(280, 'sine', 0.12, 0.08);
            setTimeout(() => playTone(440, 'sine', 0.15, 0.08), 60);
        }

        function playCollectChime() {
            const freqs = [659.25, 880, 1046.5, 1318.5];
            freqs.forEach((f, i) => {
                setTimeout(() => playTone(f, 'sine', 0.2, 0.12), i * 75);
            });
        }

        function playFanfare() {
            const freqs = [523.25, 659.25, 783.99, 1046.5];
            freqs.forEach((f, i) => {
                setTimeout(() => playTone(f, 'triangle', 0.3, 0.18), i * 110);
            });
        }

        function playCatMeow() {
            if (!audioSoundOn) return;
            try {
                initAudio();
                if (!audioCtx) return;
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'triangle';
                // Cute rising and falling cat meow formant pitch
                osc.frequency.setValueAtTime(460, now);
                osc.frequency.exponentialRampToValueAtTime(840, now + 0.12);
                osc.frequency.exponentialRampToValueAtTime(540, now + 0.36);

                gain.gain.setValueAtTime(0.001, now);
                gain.gain.linearRampToValueAtTime(0.14, now + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.38);

                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.40);
            } catch(e) {}
        }

        function playQuranMurajaahSound() {
            initAudio();
            const maqam = [392, 440, 493.88, 523.25, 587.33, 659.25, 783.99];
            maqam.forEach((freq, idx) => {
                setTimeout(() => {
                    playTone(freq, 'sine', 0.6, 0.08);
                }, idx * 450);
            });
            showCampusToast("📖 <em>Alunan Muroja'ah Syahdu bergema di Musala...</em>");
        }

        function toggleAudio() {
            audioSoundOn = !audioSoundOn;
            const icon = document.getElementById('audio-icon-box');
            const text = document.getElementById('audio-text-box');
            if (audioSoundOn) {
                icon.textContent = '🔊';
                text.textContent = 'Suara: On';
                playTone(600, 'sine', 0.15, 0.1);
            } else {
                icon.textContent = '🔇';
                text.textContent = 'Suara: Off';
            }
        }

        // ==============================================================
        // Three.js Scene Setup (Indoor Modern Islamic School Hall)
        // ==============================================================
        const container = document.getElementById('game-canvas-container');
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0xd7eae5);
        scene.fog = new THREE.FogExp2(0xd7eae5, 0.008);

        const camera = new THREE.PerspectiveCamera(52, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: 'high-performance' });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.18;
        container.appendChild(renderer.domElement);

        // Ambient Light Setup
        const ambientLight = new THREE.AmbientLight(0xfff7ee, 1.25);
        scene.add(ambientLight);

        // Sunlight Stream
        const sunLight = new THREE.DirectionalLight(0xfffaea, 1.4);
        sunLight.position.set(25, 45, 20);
        sunLight.castShadow = true;
        sunLight.shadow.mapSize.width = 2048;
        sunLight.shadow.mapSize.height = 2048;
        sunLight.shadow.camera.near = 5;
        sunLight.shadow.camera.far = 130;
        const d = 45;
        sunLight.shadow.camera.left = -d;
        sunLight.shadow.camera.right = d;
        sunLight.shadow.camera.top = d;
        sunLight.shadow.camera.bottom = -d;
        sunLight.shadow.bias = -0.0004;
        scene.add(sunLight);

        // Architectural Textures (Utilized purely as surface materials, no floating billboards!)
        const textureLoader = new THREE.TextureLoader();
        const tileTexture = textureLoader.load('{{ asset("images/campus/courtyard_tiles.jpg") }}');
        tileTexture.wrapS = THREE.RepeatWrapping;
        tileTexture.wrapT = THREE.RepeatWrapping;
        tileTexture.repeat.set(16, 20);

        const carpetTexture = textureLoader.load('{{ asset("images/campus/prayer_carpet.jpg") }}');
        carpetTexture.wrapS = THREE.RepeatWrapping;
        carpetTexture.wrapT = THREE.RepeatWrapping;
        carpetTexture.repeat.set(4, 3);

        const mosqueTexture = textureLoader.load('{{ asset("images/campus/mosque_facade.jpg") }}');
        const tvTexture = textureLoader.load('{{ asset("images/campus/smart_tv_screen.jpg") }}');
        const hallFloorEmblemTexture = textureLoader.load('{{ asset("images/campus/hall_floor_emblem.png") }}');

        // Dynamic Canvas Textures
        function createTuitionBoardTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1350;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#0a2620';
            ctx.fillRect(0, 0, 1024, 1350);

            const grad = ctx.createLinearGradient(0, 0, 1024, 200);
            grad.addColorStop(0, '#144d40');
            grad.addColorStop(1, '#0e382e');
            ctx.fillStyle = grad;
            ctx.fillRect(20, 20, 984, 180);

            ctx.strokeStyle = '#f4c444';
            ctx.lineWidth = 4;
            ctx.strokeRect(20, 20, 984, 1310);

            ctx.fillStyle = '#f4c444';
            ctx.font = 'bold 36px "Outfit", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('RINCIAN PAKET BIAYA & ADMISI', 512, 85);

            ctx.fillStyle = '#ffffff';
            ctx.font = '22px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('TAHUN AJARAN BARU 2026/2027', 512, 128);

            ctx.fillStyle = '#2de2a6';
            ctx.font = 'bold 18px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('SEKOLAH ISLAM TERPADU BERAKHLAK QUR\'ANI', 512, 165);

            const packages = @json($packagesJson);
            let startY = 240;

            if (packages && packages.length > 0) {
                packages.forEach((pkg) => {
                    if (startY > 1150) return;
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
                    ctx.fillRect(40, startY, 944, 135);
                    ctx.strokeStyle = 'rgba(45, 226, 166, 0.35)';
                    ctx.lineWidth = 1.5;
                    ctx.strokeRect(40, startY, 944, 135);

                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 26px "Outfit", sans-serif';
                    ctx.textAlign = 'left';
                    ctx.fillText(pkg.name, 65, startY + 45);

                    ctx.fillStyle = '#f4c444';
                    ctx.font = 'bold 32px "Outfit", sans-serif';
                    ctx.textAlign = 'right';
                    ctx.fillText(pkg.price, 960, startY + 48);

                    ctx.fillStyle = 'rgba(255, 255, 255, 0.65)';
                    ctx.font = '18px "Plus Jakarta Sans", sans-serif';
                    ctx.textAlign = 'left';
                    ctx.fillText(pkg.billing_period || 'Per Semester / Tahun', 65, startY + 80);

                    if (pkg.description) {
                        ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
                        ctx.font = 'italic 17px "Plus Jakarta Sans", sans-serif';
                        ctx.fillText(pkg.description.substring(0, 75), 65, startY + 112);
                    }
                    startY += 150;
                });
            }

            ctx.fillStyle = '#2de2a6';
            ctx.font = 'bold 20px "Outfit", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('KONSULTASI & PENDAFTARAN RESMI DI MEJA RESEPSIONIS', 512, 1285);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        function createNoticeBoardTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 680;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#11332a';
            ctx.fillRect(0, 0, 1024, 680);
            ctx.strokeStyle = '#2de2a6';
            ctx.lineWidth = 5;
            ctx.strokeRect(10, 10, 1004, 660);

            ctx.fillStyle = '#2de2a6';
            ctx.font = 'bold 34px "Outfit", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('📌 PAPAN PENGUMUMAN & MADING SANTRI', 512, 60);

            const notices = [
                { tag: "AKADEMIK", title: "Tasmi' Akbar 5 & 10 Juz", desc: "Penyetoran hafalan santri maraton di hadapan dewan asatidz." },
                { tag: "PRESTASI", title: "Medali Emas Olimpiade Sains", desc: "Selamat kepada santri peraih medali kompetisi fisika & robotika." },
                { tag: "EKSTRA", title: "Festival Robotika & Seni", desc: "Kompetisi karya ilmiah dan pameran kaligrafi kontemporer santri." }
            ];

            let xOff = 30;
            notices.forEach(n => {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.08)';
                ctx.fillRect(xOff, 100, 305, 520);
                ctx.strokeStyle = 'rgba(244, 196, 68, 0.4)';
                ctx.lineWidth = 2;
                ctx.strokeRect(xOff, 100, 305, 520);

                ctx.fillStyle = '#f4c444';
                ctx.font = 'bold 15px "Outfit", sans-serif';
                ctx.textAlign = 'left';
                ctx.fillText(n.tag, xOff + 20, 140);

                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 20px "Outfit", sans-serif';
                ctx.fillText(n.title, xOff + 20, 180);

                ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                ctx.font = '16px "Plus Jakarta Sans", sans-serif';
                const words = n.desc.split(' ');
                let line = '';
                let y = 225;
                for (let w of words) {
                    if ((line + w).length > 22) {
                        ctx.fillText(line, xOff + 20, y);
                        line = w + ' ';
                        y += 26;
                    } else {
                        line += w + ' ';
                    }
                }
                ctx.fillText(line, xOff + 20, y);
                xOff += 325;
            });

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        function createWhiteboardTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 512;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#f8fafc';
            ctx.fillRect(0, 0, 1024, 512);

            ctx.fillStyle = '#0f172a';
            ctx.font = 'bold 32px "Outfit", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('AGENDA BELAJAR KELAS TAHFIZ & SAINS TERPADU', 512, 60);

            ctx.strokeStyle = '#059669';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(100, 85);
            ctx.lineTo(924, 85);
            ctx.stroke();

            ctx.textAlign = 'left';
            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 24px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('1. Muroja\'ah Surat Al-Mulk ayat 1-15 (Tartil & Tajwid)', 120, 150);
            ctx.fillText('2. Eksplorasi Sains: Siklus Air & Keajaiban Alam Semesta', 120, 220);
            ctx.fillText('3. Praktikum Robotika: Logika Pemrograman & Algoritma', 120, 290);
            ctx.fillText('4. Percakapan Bahasa Arab & Inggris Harian (Muhadatsah)', 120, 360);

            ctx.fillStyle = '#047857';
            ctx.font = 'italic bold 22px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('Motto: "Ikatlah ilmu dengan menuliskannya."', 120, 440);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        function createSpeechBubbleTexture(headerText, mainText, subText) {
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 256;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = 'rgba(10, 38, 32, 0.95)';
            ctx.beginPath();
            ctx.roundRect(10, 10, 492, 190, 24);
            ctx.fill();

            ctx.strokeStyle = '#2de2a6';
            ctx.lineWidth = 4;
            ctx.stroke();

            ctx.beginPath();
            ctx.moveTo(230, 200);
            ctx.lineTo(256, 235);
            ctx.lineTo(282, 200);
            ctx.closePath();
            ctx.fillStyle = 'rgba(10, 38, 32, 0.95)';
            ctx.fill();
            ctx.strokeStyle = '#2de2a6';
            ctx.lineWidth = 3;
            ctx.stroke();

            ctx.textAlign = 'center';
            ctx.fillStyle = '#f4c444';
            ctx.font = 'bold 18px "Outfit", sans-serif';
            ctx.fillText(headerText, 256, 52);

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 28px "Outfit", sans-serif';
            ctx.fillText(mainText, 256, 105);

            ctx.fillStyle = '#2de2a6';
            ctx.font = '16px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(subText, 256, 155);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        // ==============================================================
        // GROUND RING INTERACTION MARKERS & CHECKPOINTS
        // ==============================================================
        const groundRings = [];
        function createGroundedRing(x, z, radius = 3.6, color = 0x2de2a6) {
            const ringGeo = new THREE.RingGeometry(radius * 0.85, radius, 32);
            const ringMat = new THREE.MeshBasicMaterial({ 
                color: color, 
                side: THREE.DoubleSide, 
                transparent: true, 
                opacity: 0.75 
            });
            const ring = new THREE.Mesh(ringGeo, ringMat);
            ring.rotation.x = -Math.PI / 2;
            ring.position.set(x, 0.04, z);
            scene.add(ring);
            groundRings.push(ring);
            return ring;
        }

        const checkpoints = [];

        // ==============================================================
        // 1. SOLID ENCLOSING WALLS, SKIRTING & FLOOR
        // ==============================================================
        const floorGeo = new THREE.PlaneGeometry(64, 76);
        const floorMat = new THREE.MeshStandardMaterial({
            map: tileTexture,
            roughness: 0.28,
            metalness: 0.08
        });
        const schoolFloor = new THREE.Mesh(floorGeo, floorMat);
        schoolFloor.rotation.x = -Math.PI / 2;
        schoolFloor.position.set(2, 0.02, 0);
        schoolFloor.receiveShadow = true;
        scene.add(schoolFloor);

        const wallMat = new THREE.MeshLambertMaterial({ color: 0xf6f3ea });
        const skirtingMat = new THREE.MeshLambertMaterial({ color: 0x144d40 });
        const goldMat = new THREE.MeshStandardMaterial({ color: 0xf4c444, roughness: 0.25, metalness: 0.85 });

        function buildWall(x, z, w, h, d) {
            const wall = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), wallMat);
            wall.position.set(x, h / 2, z);
            wall.receiveShadow = true;
            wall.castShadow = true;
            scene.add(wall);

            const skirt = new THREE.Mesh(new THREE.BoxGeometry(w + 0.05, 0.45, d + 0.05), skirtingMat);
            skirt.position.set(x, 0.225, z);
            scene.add(skirt);
            return wall;
        }

        // Outer Enclosing Walls (Solid Architectural Shell)
        // North Wall (Behind Musala)
        buildWall(2, -36, 56, 9.0, 1.2);
        // South Wall (Lobby & Entrance)
        buildWall(-14, 36, 26, 9.0, 1.2);
        buildWall(18, 36, 24, 9.0, 1.2);
        // West Wall (Along Library, Mading & Trophies)
        buildWall(-24, 0, 1.2, 9.0, 74);
        // East Wall (Along Classroom & STEM Lab)
        buildWall(28, 0, 1.2, 9.0, 74);

        // Ceiling Plane with Beams and Downlights
        const ceilingGeo = new THREE.PlaneGeometry(54, 74);
        const ceilingMat = new THREE.MeshLambertMaterial({ color: 0xf3efe6, side: THREE.DoubleSide });
        const indoorCeiling = new THREE.Mesh(ceilingGeo, ceilingMat);
        indoorCeiling.rotation.x = Math.PI / 2;
        indoorCeiling.position.set(2, 9.0, 0);
        scene.add(indoorCeiling);

        for (let bz = -30; bz <= 30; bz += 12) {
            const beam = new THREE.Mesh(new THREE.BoxGeometry(54, 0.65, 0.8), new THREE.MeshLambertMaterial({ color: 0x1b4d3e }));
            beam.position.set(2, 8.8, bz);
            scene.add(beam);

            [-14, -4, 6, 18].forEach(bx => {
                const lamp = new THREE.Mesh(new THREE.CylinderGeometry(0.35, 0.35, 0.1, 16), new THREE.MeshBasicMaterial({ color: 0xfff6dd }));
                lamp.position.set(bx, 8.45, bz);
                scene.add(lamp);
            });
        }

        // ==============================================================
        // 2. STATELY PILLARS (STRATEGICALLY PLACED WITH ZERO COLLISIONS)
        // ==============================================================
        // Colonnade positioned neatly along X = -10 and X = +10, leaving all rooms,
        // library, classroom, and doors completely unobstructed and free!
        const pillarMat = new THREE.MeshLambertMaterial({ color: 0xede6d8 });
        const pillarPositions = [
            [-10, 16],  [10, 16],  // In front of lobby/reception area
            [-10, 8],   [10, 8],   // Flanking the central floor medallion
            [-10, -8],  [10, -8],  // In the corridor between rooms
            [-10, -18], [10, -18]  // Flanking the Musala grand entrance
        ];

        pillarPositions.forEach(([px, pz]) => {
            const col = new THREE.Mesh(new THREE.CylinderGeometry(0.65, 0.72, 8.8, 16), pillarMat);
            col.position.set(px, 4.4, pz);
            col.castShadow = true;
            col.receiveShadow = true;
            scene.add(col);

            const ringTop = new THREE.Mesh(new THREE.CylinderGeometry(0.85, 0.85, 0.3, 16), goldMat);
            ringTop.position.set(px, 8.5, pz);
            scene.add(ringTop);

            const ringBottom = new THREE.Mesh(new THREE.CylinderGeometry(0.85, 0.85, 0.3, 16), goldMat);
            ringBottom.position.set(px, 0.15, pz);
            scene.add(ringBottom);
        });

        // ==============================================================
        // 3. CENTRAL ATRIUM FLOOR MEDALLION (BINTANG DELAPAN KHATIM SULAYMAN)
        // ==============================================================
        const hallMedallionGroup = new THREE.Group();
        hallMedallionGroup.position.set(0, 0, 8);

        const medallionGeo = new THREE.CircleGeometry(4.6, 64);
        const medallionMat = new THREE.MeshStandardMaterial({
            map: hallFloorEmblemTexture,
            transparent: true,
            roughness: 0.35,
            metalness: 0.2
        });
        const medallionMesh = new THREE.Mesh(medallionGeo, medallionMat);
        medallionMesh.rotation.x = -Math.PI / 2;
        medallionMesh.position.y = 0.035;
        medallionMesh.receiveShadow = true;
        hallMedallionGroup.add(medallionMesh);

        const ringOuterGeo = new THREE.RingGeometry(4.55, 4.85, 64);
        const ringOuter = new THREE.Mesh(ringOuterGeo, goldMat);
        ringOuter.rotation.x = -Math.PI / 2;
        ringOuter.position.y = 0.038;
        hallMedallionGroup.add(ringOuter);

        // 4 Cardinal Uplights
        [[0, 5.2], [0, -5.2], [5.2, 0], [-5.2, 0]].forEach(([ux, uz]) => {
            const upLightHousing = new THREE.Mesh(
                new THREE.CylinderGeometry(0.24, 0.28, 0.08, 16),
                new THREE.MeshLambertMaterial({ color: 0x144d40 })
            );
            upLightHousing.position.set(ux, 0.04, uz);
            hallMedallionGroup.add(upLightHousing);

            const upLight = new THREE.PointLight(0xfff1cc, 0.8, 8.5);
            upLight.position.set(ux, 0.6, uz);
            hallMedallionGroup.add(upLight);
        });

        scene.add(hallMedallionGroup);
        createGroundedRing(0, 8, 4.8);

        checkpoints.push({
            name: "Medali Balairung &amp; Filosofi Keislaman",
            subtitle: "Tekan [E] atau Sentuh untuk membaca Filosofi Kampus",
            type: "monument",
            x: 0,
            z: 8,
            radius: 4.8
        });

        // Floating Sunbeam Dust Motes
        const dustCount = 140;
        const dustGeo = new THREE.BufferGeometry();
        const dustPositions = new Float32Array(dustCount * 3);
        const dustVelocities = [];

        for (let i = 0; i < dustCount; i++) {
            dustPositions[i * 3] = (Math.random() - 0.5) * 38;
            dustPositions[i * 3 + 1] = 0.5 + Math.random() * 8.0;
            dustPositions[i * 3 + 2] = (Math.random() - 0.5) * 48;
            dustVelocities.push({
                vy: 0.003 + Math.random() * 0.007,
                swaySpeed: 0.8 + Math.random() * 1.5,
                swayRadius: 0.004 + Math.random() * 0.006,
                phase: Math.random() * Math.PI * 2
            });
        }
        dustGeo.setAttribute('position', new THREE.BufferAttribute(dustPositions, 3));
        const dustMat = new THREE.PointsMaterial({
            color: 0xfef08a,
            size: 0.16,
            transparent: true,
            opacity: 0.75,
            blending: THREE.AdditiveBlending
        });
        const dustParticles = new THREE.Points(dustGeo, dustMat);
        scene.add(dustParticles);

        // ==============================================================
        // 4. LOBI UTAMA: RESEPSIONIS, PAPAN BIAYA & SMART TV
        // ==============================================================
        // A. Standing Display Board for Tuition Packages
        const tuitionBoardGroup = new THREE.Group();
        tuitionBoardGroup.position.set(-8, 0, 20);

        const tPlinth = new THREE.Mesh(new THREE.BoxGeometry(6.4, 0.35, 1.2), new THREE.MeshLambertMaterial({ color: 0x1f2937 }));
        tPlinth.position.y = 0.18;
        tuitionBoardGroup.add(tPlinth);

        [-2.8, 2.8].forEach(px => {
            const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.08, 7.8, 12), goldMat);
            pole.position.set(px, 3.9, 0);
            tuitionBoardGroup.add(pole);
        });

        const tBoard = new THREE.Mesh(new THREE.BoxGeometry(5.8, 7.2, 0.15), new THREE.MeshLambertMaterial({ color: 0x0a2620 }));
        tBoard.position.y = 4.4;
        tuitionBoardGroup.add(tBoard);

        const tFace = new THREE.Mesh(new THREE.PlaneGeometry(5.6, 7.0), new THREE.MeshLambertMaterial({ map: createTuitionBoardTexture() }));
        tFace.position.set(0, 4.4, 0.09);
        tuitionBoardGroup.add(tFace);

        scene.add(tuitionBoardGroup);
        createGroundedRing(-8, 22, 4.5);

        checkpoints.push({
            name: "Papan Rincian Paket Biaya &amp; Admisi",
            subtitle: "Tekan [E] atau Sentuh untuk Konsultasi Pendaftaran",
            type: "admission",
            x: -8,
            z: 22,
            radius: 5.2
        });

        // B. Reception Counter Desk & Ustadzah Fatimah
        const receptionGroup = new THREE.Group();
        receptionGroup.position.set(8, 0, 20);

        const rDesk = new THREE.Mesh(new THREE.BoxGeometry(6.4, 2.2, 1.8), new THREE.MeshLambertMaterial({ color: 0x244238 }));
        rDesk.position.y = 1.1;
        receptionGroup.add(rDesk);

        const rTop = new THREE.Mesh(new THREE.BoxGeometry(6.6, 0.15, 2.0), new THREE.MeshLambertMaterial({ color: 0xded5c3 }));
        rTop.position.y = 2.25;
        receptionGroup.add(rTop);

        const rPlaque = new THREE.Mesh(new THREE.BoxGeometry(3.2, 0.6, 0.08), goldMat);
        rPlaque.position.set(0, 1.35, 0.95);
        receptionGroup.add(rPlaque);

        const monitor = new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.8, 0.1), new THREE.MeshLambertMaterial({ color: 0x111111 }));
        monitor.position.set(1.9, 2.75, -0.2);
        monitor.rotation.y = -0.15;
        receptionGroup.add(monitor);

        scene.add(receptionGroup);
        createGroundedRing(8, 22.5, 4.5);

        checkpoints.push({
            name: "Meja Resepsionis &amp; Profil Sekolah",
            subtitle: "Disambut Ustadzah &bull; Tekan [E] untuk Profil Sekolah",
            type: "reception",
            x: 8,
            z: 22.5,
            radius: 5.2
        });

        // NPC Ustadzah at Reception Desk
        const npcDesk = new THREE.Group();
        npcDesk.position.set(8, 0, 18.2);

        const ndBody = new THREE.Mesh(new THREE.CylinderGeometry(0.44, 0.48, 1.35, 18), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        ndBody.position.y = 1.95;
        npcDesk.add(ndBody);

        const ndHijabMat = new THREE.MeshLambertMaterial({ color: 0x2de2a6 });
        const ndHeadGroup = new THREE.Group();
        ndHeadGroup.position.set(0, 3.08, 0);

        const faceSkin = new THREE.Mesh(new THREE.SphereGeometry(0.48, 20, 20), new THREE.MeshLambertMaterial({ color: 0xfce1cc }));
        faceSkin.position.set(0, 0, 0.02);
        ndHeadGroup.add(faceSkin);

        const hijabBack = new THREE.Mesh(new THREE.SphereGeometry(0.51, 20, 20), ndHijabMat);
        hijabBack.position.set(0, 0.03, -0.10);
        ndHeadGroup.add(hijabBack);

        [-0.16, 0.16].forEach(ex => {
            const eye = new THREE.Mesh(new THREE.SphereGeometry(0.075, 12, 12), new THREE.MeshBasicMaterial({ color: 0x0f172a }));
            eye.scale.set(0.85, 1.35, 0.35);
            eye.position.set(ex, 0.04, 0.478);
            ndHeadGroup.add(eye);
        });

        npcDesk.add(ndHeadGroup);

        const ndArmRGroup = new THREE.Group();
        ndArmRGroup.position.set(0.62, 2.3, 0.1);
        const ndArmR = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.11, 0.85, 12), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        ndArmR.position.y = -0.42;
        ndArmRGroup.add(ndArmR);
        npcDesk.add(ndArmRGroup);

        const bubbleTexture = createSpeechBubbleTexture('🧕 RESEPSIONIS', 'Selamat Datang! 👋', 'Tekan [E] untuk Profil Sekolah');
        const bubbleSprite = new THREE.Sprite(new THREE.SpriteMaterial({ map: bubbleTexture, transparent: true }));
        bubbleSprite.scale.set(3.8, 1.9, 1.0);
        bubbleSprite.position.set(0, 4.35, 0);
        npcDesk.add(bubbleSprite);

        npcDesk.userData = { head: ndHeadGroup, rightArm: ndArmRGroup, bubble: bubbleSprite };
        scene.add(npcDesk);

        // C. Smart TV Wall Frame
        const tvGroup = new THREE.Group();
        tvGroup.position.set(-17, 0, 21.5);

        const tvFrame = new THREE.Mesh(new THREE.BoxGeometry(7.4, 4.4, 0.18), new THREE.MeshLambertMaterial({ color: 0x0f172a }));
        tvFrame.position.set(0, 4.2, 0.05);
        tvGroup.add(tvFrame);

        const tvScreen = new THREE.Mesh(new THREE.PlaneGeometry(7.2, 4.2), new THREE.MeshBasicMaterial({ map: tvTexture }));
        tvScreen.position.set(0, 4.2, 0.16);
        tvGroup.add(tvScreen);

        const tvGlow = new THREE.PointLight(0x2de2a6, 0.85, 12);
        tvGlow.position.set(0, 4.2, 1.5);
        tvGroup.add(tvGlow);

        scene.add(tvGroup);
        createGroundedRing(-17, 21.5, 4.5);

        checkpoints.push({
            name: "Smart TV &bull; Profil Sekolah",
            subtitle: "Tekan [E] atau Sentuh untuk memutar Video Profil",
            type: "cinema",
            x: -17,
            z: 21.5,
            radius: 5.2
        });

        // ==============================================================
        // 5. PAPAN MADING SANTRI & LOCKERS
        // ==============================================================
        const noticeGroup = new THREE.Group();
        noticeGroup.position.set(-23.4, 0, 10);

        const nFrame = new THREE.Mesh(new THREE.BoxGeometry(0.2, 5.2, 9.2), new THREE.MeshLambertMaterial({ color: 0x4a2e18 }));
        nFrame.position.set(0, 4.2, 0);
        noticeGroup.add(nFrame);

        const nFace = new THREE.Mesh(new THREE.PlaneGeometry(9.0, 5.0), new THREE.MeshLambertMaterial({ map: createNoticeBoardTexture() }));
        nFace.rotation.y = Math.PI / 2;
        nFace.position.set(0.12, 4.2, 0);
        noticeGroup.add(nFace);

        scene.add(noticeGroup);
        createGroundedRing(-17, 10, 4.2);

        checkpoints.push({
            name: "Papan Pengumuman &amp; Mading Sekolah",
            subtitle: "Tekan [E] untuk membaca Agenda Santri",
            type: "notice",
            x: -17,
            z: 10,
            radius: 5.0
        });

        // ==============================================================
        // 6. PERPUSTAKAAN DIGITAL BAYT AL-HIKMAH (3D REALISTIC SANCTUARY)
        // Built with 100% 3D models: multi-tier wooden bookshelves, reading table,
        // cozy chairs, and rehal with holy Quran! (Zero flat photo billboards!)
        // ==============================================================
        const libraryGroup = new THREE.Group();
        libraryGroup.position.set(-18, 0, -1);

        // 2 Big Sturdy Cedar Bookshelves along the west wall
        [-3.2, 3.2].forEach(sz => {
            const shelf = new THREE.Mesh(new THREE.BoxGeometry(1.2, 5.8, 4.8), new THREE.MeshLambertMaterial({ color: 0x5a381d }));
            shelf.position.set(-5.0, 2.9, sz);
            shelf.castShadow = true;
            libraryGroup.add(shelf);

            // Colorful 3D Book Spines on Shelves
            const bookColors = [0x15803d, 0xb45309, 0x1e3a8a, 0x991b1b, 0x047857, 0xf59e0b];
            for (let tier = 1; tier <= 4; tier++) {
                for (let b = 0; b < 5; b++) {
                    const bMat = new THREE.MeshLambertMaterial({ color: bookColors[(tier + b) % bookColors.length] });
                    const book = new THREE.Mesh(new THREE.BoxGeometry(0.85, 0.9, 0.7), bMat);
                    book.position.set(-4.95, 0.4 + tier * 1.15, sz - 1.8 + b * 0.9);
                    libraryGroup.add(book);
                }
            }
        });

        // Round Solid Teak Reading Table in center
        const rTable = new THREE.Mesh(new THREE.CylinderGeometry(1.8, 1.8, 0.14, 24), new THREE.MeshLambertMaterial({ color: 0x78350f }));
        rTable.position.set(0.5, 1.1, 0);
        libraryGroup.add(rTable);

        const rTableLeg = new THREE.Mesh(new THREE.CylinderGeometry(0.24, 0.4, 1.05, 16), new THREE.MeshLambertMaterial({ color: 0x451a03 }));
        rTableLeg.position.set(0.5, 0.55, 0);
        libraryGroup.add(rTableLeg);

        // Cozy Circular Study Chairs
        for (let i = 0; i < 3; i++) {
            const angle = (i / 3) * Math.PI * 2 + 0.5;
            const chair = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.48, 0.65, 16), new THREE.MeshLambertMaterial({ color: 0x1e293b }));
            chair.position.set(0.5 + Math.cos(angle) * 2.8, 0.35, Math.sin(angle) * 2.8);
            libraryGroup.add(chair);

            const cushion = new THREE.Mesh(new THREE.CylinderGeometry(0.46, 0.46, 0.12, 16), new THREE.MeshLambertMaterial({ color: 0x2de2a6 }));
            cushion.position.set(0.5 + Math.cos(angle) * 2.8, 0.72, Math.sin(angle) * 2.8);
            libraryGroup.add(cushion);
        }

        // Holy Quran on Rehal (Stand) on table
        const rehalBase = new THREE.Mesh(new THREE.BoxGeometry(0.65, 0.15, 0.5), goldMat);
        rehalBase.position.set(0.5, 1.25, 0);
        libraryGroup.add(rehalBase);

        const quranBook = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.08, 0.4), new THREE.MeshLambertMaterial({ color: 0x064e3b }));
        quranBook.position.set(0.5, 1.36, 0);
        quranBook.rotation.y = 0.2;
        libraryGroup.add(quranBook);

        // Warm Reading Spotlight
        const libLight = new THREE.PointLight(0xfef08a, 0.95, 12);
        libLight.position.set(0.5, 4.5, 0);
        libraryGroup.add(libLight);

        scene.add(libraryGroup);
        createGroundedRing(-18, -1, 4.5, 0xa855f7);

        checkpoints.push({
            name: "Perpustakaan Bayt Al-Hikmah",
            subtitle: "Tekan [E] atau Sentuh untuk Khazanah Literasi",
            type: "library",
            x: -18,
            z: -1,
            radius: 5.2
        });

        // ==============================================================
        // 7. RUANG KELAS PEMBELAJARAN (INTERACTIVE CLASSROOM)
        // ==============================================================
        const classGroup = new THREE.Group();
        classGroup.position.set(19, 0, -2);

        // Partition Wall facing the main corridor with wide open door entrance
        const partWallN = new THREE.Mesh(new THREE.BoxGeometry(0.3, 8.5, 5.0), wallMat);
        partWallN.position.set(-8, 4.25, -6.5);
        classGroup.add(partWallN);

        const partWallS = new THREE.Mesh(new THREE.BoxGeometry(0.3, 8.5, 5.0), wallMat);
        partWallS.position.set(-8, 4.25, 6.5);
        classGroup.add(partWallS);

        const doorHeader = new THREE.Mesh(new THREE.BoxGeometry(0.3, 2.5, 9.0), wallMat);
        doorHeader.position.set(-8, 7.25, 0);
        classGroup.add(doorHeader);

        // Plaque Sign over classroom door
        const classSign = new THREE.Mesh(new THREE.BoxGeometry(0.45, 0.9, 4.6), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        classSign.position.set(-8.15, 6.2, 0);
        classGroup.add(classSign);

        // Big Whiteboard on North Wall of Classroom
        const wBoard = new THREE.Mesh(new THREE.BoxGeometry(9.6, 5.0, 0.15), new THREE.MeshLambertMaterial({ color: 0x94a3b8 }));
        wBoard.position.set(0, 4.2, -9.0);
        classGroup.add(wBoard);

        const wFace = new THREE.Mesh(new THREE.PlaneGeometry(9.4, 4.8), new THREE.MeshLambertMaterial({ map: createWhiteboardTexture() }));
        wFace.position.set(0, 4.2, -8.9);
        classGroup.add(wFace);

        // Teacher Desk
        const tDesk = new THREE.Mesh(new THREE.BoxGeometry(3.2, 1.4, 1.6), new THREE.MeshLambertMaterial({ color: 0x7c4f2c }));
        tDesk.position.set(0, 0.7, -6.2);
        classGroup.add(tDesk);

        // Student Desks and Chairs (6 sets in 2 rows)
        const deskMat = new THREE.MeshLambertMaterial({ color: 0x9c7a52 });
        const chairMat = new THREE.MeshLambertMaterial({ color: 0x475569 });
        [-3.2, 3.2].forEach(dx => {
            [-2.5, 1.2, 4.8].forEach(dz => {
                const sDesk = new THREE.Mesh(new THREE.BoxGeometry(1.8, 1.2, 1.1), deskMat);
                sDesk.position.set(dx, 0.6, dz);
                classGroup.add(sDesk);

                const sBook = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.08, 0.65), goldMat);
                sBook.position.set(dx, 1.24, dz);
                classGroup.add(sBook);

                const sChair = new THREE.Mesh(new THREE.BoxGeometry(0.9, 0.8, 0.9), chairMat);
                sChair.position.set(dx, 0.4, dz + 0.95);
                classGroup.add(sChair);
            });
        });

        scene.add(classGroup);
        createGroundedRing(16, -2, 4.8);

        checkpoints.push({
            name: "Ruang Kelas &bull; Program &amp; Tahfiz",
            subtitle: "Tekan [E] atau Sentuh untuk melihat Kurikulum",
            type: "program",
            x: 16,
            z: -2,
            radius: 5.6
        });

        // ==============================================================
        // 8. POJOK ROBOTIKA & SAINS ISLAM (AL-KHAWARIZMI STEM LAB)
        // Built with 100% 3D models: futuristic tech workbench, spinning 3D
        // holographic Earth globe with glowing rings, Robo-Santri android figure!
        // (Zero flat photo billboards!)
        // ==============================================================
        const stemGroup = new THREE.Group();
        stemGroup.position.set(19, 0, -22);

        // Modern Tech Workbench Table
        const techTable = new THREE.Mesh(new THREE.BoxGeometry(7.2, 1.3, 3.2), new THREE.MeshLambertMaterial({ color: 0x1e293b }));
        techTable.position.set(0, 0.65, 0);
        techTable.castShadow = true;
        stemGroup.add(techTable);

        const techTableTrim = new THREE.Mesh(new THREE.BoxGeometry(7.3, 0.12, 3.3), new THREE.MeshBasicMaterial({ color: 0x38bdf8 }));
        techTableTrim.position.set(0, 1.32, 0);
        stemGroup.add(techTableTrim);

        // 3D Spinning Holographic Globe Model
        const globeGroup = new THREE.Group();
        globeGroup.position.set(0, 2.5, 0);

        const globeMesh = new THREE.Mesh(
            new THREE.SphereGeometry(1.0, 24, 24),
            new THREE.MeshStandardMaterial({
                color: 0x38bdf8,
                wireframe: true,
                emissive: 0x0284c7,
                emissiveIntensity: 0.7
            })
        );
        globeGroup.add(globeMesh);

        const orbit1 = new THREE.Mesh(new THREE.TorusGeometry(1.5, 0.035, 8, 32), goldMat);
        orbit1.rotation.x = Math.PI / 3;
        globeGroup.add(orbit1);

        const orbit2 = new THREE.Mesh(new THREE.TorusGeometry(1.7, 0.035, 8, 32), new THREE.MeshBasicMaterial({ color: 0x2de2a6 }));
        orbit2.rotation.y = Math.PI / 3;
        globeGroup.add(orbit2);

        const globeLight = new THREE.PointLight(0x38bdf8, 1.1, 8.0);
        globeGroup.add(globeLight);
        stemGroup.add(globeGroup);

        // Robo-Santri Android Figure standing beside bench
        const roboFigure = new THREE.Group();
        roboFigure.position.set(2.8, 0, 0.8);
        const rfBody = new THREE.Mesh(new THREE.CylinderGeometry(0.32, 0.35, 1.1, 16), new THREE.MeshLambertMaterial({ color: 0xf8fafc }));
        rfBody.position.y = 1.1;
        roboFigure.add(rfBody);

        const rfHead = new THREE.Mesh(new THREE.BoxGeometry(0.48, 0.44, 0.44), new THREE.MeshLambertMaterial({ color: 0x0f172a }));
        rfHead.position.y = 1.85;
        roboFigure.add(rfHead);

        const rfVisor = new THREE.Mesh(new THREE.BoxGeometry(0.40, 0.14, 0.06), new THREE.MeshBasicMaterial({ color: 0x38bdf8 }));
        rfVisor.position.set(0, 1.86, 0.23);
        roboFigure.add(rfVisor);
        stemGroup.add(roboFigure);

        scene.add(stemGroup);
        createGroundedRing(16, -22, 4.5, 0x38bdf8);

        checkpoints.push({
            name: "Pojok Robotika &amp; Sains Al-Khawarizmi",
            subtitle: "Tekan [E] atau Sentuh untuk Riset Sains & Kuis",
            type: "robotics",
            x: 16,
            z: -22,
            radius: 5.2
        });

        // ==============================================================
        // 9. ETALASE LEMARI PIALA PRESTASI (TROPHY GALLERY)
        // ==============================================================
        const trophyGroup = new THREE.Group();
        trophyGroup.position.set(-18, 0, -22);

        // Showcase Cabinet
        const tCabinet = new THREE.Mesh(new THREE.BoxGeometry(1.2, 5.8, 7.8), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        tCabinet.position.set(-4.5, 2.9, 0);
        trophyGroup.add(tCabinet);

        // Red Carpet Runner leading to showcase
        const runner = new THREE.Mesh(new THREE.PlaneGeometry(5.0, 7.8), new THREE.MeshLambertMaterial({ color: 0x991b1b }));
        runner.rotation.x = -Math.PI / 2;
        runner.position.set(-1.8, 0.03, 0);
        runner.receiveShadow = true;
        trophyGroup.add(runner);

        // Golden Trophies inside showcase
        [-2.4, -0.8, 0.8, 2.4].forEach(tz => {
            const tBase = new THREE.Mesh(new THREE.CylinderGeometry(0.35, 0.45, 0.5, 16), new THREE.MeshLambertMaterial({ color: 0x1f2937 }));
            tBase.position.set(-4.3, 2.0, tz);
            trophyGroup.add(tBase);

            const cup = new THREE.Mesh(new THREE.CylinderGeometry(0.45, 0.2, 1.1, 16), goldMat);
            cup.position.set(-4.3, 2.8, tz);
            trophyGroup.add(cup);
        });

        scene.add(trophyGroup);
        createGroundedRing(-16, -22, 4.5, 0xf4c444);

        checkpoints.push({
            name: "Etalase Prestasi &amp; Lemari Piala",
            subtitle: "Tekan [E] atau Sentuh untuk melihat Etalase Juara",
            type: "trophy",
            x: -16,
            z: -22,
            radius: 5.2
        });

        // ==============================================================
        // 10. MUSALA AKBAR AL-IBRAHIM (NORTH SANCTUARY)
        // ==============================================================
        const musalaGroup = new THREE.Group();
        musalaGroup.position.set(0, 0, -25);

        // Grand Moorish Arch Entrance
        [-7.5, 7.5].forEach(mx => {
            const mArchCol = new THREE.Mesh(new THREE.CylinderGeometry(0.75, 0.85, 8.5, 16), pillarMat);
            mArchCol.position.set(mx, 4.25, 7.0);
            musalaGroup.add(mArchCol);
        });

        const mArchHeader = new THREE.Mesh(new THREE.BoxGeometry(17, 1.8, 1.4), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        mArchHeader.position.set(0, 7.8, 7.0);
        musalaGroup.add(mArchHeader);

        // Velvet Prayer Carpet (Emerald Green with Arched Motifs)
        const carpet = new THREE.Mesh(new THREE.PlaneGeometry(16, 13), new THREE.MeshLambertMaterial({ 
            map: carpetTexture,
            roughness: 0.85
        }));
        carpet.rotation.x = -Math.PI / 2;
        carpet.position.set(0, 0.03, 0);
        carpet.receiveShadow = true;
        musalaGroup.add(carpet);

        // Mihrab Facade Wall
        const mihrab = new THREE.Mesh(new THREE.PlaneGeometry(14, 8.2), new THREE.MeshLambertMaterial({ map: mosqueTexture }));
        mihrab.position.set(0, 4.1, -6.4);
        musalaGroup.add(mihrab);

        const mCrescent = new THREE.Mesh(new THREE.TorusGeometry(0.8, 0.16, 10, 20, Math.PI * 1.5), goldMat);
        mCrescent.position.set(0, 7.5, -6.3);
        musalaGroup.add(mCrescent);

        scene.add(musalaGroup);
        createGroundedRing(0, -18, 5.2, 0x14b8a6);

        checkpoints.push({
            name: "Musala Al-Ibrahim &bull; Visi &amp; Misi",
            subtitle: "Tekan [E] atau Sentuh untuk membuka Visi & Misi",
            type: "mosque",
            x: 0,
            z: -18,
            radius: 5.6
        });

        // NPC Ustadz Salman near Musala
        const npcUstadz = new THREE.Group();
        npcUstadz.position.set(-4, 0, -18);
        const nuBody = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.52, 1.7, 18), new THREE.MeshLambertMaterial({ color: 0xf8fafc }));
        nuBody.position.y = 1.7;
        npcUstadz.add(nuBody);
        const nuHead = new THREE.Mesh(new THREE.SphereGeometry(0.52, 18, 18), new THREE.MeshLambertMaterial({ color: 0xfce1cc }));
        nuHead.position.y = 2.9;
        npcUstadz.add(nuHead);
        const nuPeci = new THREE.Mesh(new THREE.CylinderGeometry(0.52, 0.54, 0.36, 18), new THREE.MeshLambertMaterial({ color: 0x111111 }));
        nuPeci.position.y = 3.32;
        npcUstadz.add(nuPeci);
        scene.add(npcUstadz);

        // ==============================================================
        // 11. 15 COLLECTIBLE GLOWING STARS (PERMATA ILMU TERSEMBUNYI)
        // ==============================================================
        const collectibleStars = [];
        const starLocations = [
            { x: 0, z: 8, name: "Permata Balairung Utama" },
            { x: 8, z: 22, name: "Bintang Meja Resepsionis" },
            { x: -8, z: 22, name: "Bintang Papan Admisi" },
            { x: -17, z: 21, name: "Permata Smart TV" },
            { x: -17, z: 10, name: "Bintang Mading Sekolah" },
            { x: 16, z: -2, name: "Permata Ruang Kelas" },
            { x: 22, z: 2, name: "Bintang Meja Belajar Santri" },
            { x: 16, z: -22, name: "Permata Robotika Al-Khawarizmi" },
            { x: 22, z: -25, name: "Bintang Sains & Astronomi" },
            { x: -18, z: -1, name: "Permata Perpustakaan Hikmah" },
            { x: -22, z: 3, name: "Bintang Khazanah Literasi" },
            { x: -16, z: -22, name: "Permata Etalase Juara" },
            { x: -20, z: -25, name: "Bintang Medali Emas" },
            { x: 0, z: -18, name: "Permata Gerbang Musala" },
            { x: 0, z: -28, name: "Bintang Mihrab Al-Ibrahim" }
        ];

        const starOctaGeo = new THREE.OctahedronGeometry(0.5, 0);
        const starMat = new THREE.MeshStandardMaterial({
            color: 0xf4c444,
            emissive: 0xf59e0b,
            emissiveIntensity: 0.8,
            metalness: 0.8,
            roughness: 0.2
        });

        starLocations.forEach((loc, index) => {
            const sGroup = new THREE.Group();
            sGroup.position.set(loc.x, 1.4, loc.z);

            const starMesh = new THREE.Mesh(starOctaGeo, starMat);
            sGroup.add(starMesh);

            const sLight = new THREE.PointLight(0xf4c444, 0.5, 3.5);
            sGroup.add(sLight);

            scene.add(sGroup);
            collectibleStars.push({
                group: sGroup,
                mesh: starMesh,
                x: loc.x,
                z: loc.z,
                collected: false,
                name: loc.name,
                id: index
            });
        });

        // ==============================================================
        // 12. 3D THIRD-PERSON PLAYER CHARACTER (ANIMATED STUDENT)
        // ==============================================================
        const player = {
            group: new THREE.Group(),
            x: 0,
            z: 22, // Spawn in Front Entrance Lobby
            rotation: 0,
            targetRotation: 0,
            speed: 0,
            moveAngle: 0,
            walkCycle: 0,
            currentLimb: { leg: 0, arm: 0, bounce: 0 },
            isJumping: false,
            jumpVelocity: 0,
            y: 0,
            
            limbs: {
                head: null,
                body: null,
                vestMat: null,
                leftArm: null,
                rightArm: null,
                leftLeg: null,
                rightLeg: null,
                peciGroup: null,
                peciMat: null,
                hijabGroup: null,
                hijabMat: null
            }
        };

        function buildPlayer() {
            while (player.group.children.length > 0) {
                player.group.remove(player.group.children[0]);
            }

            const vestColor = 0x144d40;
            const skinColor = 0xfce1cc;
            const darkPantsColor = 0x1a2723;

            const skinMat = new THREE.MeshLambertMaterial({ color: skinColor });
            const vestMat = new THREE.MeshLambertMaterial({ color: vestColor });
            const shirtMat = new THREE.MeshLambertMaterial({ color: 0xffffff });
            const pantsMat = new THREE.MeshLambertMaterial({ color: darkPantsColor });
            const shoeMat = new THREE.MeshLambertMaterial({ color: 0x1f2421 });
            const packMat = new THREE.MeshLambertMaterial({ color: 0xc49b4b });
            const peciMat = new THREE.MeshLambertMaterial({ color: 0x111111 });
            const hijabMat = new THREE.MeshLambertMaterial({ color: 0xfdfdfd });

            // Torso
            const torsoGroup = new THREE.Group();
            const body = new THREE.Mesh(new THREE.CylinderGeometry(0.55, 0.48, 1.05, 20), vestMat);
            body.position.y = 1.88;
            body.castShadow = true;
            torsoGroup.add(body);

            const belt = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.49, 0.18, 20), pantsMat);
            belt.position.y = 1.34;
            torsoGroup.add(belt);

            const buckle = new THREE.Mesh(new THREE.BoxGeometry(0.18, 0.12, 0.08), goldMat);
            buckle.position.set(0, 1.34, 0.48);
            torsoGroup.add(buckle);

            // Shirt Insert
            const shirtV = new THREE.Mesh(new THREE.CylinderGeometry(0.26, 0.18, 0.72, 16), shirtMat);
            shirtV.position.set(0, 2.05, 0.32);
            shirtV.rotation.x = 0.18;
            torsoGroup.add(shirtV);

            // Gold School Crest
            const badge = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.08, 0.03, 12), goldMat);
            badge.rotation.x = Math.PI / 2;
            badge.position.set(-0.28, 2.08, 0.48);
            torsoGroup.add(badge);

            // Backpack
            const pack = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.95, 0.45), packMat);
            pack.position.set(0, 1.88, -0.45);
            pack.castShadow = true;
            torsoGroup.add(pack);

            player.group.add(torsoGroup);
            player.limbs.body = body;
            player.limbs.vestMat = vestMat;

            // Head Group
            const headGroup = new THREE.Group();
            headGroup.position.set(0, 2.82, 0);

            const head = new THREE.Mesh(new THREE.SphereGeometry(0.48, 20, 20), skinMat);
            head.position.y = 0;
            head.castShadow = true;
            headGroup.add(head);

            [-0.15, 0.15].forEach(ex => {
                const eye = new THREE.Mesh(new THREE.SphereGeometry(0.07, 10, 10), new THREE.MeshBasicMaterial({ color: 0x0f172a }));
                eye.scale.set(0.8, 1.2, 0.3);
                eye.position.set(ex, 0.02, 0.46);
                headGroup.add(eye);
            });

            // Peci Group
            const peciGroup = new THREE.Group();
            const peci = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.50, 0.32, 20), peciMat);
            peci.position.set(0, 0.38, 0);
            peciGroup.add(peci);
            headGroup.add(peciGroup);

            // Hijab Group
            const hijabGroup = new THREE.Group();
            const hijabHood = new THREE.Mesh(new THREE.SphereGeometry(0.53, 20, 20), hijabMat);
            hijabHood.position.set(0, 0.04, -0.06);
            hijabGroup.add(hijabHood);
            const hijabCape = new THREE.Mesh(new THREE.CylinderGeometry(0.42, 0.65, 0.65, 18), hijabMat);
            hijabCape.position.set(0, -0.32, 0);
            hijabGroup.add(hijabCape);
            hijabGroup.visible = false;
            headGroup.add(hijabGroup);

            player.group.add(headGroup);
            player.limbs.head = headGroup;
            player.limbs.peciGroup = peciGroup;
            player.limbs.peciMat = peciMat;
            player.limbs.hijabGroup = hijabGroup;
            player.limbs.hijabMat = hijabMat;

            // Arms
            function makeArm(isLeft) {
                const armGroup = new THREE.Group();
                armGroup.position.set(isLeft ? -0.62 : 0.62, 2.3, 0);

                const sleeve = new THREE.Mesh(new THREE.CylinderGeometry(0.16, 0.15, 0.8, 12), vestMat);
                sleeve.position.y = -0.4;
                armGroup.add(sleeve);

                const hand = new THREE.Mesh(new THREE.SphereGeometry(0.12, 10, 10), skinMat);
                hand.position.y = -0.85;
                armGroup.add(hand);

                return armGroup;
            }

            player.limbs.leftArm = makeArm(true);
            player.limbs.rightArm = makeArm(false);
            player.group.add(player.limbs.leftArm);
            player.group.add(player.limbs.rightArm);

            // Legs
            function makeLeg(isLeft) {
                const legGroup = new THREE.Group();
                legGroup.position.set(isLeft ? -0.25 : 0.25, 1.25, 0);

                const leg = new THREE.Mesh(new THREE.CylinderGeometry(0.18, 0.16, 1.1, 14), pantsMat);
                leg.position.y = -0.55;
                legGroup.add(leg);

                const shoe = new THREE.Mesh(new THREE.BoxGeometry(0.32, 0.18, 0.52), shoeMat);
                shoe.position.set(0, -1.15, 0.08);
                legGroup.add(shoe);

                return legGroup;
            }

            player.limbs.leftLeg = makeLeg(true);
            player.limbs.rightLeg = makeLeg(false);
            player.group.add(player.limbs.leftLeg);
            player.group.add(player.limbs.rightLeg);

            scene.add(player.group);
        }

        buildPlayer();

        // ==============================================================
        // 12B. 3D PET COMPANION CAT (SI OYEN / MUEZZA)
        // ==============================================================
        const cat = {
            group: new THREE.Group(),
            x: 1.5,
            y: 0,
            z: 23.5,
            rotation: 0,
            speed: 0,
            walkCycle: 0,
            isHappy: 0,
            limbs: {
                root: null,
                body: null,
                head: null,
                tail: null,
                tailMid: null,
                tailTip: null,
                legFL: null,
                legFR: null,
                legBL: null,
                legBR: null,
                bell: null
            }
        };

        function buildCat() {
            while (cat.group.children.length > 0) {
                cat.group.remove(cat.group.children[0]);
            }

            const catFurMat = new THREE.MeshLambertMaterial({ color: 0xeb7a2b }); // Warm orange tabby
            const catChestMat = new THREE.MeshLambertMaterial({ color: 0xfef3c7 }); // Soft cream fur
            const catPawMat = new THREE.MeshLambertMaterial({ color: 0xffffff }); // White paws
            const catNoseMat = new THREE.MeshLambertMaterial({ color: 0xf472b6 }); // Pink nose & ears
            const catCollarMat = new THREE.MeshLambertMaterial({ color: 0x2de2a6 }); // Mint ribbon collar
            const catEyeMat = new THREE.MeshBasicMaterial({ color: 0x10b981 }); // Emerald eyes
            const catPupilMat = new THREE.MeshBasicMaterial({ color: 0x0a101d }); // Pupil

            const catRoot = new THREE.Group();
            cat.group.add(catRoot);
            cat.limbs.root = catRoot;

            // Torso (elongated rounded cylinder)
            const bodyMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.24, 0.22, 0.68, 14), catFurMat);
            bodyMesh.rotation.x = Math.PI / 2;
            bodyMesh.position.set(0, 0.42, 0);
            bodyMesh.castShadow = true;
            catRoot.add(bodyMesh);

            // Cream underbelly
            const bellyMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.18, 0.17, 0.52, 12), catChestMat);
            bellyMesh.rotation.x = Math.PI / 2;
            bellyMesh.position.set(0, 0.36, 0.02);
            catRoot.add(bellyMesh);

            // Fluffy chest patch
            const chestTuft = new THREE.Mesh(new THREE.SphereGeometry(0.16, 12, 10), catChestMat);
            chestTuft.position.set(0, 0.46, 0.28);
            catRoot.add(chestTuft);

            // Head Group
            const headGroup = new THREE.Group();
            headGroup.position.set(0, 0.60, 0.38);

            const headMesh = new THREE.Mesh(new THREE.SphereGeometry(0.25, 16, 14), catFurMat);
            headMesh.scale.set(1.15, 1.0, 1.0);
            headMesh.castShadow = true;
            headGroup.add(headMesh);

            // Cute Muzzle/Cheeks
            const snoutMesh = new THREE.Mesh(new THREE.SphereGeometry(0.11, 10, 8), catChestMat);
            snoutMesh.position.set(0, -0.06, 0.20);
            snoutMesh.scale.set(1.4, 0.8, 0.9);
            headGroup.add(snoutMesh);

            // Tiny Pink Nose
            const noseMesh = new THREE.Mesh(new THREE.ConeGeometry(0.038, 0.045, 4), catNoseMat);
            noseMesh.rotation.x = -Math.PI / 2;
            noseMesh.position.set(0, -0.03, 0.28);
            headGroup.add(noseMesh);

            // Expressive Eyes with pupil and shine
            [-0.095, 0.095].forEach(ex => {
                const eyeGroup = new THREE.Group();
                eyeGroup.position.set(ex, 0.06, 0.20);
                const eyeSphere = new THREE.Mesh(new THREE.SphereGeometry(0.062, 12, 10), catEyeMat);
                eyeSphere.scale.set(0.9, 1.1, 0.4);
                eyeGroup.add(eyeSphere);

                const pupil = new THREE.Mesh(new THREE.SphereGeometry(0.036, 8, 8), catPupilMat);
                pupil.scale.set(0.45, 1.0, 0.3);
                pupil.position.set(0, 0, 0.038);
                eyeGroup.add(pupil);

                const glint = new THREE.Mesh(new THREE.SphereGeometry(0.016, 6, 6), new THREE.MeshBasicMaterial({ color: 0xffffff }));
                glint.position.set(0.02, 0.025, 0.05);
                eyeGroup.add(glint);

                headGroup.add(eyeGroup);
            });

            // Pointed Ears with Pink Accents
            [-0.14, 0.14].forEach((ex, idx) => {
                const earGroup = new THREE.Group();
                earGroup.position.set(ex, 0.23, -0.02);
                earGroup.rotation.z = idx === 0 ? 0.32 : -0.32;
                earGroup.rotation.x = -0.12;

                const earCone = new THREE.Mesh(new THREE.ConeGeometry(0.095, 0.18, 4), catFurMat);
                earGroup.add(earCone);

                const earInner = new THREE.Mesh(new THREE.ConeGeometry(0.062, 0.14, 4), catNoseMat);
                earInner.position.set(0, -0.01, 0.02);
                earGroup.add(earInner);

                headGroup.add(earGroup);
            });

            // Mint Collar & Golden Bell
            const collar = new THREE.Mesh(new THREE.TorusGeometry(0.18, 0.032, 8, 16), catCollarMat);
            collar.position.set(0, -0.16, -0.06);
            collar.rotation.x = Math.PI / 2.3;
            headGroup.add(collar);

            const bell = new THREE.Mesh(new THREE.SphereGeometry(0.045, 10, 10), goldMat);
            bell.position.set(0, -0.22, 0.12);
            headGroup.add(bell);

            catRoot.add(headGroup);
            cat.limbs.head = headGroup;
            cat.limbs.bell = bell;

            // 4 Legs with White Paws
            function makeCatLeg(x, z) {
                const legPivot = new THREE.Group();
                legPivot.position.set(x, 0.35, z);

                const legBone = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.044, 0.30, 8), catFurMat);
                legBone.position.y = -0.14;
                legBone.castShadow = true;
                legPivot.add(legBone);

                const paw = new THREE.Mesh(new THREE.BoxGeometry(0.10, 0.07, 0.13), catPawMat);
                paw.position.set(0, -0.28, 0.02);
                legPivot.add(paw);

                catRoot.add(legPivot);
                return legPivot;
            }

            cat.limbs.legFL = makeCatLeg(-0.13, 0.20);
            cat.limbs.legFR = makeCatLeg(0.13, 0.20);
            cat.limbs.legBL = makeCatLeg(-0.13, -0.20);
            cat.limbs.legBR = makeCatLeg(0.13, -0.20);

            // Chained Tail
            const tailBase = new THREE.Group();
            tailBase.position.set(0, 0.42, -0.32);
            tailBase.rotation.x = 0.55;

            const t1 = new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.046, 0.22, 8), catFurMat);
            t1.position.y = 0.11;
            tailBase.add(t1);

            const tailMid = new THREE.Group();
            tailMid.position.set(0, 0.22, 0);
            tailMid.rotation.x = 0.35;

            const t2 = new THREE.Mesh(new THREE.CylinderGeometry(0.034, 0.04, 0.20, 8), catFurMat);
            t2.position.y = 0.10;
            tailMid.add(t2);

            const tailTip = new THREE.Group();
            tailTip.position.set(0, 0.20, 0);
            tailTip.rotation.x = 0.35;

            const t3 = new THREE.Mesh(new THREE.CylinderGeometry(0.024, 0.034, 0.16, 8), catPawMat);
            t3.position.y = 0.08;
            tailTip.add(t3);

            tailMid.add(tailTip);
            tailBase.add(tailMid);
            catRoot.add(tailBase);

            cat.limbs.tail = tailBase;
            cat.limbs.tailMid = tailMid;
            cat.limbs.tailTip = tailTip;

            // Ground Soft Shadow
            const catShadow = new THREE.Mesh(new THREE.CylinderGeometry(0.34, 0.34, 0.01, 16), new THREE.MeshBasicMaterial({
                color: 0x000000,
                transparent: true,
                opacity: 0.28
            }));
            catShadow.position.y = 0.01;
            cat.group.add(catShadow);

            cat.group.position.set(cat.x, 0, cat.z);
            scene.add(cat.group);
        }

        buildCat();

        function interactWithCat() {
            initAudio();
            playCatMeow();
            if (typeof cat !== 'undefined' && cat.group) {
                cat.isHappy = 1.2;
            }
            const hearts = ['🐾', '❤️', '✨', '🐾', '😻'];
            hearts.forEach((icon, i) => {
                setTimeout(() => {
                    const el = document.createElement('div');
                    el.className = 'cat-heart-particle';
                    el.textContent = icon;
                    const randomOffset = (Math.random() - 0.5) * 120;
                    el.style.left = (window.innerWidth / 2 + randomOffset) + 'px';
                    el.style.top = (window.innerHeight / 2 + 50) + 'px';
                    document.body.appendChild(el);
                    setTimeout(() => el.remove(), 1500);
                }, i * 110);
            });
            showCampusToast('🐱 <em>"Meooong~"</em> &bull; <strong>Si Oyen (Muezza)</strong> mendengkur manja mengikutimu! 🐾');
        }

        // ==============================================================
        // 13. CAMERA, CONTROLS & PHYSICS
        // ==============================================================
        let cameraAzimuth = 0;
        let cameraPitch = 0.32;
        let cameraZoom = 11.5;

        let targetAzimuth = 0;
        let targetPitch = 0.32;
        let targetZoom = 11.5;

        const cameraTarget = new THREE.Vector3(player.x, player.y + 1.85, player.z);

        const keys = { w: false, a: false, s: false, d: false, shift: false, space: false };

        window.addEventListener('keydown', (e) => {
            initAudio();
            const k = e.key.toLowerCase();
            if (k === 'w' || k === 'arrowup') keys.w = true;
            if (k === 's' || k === 'arrowdown') keys.s = true;
            if (k === 'a' || k === 'arrowleft') keys.a = true;
            if (k === 'd' || k === 'arrowright') keys.d = true;
            if (e.key === 'Shift') keys.shift = true;
            if (e.key === ' ') {
                e.preventDefault();
                triggerJump();
            }
            if (k === 'e') triggerActiveCheckpoint();
            if (k === 'm') openModal('map');
            if (k === 'p') interactWithCat();
        });

        window.addEventListener('keyup', (e) => {
            const k = e.key.toLowerCase();
            if (k === 'w' || k === 'arrowup') keys.w = false;
            if (k === 's' || k === 'arrowdown') keys.s = false;
            if (k === 'a' || k === 'arrowleft') keys.a = false;
            if (k === 'd' || k === 'arrowright') keys.d = false;
            if (e.key === 'Shift') keys.shift = false;
        });

        let isMouseDown = false;
        let prevMouseX = 0;
        let prevMouseY = 0;
        let mouseDownPos = { x: 0, y: 0 };
        let mouseDownTime = 0;

        const catRaycaster = new THREE.Raycaster();
        const catMouseVec = new THREE.Vector2();

        function checkClickCat(e) {
            if (typeof cat === 'undefined' || !cat.group) return;
            catMouseVec.x = (e.clientX / window.innerWidth) * 2 - 1;
            catMouseVec.y = -(e.clientY / window.innerHeight) * 2 + 1;
            catRaycaster.setFromCamera(catMouseVec, camera);
            const intersects = catRaycaster.intersectObjects(cat.group.children, true);
            if (intersects.length > 0) {
                interactWithCat();
            }
        }

        window.addEventListener('mousedown', (e) => {
            if (e.target.closest('button') || e.target.closest('#campus-modal') || e.target.closest('#radar-container')) return;
            isMouseDown = true;
            prevMouseX = e.clientX;
            prevMouseY = e.clientY;
            mouseDownPos.x = e.clientX;
            mouseDownPos.y = e.clientY;
            mouseDownTime = Date.now();
        });

        window.addEventListener('mouseup', (e) => {
            if (isMouseDown) {
                const dist = Math.hypot(e.clientX - mouseDownPos.x, e.clientY - mouseDownPos.y);
                const timeDiff = Date.now() - mouseDownTime;
                if (dist < 6 && timeDiff < 350) {
                    checkClickCat(e);
                }
            }
            isMouseDown = false;
        });
        window.addEventListener('mousemove', (e) => {
            if (!isMouseDown) return;
            const dx = e.clientX - prevMouseX;
            const dy = e.clientY - prevMouseY;
            targetAzimuth -= dx * 0.0055;
            targetPitch += dy * 0.0035;
            targetPitch = Math.max(-0.10, Math.min(1.20, targetPitch));
            prevMouseX = e.clientX;
            prevMouseY = e.clientY;
        });

        window.addEventListener('wheel', (e) => {
            targetZoom += e.deltaY * 0.008;
            targetZoom = Math.max(5.0, Math.min(22.0, targetZoom));
        }, { passive: true });

        // Touch Control Bindings
        function bindTouch(id, keyName) {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('touchstart', (e) => {
                e.preventDefault();
                initAudio();
                keys[keyName] = true;
            }, { passive: false });
            el.addEventListener('touchend', (e) => {
                e.preventDefault();
                keys[keyName] = false;
            }, { passive: false });
        }

        bindTouch('btn-up', 'w');
        bindTouch('btn-down', 's');
        bindTouch('btn-left', 'a');
        bindTouch('btn-right', 'd');

        function toggleSprintMobile() {
            isSprintActive = !isSprintActive;
            keys.shift = isSprintActive;
            const btn = document.getElementById('btn-touch-sprint');
            if (btn) {
                if (isSprintActive) btn.classList.add('active');
                else btn.classList.remove('active');
            }
            playTone(isSprintActive ? 600 : 350, 'sine', 0.1, 0.08);
        }

        function triggerJump() {
            if (!player.isJumping) {
                player.isJumping = true;
                player.jumpVelocity = 9.8;
                playJumpSound();
            }
        }

        function triggerEmoteCelebration() {
            triggerConfetti();
            playFanfare();
            showCampusToast("🎉 <strong>Allahu Akbar!</strong> Semangat Menuntut Ilmu!");
        }

        function toggleCameraView() {
            cameraDistanceMode = (cameraDistanceMode + 1) % 3;
            const label = document.getElementById('camera-mode-text');
            if (cameraDistanceMode === 0) {
                targetZoom = 11.5; targetPitch = 0.32;
                label.textContent = 'TPV Normal';
            }
            if (cameraDistanceMode === 1) {
                targetZoom = 6.5; targetPitch = 0.18;
                label.textContent = 'TPV Dekat';
            }
            if (cameraDistanceMode === 2) {
                targetZoom = 20.0; targetPitch = 0.90;
                label.textContent = 'Bird-Eye';
            }
            playTone(480, 'sine', 0.1, 0.08);
        }

        function teleportPlayer(tx, tz) {
            player.x = tx;
            player.z = tz;
            player.speed = 0;
            cameraTarget.set(tx, player.y + 1.85, tz);
            if (typeof cat !== 'undefined' && cat.group) {
                cat.x = tx + 1.4;
                cat.z = tz + 1.4;
                cat.speed = 0;
                cat.rotation = player.rotation;
                cat.group.position.set(cat.x, 0, cat.z);
            }
            triggerConfetti();
            playFanfare();
            showCampusToast(`🚀 Berpindah ke kawasan baru!`);
        }

        // ==============================================================
        // 14. RADAR MINIMAP RENDERING
        // ==============================================================
        const radarCanvas = document.getElementById('radar-canvas');
        const rCtx = radarCanvas.getContext('2d');

        function drawRadar() {
            const w = radarCanvas.width;
            const h = radarCanvas.height;
            const cx = w / 2;
            const cy = h / 2;
            const scale = 1.35;

            rCtx.clearRect(0, 0, w, h);

            // Radar concentric rings
            rCtx.strokeStyle = 'rgba(45, 226, 166, 0.25)';
            rCtx.lineWidth = 1;
            rCtx.beginPath();
            rCtx.arc(cx, cy, 28, 0, Math.PI * 2);
            rCtx.arc(cx, cy, 54, 0, Math.PI * 2);
            rCtx.stroke();

            // Checkpoints on radar
            checkpoints.forEach(cp => {
                const relX = (cp.x - player.x) * scale;
                const relZ = (cp.z - player.z) * scale;
                const dist = Math.hypot(relX, relZ);
                if (dist < 64) {
                    rCtx.fillStyle = '#f4c444';
                    rCtx.beginPath();
                    rCtx.arc(cx + relX, cy + relZ, 3.5, 0, Math.PI * 2);
                    rCtx.fill();
                }
            });

            // Collectible stars on radar
            collectibleStars.forEach(st => {
                if (!st.collected) {
                    const relX = (st.x - player.x) * scale;
                    const relZ = (st.z - player.z) * scale;
                    const dist = Math.hypot(relX, relZ);
                    if (dist < 64) {
                        rCtx.fillStyle = '#2de2a6';
                        rCtx.beginPath();
                        rCtx.arc(cx + relX, cy + relZ, 2.5, 0, Math.PI * 2);
                        rCtx.fill();
                    }
                }
            });

            // Cat companion on radar (orange dot with white border)
            if (typeof cat !== 'undefined' && cat.group) {
                const catRelX = (cat.x - player.x) * scale;
                const catRelZ = (cat.z - player.z) * scale;
                const catDist = Math.hypot(catRelX, catRelZ);
                if (catDist < 64) {
                    rCtx.fillStyle = '#ea580c';
                    rCtx.beginPath();
                    rCtx.arc(cx + catRelX, cy + catRelZ, 3.2, 0, Math.PI * 2);
                    rCtx.fill();
                    rCtx.strokeStyle = '#ffffff';
                    rCtx.lineWidth = 1.2;
                    rCtx.stroke();
                }
            }

            // Center Player Pointer (Arrow)
            rCtx.save();
            rCtx.translate(cx, cy);
            rCtx.rotate(-player.rotation + Math.PI);
            rCtx.fillStyle = '#ffffff';
            rCtx.beginPath();
            rCtx.moveTo(0, -7);
            rCtx.lineTo(5, 5);
            rCtx.lineTo(0, 3);
            rCtx.lineTo(-5, 5);
            rCtx.closePath();
            rCtx.fill();
            rCtx.restore();
        }

        // ==============================================================
        // 15. ANIMATION LOOP & THIRD-PERSON FOLLOW
        // ==============================================================
        let clock = new THREE.Clock();
        let stepSoundTimer = 0;

        function animate() {
            requestAnimationFrame(animate);
            const rawDelta = clock.getDelta();
            const delta = Math.min(rawDelta, 0.06);
            const time = clock.getElapsedTime();

            // 1. Movement Physics
            let inputX = 0;
            let inputZ = 0;

            if (keys.w) inputZ -= 1;
            if (keys.s) inputZ += 1;
            if (keys.a) inputX -= 1;
            if (keys.d) inputX += 1;

            const isInputActive = (inputX !== 0 || inputZ !== 0);

            if (isInputActive) {
                player.moveAngle = Math.atan2(inputX, inputZ) + cameraAzimuth;
                player.targetRotation = player.moveAngle;
            }

            const isSprinting = keys.shift;
            const topSpeed = isSprinting ? 20.0 : 13.5;
            const targetSpeed = isInputActive ? topSpeed : 0;
            const accelRate = isInputActive ? 12.0 : 14.0;
            player.speed += (targetSpeed - player.speed) * Math.min(1, accelRate * delta);
            if (player.speed < 0.005) player.speed = 0;

            // Apply displacement with solid building wall boundaries
            if (player.speed > 0) {
                player.x += Math.sin(player.moveAngle) * player.speed * delta;
                player.z += Math.cos(player.moveAngle) * player.speed * delta;

                // Hall boundaries (Cleanly bounded inside the school hall)
                player.x = Math.max(-21.5, Math.min(25.5, player.x));
                player.z = Math.max(-32.0, Math.min(31.0, player.z));
            }

            // Smooth rotation towards travel vector
            let diffRot = player.targetRotation - player.rotation;
            while (diffRot < -Math.PI) diffRot += Math.PI * 2;
            while (diffRot > Math.PI) diffRot -= Math.PI * 2;
            player.rotation += diffRot * Math.min(1, 14 * delta);

            // Jump Physics
            if (player.isJumping) {
                player.y += player.jumpVelocity * delta;
                player.jumpVelocity -= 26.0 * delta; // Gravity
                if (player.y <= 0) {
                    player.y = 0;
                    player.isJumping = false;
                    player.jumpVelocity = 0;
                    playStepSound();
                }
            }

            // Limb Stride Animation
            const speedRatio = player.speed / 13.5;
            if (speedRatio > 0.01) {
                player.walkCycle += delta * (speedRatio * 11.5);
                stepSoundTimer += delta * (speedRatio * 3.6);
                if (stepSoundTimer > 1.0) {
                    playStepSound();
                    stepSoundTimer = 0;
                }
            } else {
                stepSoundTimer = 0.7;
                const idleBreathe = Math.sin(time * 2.8) * 0.018;
                player.currentLimb.bounce += (idleBreathe - player.currentLimb.bounce) * Math.min(1, 8 * delta);
            }

            const targetLegSwing = Math.sin(player.walkCycle) * 0.65 * speedRatio;
            const targetArmSwing = Math.cos(player.walkCycle) * 0.65 * speedRatio;
            const targetBounce = Math.abs(Math.sin(player.walkCycle * 2)) * 0.05 * speedRatio;

            const limbDamp = Math.min(1, 16 * delta);
            player.currentLimb.leg += (targetLegSwing - player.currentLimb.leg) * limbDamp;
            player.currentLimb.arm += (targetArmSwing - player.currentLimb.arm) * limbDamp;
            player.currentLimb.bounce += (targetBounce - player.currentLimb.bounce) * limbDamp;

            if (player.limbs.leftLeg && player.limbs.rightLeg) {
                player.limbs.leftLeg.rotation.x = player.currentLimb.leg;
                player.limbs.rightLeg.rotation.x = -player.currentLimb.leg;
                player.limbs.leftArm.rotation.x = -player.currentLimb.arm;
                player.limbs.rightArm.rotation.x = player.currentLimb.arm;
            }

            player.group.position.set(player.x, player.y + player.currentLimb.bounce, player.z);
            player.group.rotation.y = player.rotation;

            // Cat AI Follower Movement & Animation
            if (typeof cat !== 'undefined' && cat.group && cat.limbs.root) {
                // Compute desired follow slot: behind and slightly to the right of the player
                const followDist = 1.85;
                const sideDist = 0.85;
                const desiredX = player.x - Math.sin(player.rotation) * followDist + Math.cos(player.rotation) * sideDist;
                const desiredZ = player.z - Math.cos(player.rotation) * followDist - Math.sin(player.rotation) * sideDist;

                const dx = desiredX - cat.x;
                const dz = desiredZ - cat.z;
                const distToTarget = Math.hypot(dx, dz);
                const distToPlayer = Math.hypot(player.x - cat.x, player.z - cat.z);

                // Teleport catch-up if separated by more than 20 units (e.g. after fast-travel)
                if (distToPlayer > 20.0) {
                    cat.x = desiredX;
                    cat.z = desiredZ;
                    cat.speed = 0;
                    cat.rotation = player.rotation;
                } else if (distToTarget > 0.42) {
                    // Turn towards target point
                    const targetAngle = Math.atan2(dx, dz);
                    let diffAngle = targetAngle - cat.rotation;
                    while (diffAngle < -Math.PI) diffAngle += Math.PI * 2;
                    while (diffAngle > Math.PI) diffAngle -= Math.PI * 2;
                    cat.rotation += diffAngle * Math.min(1, 11 * delta);

                    // Speed dynamically catches up to player
                    const desiredCatSpeed = Math.min(23.0, Math.max(9.0, distToTarget * 7.5));
                    cat.speed += (desiredCatSpeed - cat.speed) * Math.min(1, 9.0 * delta);

                    cat.x += Math.sin(cat.rotation) * cat.speed * delta;
                    cat.z += Math.cos(cat.rotation) * cat.speed * delta;

                    // Bounds inside the school hall
                    cat.x = Math.max(-21.5, Math.min(25.5, cat.x));
                    cat.z = Math.max(-32.0, Math.min(31.0, cat.z));

                    // Stride gait animation (4-legged trot)
                    const strideRate = (cat.speed / 10.0) * 14.0;
                    cat.walkCycle += delta * strideRate;

                    const legAngle = Math.sin(cat.walkCycle) * 0.58;
                    if (cat.limbs.legFL && cat.limbs.legBR) {
                        cat.limbs.legFL.rotation.x = legAngle;
                        cat.limbs.legBR.rotation.x = legAngle;
                        cat.limbs.legFR.rotation.x = -legAngle;
                        cat.limbs.legBL.rotation.x = -legAngle;
                    }

                    // Trot vertical bob
                    const trotBob = Math.abs(Math.sin(cat.walkCycle * 2)) * 0.05;
                    cat.limbs.root.position.y = trotBob;

                    // Tail dynamic sway
                    if (cat.limbs.tail) {
                        cat.limbs.tail.rotation.y = Math.sin(cat.walkCycle) * 0.4;
                        cat.limbs.tail.rotation.x = 0.55 + Math.cos(cat.walkCycle * 2) * 0.12;
                    }
                } else {
                    // Arrived / Idle near player
                    cat.speed += (0 - cat.speed) * Math.min(1, 12 * delta);

                    // Slowly orient towards player to keep affectionate eye contact
                    const lookAngle = Math.atan2(player.x - cat.x, player.z - cat.z);
                    let diffAngle = lookAngle - cat.rotation;
                    while (diffAngle < -Math.PI) diffAngle += Math.PI * 2;
                    while (diffAngle > Math.PI) diffAngle -= Math.PI * 2;
                    cat.rotation += diffAngle * Math.min(1, 3.5 * delta);

                    // Smooth legs return to standing
                    const dampLimb = Math.min(1, 10 * delta);
                    if (cat.limbs.legFL) {
                        cat.limbs.legFL.rotation.x += (0 - cat.limbs.legFL.rotation.x) * dampLimb;
                        cat.limbs.legFR.rotation.x += (0 - cat.limbs.legFR.rotation.x) * dampLimb;
                        cat.limbs.legBL.rotation.x += (0 - cat.limbs.legBL.rotation.x) * dampLimb;
                        cat.limbs.legBR.rotation.x += (0 - cat.limbs.legBR.rotation.x) * dampLimb;
                    }

                    // Gentle breathing and tail swishing
                    const breatheBob = Math.sin(time * 2.8) * 0.015;
                    cat.limbs.root.position.y = breatheBob;
                    if (cat.limbs.tail) {
                        cat.limbs.tail.rotation.y = Math.sin(time * 2.2) * 0.3;
                        cat.limbs.tail.rotation.x = 0.55 + Math.sin(time * 1.5) * 0.08;
                    }
                    if (cat.limbs.head) {
                        cat.limbs.head.rotation.y = Math.sin(time * 1.1) * 0.12;
                    }
                }

                // Happy celebration / emote animation
                if (cat.isHappy > 0) {
                    cat.isHappy -= delta;
                    cat.limbs.root.position.y += Math.abs(Math.sin(cat.isHappy * 14)) * 0.30;
                    cat.limbs.root.rotation.y = Math.sin(cat.isHappy * 16) * 0.45;
                    if (cat.limbs.tail) {
                        cat.limbs.tail.rotation.y = Math.sin(time * 20) * 0.6;
                    }
                } else {
                    cat.limbs.root.rotation.y = 0;
                }

                // Player jump sync (playful hop)
                if (player.isJumping && distToPlayer < 4.0 && cat.isHappy <= 0) {
                    cat.limbs.root.position.y += Math.max(0, player.y * 0.35);
                }

                cat.group.position.set(cat.x, 0, cat.z);
                cat.group.rotation.y = cat.rotation;
            }

            // Camera Follow
            const angleDamp = Math.min(1, 18 * delta);
            cameraAzimuth += (targetAzimuth - cameraAzimuth) * angleDamp;
            cameraPitch += (targetPitch - cameraPitch) * angleDamp;
            cameraZoom += (targetZoom - cameraZoom) * Math.min(1, 12 * delta);

            const targetDamp = Math.min(1, 12 * delta);
            cameraTarget.x += (player.x - cameraTarget.x) * targetDamp;
            cameraTarget.y += (player.y + 1.85 - cameraTarget.y) * targetDamp;
            cameraTarget.z += (player.z - cameraTarget.z) * targetDamp;

            const horizDist = cameraZoom * Math.cos(cameraPitch);
            const vertDist = cameraZoom * Math.sin(cameraPitch);

            const desiredCamX = cameraTarget.x + Math.sin(cameraAzimuth) * horizDist;
            const desiredCamY = cameraTarget.y + vertDist;
            const desiredCamZ = cameraTarget.z + Math.cos(cameraAzimuth) * horizDist;

            const camEyeDamp = Math.min(1, 12 * delta);
            camera.position.x += (desiredCamX - camera.position.x) * camEyeDamp;
            camera.position.y += (desiredCamY - camera.position.y) * camEyeDamp;
            camera.position.z += (desiredCamZ - camera.position.z) * camEyeDamp;
            camera.lookAt(cameraTarget.x, cameraTarget.y, cameraTarget.z);

            // Ground Rings Pulse
            groundRings.forEach((ring, idx) => {
                ring.scale.setScalar(1 + Math.sin(time * 2.2 + idx) * 0.08);
            });

            // Dust Particles Sway
            if (typeof dustGeo !== 'undefined' && dustGeo.attributes.position) {
                const dPos = dustGeo.attributes.position;
                for (let i = 0; i < dustCount; i++) {
                    const v = dustVelocities[i];
                    let py = dPos.getY(i) + v.vy;
                    if (py > 8.7) py = 0.5;
                    dPos.setY(i, py);
                    let px = dPos.getX(i) + Math.sin(time * v.swaySpeed + v.phase) * v.swayRadius;
                    dPos.setX(i, px);
                }
                dPos.needsUpdate = true;
            }

            // Holographic Globe 3D Rotation
            if (typeof globeMesh !== 'undefined' && globeMesh) {
                globeMesh.rotation.y += delta * 0.8;
                orbit1.rotation.z += delta * 0.6;
                orbit2.rotation.x += delta * 0.5;
            }

            // Animate Collectible Stars & Check Pickup Collision
            collectibleStars.forEach(st => {
                if (!st.collected) {
                    st.group.rotation.y += delta * 2.0;
                    st.group.position.y = 1.35 + Math.sin(time * 3.0 + st.id) * 0.22;

                    const distToPlayer = Math.hypot(player.x - st.x, player.z - st.z);
                    if (distToPlayer < 2.0) {
                        collectStar(st);
                    }
                }
            });

            // NPCs Greeting Interaction
            if (typeof npcDesk !== 'undefined' && npcDesk) {
                const distD = Math.hypot(player.x - 8, player.z - 22.5);
                if (distD < 8.0) {
                    npcDesk.rotation.y = Math.atan2(player.x - 8, player.z - 18.2);
                    if (npcDesk.userData.rightArm) {
                        npcDesk.userData.rightArm.rotation.x = -1.2 + Math.sin(time * 5.5) * 0.3;
                    }
                } else {
                    npcDesk.rotation.y = 0;
                    if (npcDesk.userData.rightArm) {
                        npcDesk.userData.rightArm.rotation.x = -0.3 + Math.sin(time * 1.5) * 0.04;
                    }
                }
            }

            // Checkpoint Proximity
            let foundNearby = null;
            checkpoints.forEach(cp => {
                const dist = Math.hypot(player.x - cp.x, player.z - cp.z);
                if (dist < cp.radius) {
                    foundNearby = cp;
                }
            });

            const promptEl = document.getElementById('proximity-prompt');
            if (foundNearby) {
                activeCheckpointKey = foundNearby.type;
                document.getElementById('prompt-action-title').innerHTML = foundNearby.name;
                document.getElementById('prompt-action-subtitle').textContent = foundNearby.subtitle || 'Tekan [E] atau Sentuh untuk Masuk';
                promptEl.classList.add('show');
                recordCheckpointDiscovery(foundNearby);
            } else {
                activeCheckpointKey = null;
                promptEl.classList.remove('show');
            }

            drawRadar();
            renderer.render(scene, camera);
        }

        // ==============================================================
        // 16. COLLECTIBLES & PROGRESSION SYSTEM
        // ==============================================================
        const discoveredCheckpoints = new Set();
        let starCount = 0;

        function collectStar(st) {
            st.collected = true;
            st.group.visible = false;
            starCount++;
            updateExplorationHUD();
            playCollectChime();
            showCampusToast(`⭐ <strong>Permata Berkah Ditemukan!</strong> &bull; ${st.name} (+1)`);

            if (starCount === 5 || starCount === 10 || starCount === 15) {
                triggerConfetti();
                playFanfare();
                showCampusToast(`🏆 <strong>Pencapaian Hebat!</strong> &bull; Berhasil mengumpulkan ${starCount} Permata Bintang!`);
            }
        }

        function updateExplorationHUD() {
            const exploreText = document.getElementById('hud-explore-text');
            const starText = document.getElementById('hud-star-count');
            if (exploreText) {
                exploreText.textContent = `${discoveredCheckpoints.size}/${checkpoints.length} Kawasan`;
            }
            if (starText) {
                starText.textContent = `${starCount}/${collectibleStars.length}`;
            }

            // Update ID Card Stats
            const cStar = document.getElementById('card-star-display');
            const cZone = document.getElementById('card-zone-display');
            const cRank = document.getElementById('card-rank-title');
            if (cStar) cStar.textContent = `${starCount} Permata Bintang`;
            if (cZone) cZone.textContent = `${discoveredCheckpoints.size} / ${checkpoints.length} Kawasan`;
            if (cRank) {
                if (starCount >= 12) cRank.textContent = "Bintang Prestasi Utama 👑";
                else if (starCount >= 7) cRank.textContent = "Duta Teladan Santri 🌟";
                else if (starCount >= 3) cRank.textContent = "Penjelajah Kampus Hebat 🧭";
                else cRank.textContent = "Santri Pelopor 🐣";
            }
        }

        function recordCheckpointDiscovery(cp) {
            if (!discoveredCheckpoints.has(cp.type)) {
                discoveredCheckpoints.add(cp.type);
                updateExplorationHUD();
                playTone(784, 'triangle', 0.15, 0.12);
                setTimeout(() => playTone(1046.5, 'sine', 0.22, 0.14), 100);
                showCampusToast(`📍 <strong>Kawasan Terbuka!</strong> &bull; Menemukan ${cp.name}`);
            }
        }

        function showCampusToast(message) {
            const toast = document.getElementById('campus-toast');
            if (!toast) return;
            toast.innerHTML = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3400);
        }

        // ==============================================================
        // 17. CHECKPOINT MODAL CONTROLS
        // ==============================================================
        function triggerActiveCheckpoint() {
            if (activeCheckpointKey) {
                openModal(activeCheckpointKey);
            }
        }

        function openModal(type) {
            initAudio();
            playTone(520, 'sine', 0.15, 0.1);
            const overlay = document.getElementById('campus-modal');
            const dynamicBody = document.getElementById('modal-dynamic-body');
            const tpl = document.getElementById('tpl-' + type);

            if (tpl && dynamicBody) {
                dynamicBody.innerHTML = tpl.innerHTML;
                overlay.classList.add('open');
            }

            if (type === 'admission') {
                triggerConfetti();
                playFanfare();
            }
        }

        function closeModal() {
            playTone(400, 'sine', 0.1, 0.08);
            document.getElementById('campus-modal').classList.remove('open');
        }

        function closeModalOnBackdrop(e) {
            if (e.target.id === 'campus-modal') {
                closeModal();
            }
        }

        // Persona Customizer
        function setPlayerPersona(key, name, role, vestColor, hatType, avatarUrl) {
            playTone(659.25, 'sine', 0.15, 0.15);
            setTimeout(() => playTone(880, 'sine', 0.18, 0.12), 100);

            document.getElementById('char-name-hud').textContent = name.split(' ')[0] + ' (' + role.split(' ')[1] + ')';
            
            const avatarHud = document.getElementById('char-avatar-hud');
            if (avatarHud && avatarUrl) {
                avatarHud.src = avatarUrl;
            }
            const cardImg = document.getElementById('card-photo-img');
            if (cardImg && avatarUrl) {
                cardImg.src = avatarUrl;
            }

            const colorHex = (typeof vestColor === 'string') ? parseInt(vestColor.replace('#', '0x')) : vestColor;
            if (player.limbs.vestMat) {
                player.limbs.vestMat.color.set(colorHex);
            }

            if (hatType && hatType.startsWith('hijab')) {
                if (player.limbs.peciGroup) player.limbs.peciGroup.visible = false;
                if (player.limbs.hijabGroup) {
                    player.limbs.hijabGroup.visible = true;
                    if (hatType === 'hijab-teal') {
                        player.limbs.hijabMat.color.set(0x2de2a6);
                    } else {
                        player.limbs.hijabMat.color.set(0xfdfdfd);
                    }
                }
            } else {
                if (player.limbs.hijabGroup) player.limbs.hijabGroup.visible = false;
                if (player.limbs.peciGroup) {
                    player.limbs.peciGroup.visible = true;
                    if (hatType === 'peci-white') {
                        player.limbs.peciMat.color.set(0xf8fafc);
                    } else {
                        player.limbs.peciMat.color.set(0x111111);
                    }
                }
            }

            document.querySelectorAll('.persona-btn').forEach(b => b.classList.remove('selected'));
            if (event && event.currentTarget) event.currentTarget.classList.add('selected');
            showCampusToast(`Sahabat Aktif: <strong>${name}</strong>`);
        }

        function updateCardName(val) {
            const nameEl = document.getElementById('card-custom-name');
            if (nameEl) nameEl.value = val;
        }

        // ==============================================================
        // 18. INTERACTIVE QUIZ MINIGAME (KUIS CERDAS SANTRI JUARA)
        // ==============================================================
        const quizQuestions = [
            {
                q: "Siapakah ilmuwan muslim legendaris yang menciptakan konsep Aljabar dan angka nol dalam matematika?",
                options: ["A. Muhammad bin Musa Al-Khawarizmi", "B. Ibnu Sina (Avicenna)", "C. Jabir bin Hayyan", "D. Ibnu Rusyd (Averroes)"],
                correct: 0,
                explanation: "Tepat sekali! Al-Khawarizmi adalah matematikawan agung muslim asal Persia penemu Aljabar dan algoritma!"
            },
            {
                q: "Apa nama ilmuwan optik muslim yang menemukan prinsip kamera obskura dan menulis Kitab Al-Manadhir?",
                options: ["A. Al-Farabi", "B. Al-Kindi", "C. Al-Hasan Ibnu Al-Haitsam", "D. Al-Jazari"],
                correct: 2,
                explanation: "Hebat! Ibnu Al-Haitsam diakui dunia sebagai Bapak Optik Modern yang mendasari penemuan kamera modern."
            },
            {
                q: "Di manakah letak perpustakaan dan pusat ilmu pengetahuan terbesar pada era keemasan Islam?",
                options: ["A. Bayt Al-Hikmah di Baghdad", "B. Colosseum di Roma", "C. Menara Pisa", "D. Alexandria di Yunani"],
                correct: 0,
                explanation: "Benar! Bayt Al-Hikmah (House of Wisdom) di Baghdad mengumpulkan ilmuwan dari seluruh penjuru dunia."
            },
            {
                q: "Apakah tiga cabang olahraga yang secara istimewa dianjurkan dalam tuntunan sahabat Nabi untuk diajarkan kepada anak?",
                options: ["A. Futsal, Basket, Voli", "B. Memanah, Berenang, Menunggang Kuda", "C. Catur, Silat, Golf", "D. Angkat Besi, Lari, Yoga"],
                correct: 1,
                explanation: "Masya Allah benar! Memanah, berenang, dan menunggang kuda melatih ketajaman fokus, ketahanan fisik, dan keberanian."
            },
            {
                q: "Apakah adab makan utama yang diajarkan Rasulullah SAW ketika hendak menikmati makanan?",
                options: ["A. Berdiri sambil membaca doa", "B. Duduk rapi, membaca bismillah, dan makan dengan tangan kanan", "C. Makan tergesa-gesa agar cepat habis", "D. Berbicara dengan suara keras"],
                correct: 1,
                explanation: "Alhamdulillah tepat! Duduk tenang, berdoa memohon berkah, dan makan menggunakan tangan kanan secara sopan."
            }
        ];

        let currentQuizIndex = 0;
        let quizScore = 0;

        function answerQuiz(selectedIndex) {
            const q = quizQuestions[currentQuizIndex];
            const optButtons = document.querySelectorAll('#quiz-options-box .quiz-option-btn');
            optButtons.forEach(b => b.disabled = true);

            const feedback = document.getElementById('quiz-feedback-box');
            feedback.style.display = 'block';

            if (selectedIndex === q.correct) {
                optButtons[selectedIndex].classList.add('correct');
                quizScore += 20;
                feedback.style.background = 'rgba(34, 197, 94, 0.2)';
                feedback.style.border = '1px solid #22c55e';
                feedback.style.color = '#86efac';
                feedback.innerHTML = `✅ <strong>Jawaban Benar!</strong> ${q.explanation}`;
                playCollectChime();
            } else {
                optButtons[selectedIndex].classList.add('wrong');
                optButtons[q.correct].classList.add('correct');
                feedback.style.background = 'rgba(239, 68, 68, 0.2)';
                feedback.style.border = '1px solid #ef4444';
                feedback.style.color = '#fca5a5';
                feedback.innerHTML = `❌ <strong>Kurang Tepat!</strong> Jawaban yang benar adalah <strong>${q.options[q.correct]}</strong>.<br>${q.explanation}`;
                playTone(220, 'sine', 0.25, 0.1);
            }

            document.getElementById('quiz-score-badge').textContent = `Skor: ${quizScore}`;
            document.getElementById('quiz-next-btn').style.display = 'inline-flex';
        }

        function nextQuizQuestion() {
            currentQuizIndex++;
            if (currentQuizIndex < quizQuestions.length) {
                renderQuizQuestion();
            } else {
                const container = document.getElementById('quiz-container');
                container.innerHTML = `
                    <div style="text-align:center;padding:20px 0;">
                        <span style="font-size:54px;display:block;margin-bottom:10px;">🏆</span>
                        <h3 style="font-family:'Outfit',sans-serif;font-size:24px;color:#fff;margin-bottom:8px;">Kuis Selesai! Skor Akhir: ${quizScore}/100</h3>
                        <p style="color:var(--mint);font-size:14px;margin-bottom:20px;">
                            ${quizScore >= 80 ? 'Mumtaz! Kamu pantas menjadi Bintang Cendekiawan Muslim!' : 'Bagus sekali! Teruslah rajin membaca dan menuntut ilmu!'}
                        </p>
                        <button type="button" class="hud-btn-pill gold" onclick="resetQuiz()">
                            🔄 Ulangi Kuis
                        </button>
                    </div>
                `;
                document.getElementById('quiz-next-btn').style.display = 'none';
                triggerConfetti();
                playFanfare();
            }
        }

        function renderQuizQuestion() {
            const q = quizQuestions[currentQuizIndex];
            document.getElementById('quiz-question-number').textContent = `Pertanyaan ${currentQuizIndex + 1} dari ${quizQuestions.length}`;
            document.getElementById('quiz-question-text').textContent = q.q;

            const optBox = document.getElementById('quiz-options-box');
            optBox.innerHTML = '';
            q.options.forEach((opt, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'quiz-option-btn';
                btn.textContent = opt;
                btn.onclick = () => answerQuiz(idx);
                optBox.appendChild(btn);
            });

            document.getElementById('quiz-feedback-box').style.display = 'none';
            document.getElementById('quiz-next-btn').style.display = 'none';
        }

        function resetQuiz() {
            currentQuizIndex = 0;
            quizScore = 0;
            openModal('quiz');
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) document.exitFullscreen();
            }
        }

        // Confetti Fireworks
        function triggerConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const particles = [];
            const colors = ['#2de2a6', '#f4c444', '#ff7644', '#38bdf8', '#ffffff', '#a855f7'];
            for (let i = 0; i < 90; i++) {
                particles.push({
                    x: canvas.width / 2 + (Math.random() - 0.5) * 160,
                    y: canvas.height / 2 + (Math.random() - 0.5) * 100,
                    vx: (Math.random() - 0.5) * 14,
                    vy: (Math.random() - 1.2) * 16,
                    size: Math.random() * 8 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    rot: Math.random() * 360,
                    vRot: (Math.random() - 0.5) * 12,
                    alpha: 1
                });
            }

            function frame() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                let alive = 0;
                particles.forEach(p => {
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.38;
                    p.rot += p.vRot;
                    p.alpha -= 0.009;
                    if (p.alpha > 0) {
                        alive++;
                        ctx.save();
                        ctx.globalAlpha = p.alpha;
                        ctx.translate(p.x, p.y);
                        ctx.rotate((p.rot * Math.PI) / 180);
                        ctx.fillStyle = p.color;
                        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
                        ctx.restore();
                    }
                });
                if (alive > 0) requestAnimationFrame(frame);
                else ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
            frame();
        }

        // Window Resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // Initialize & Start
        window.addEventListener('load', () => {
            updateExplorationHUD();
            const fill = document.getElementById('loader-fill');
            fill.style.width = '100%';
            setTimeout(() => {
                const overlay = document.getElementById('loading-overlay');
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 500);
            }, 400);

            animate();
        });
    </script>
</body>
</html>

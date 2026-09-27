<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="Petualangan 3D Siswa Third-Person &amp; Buku Petualangan Cerita Interaktif Kampus Islami {{ $school->school_name }}">
    <title>Jelajah 3D Kampus Islami — Ekosistem Sekolah {{ $school->school_name }} | Buku Petualangan Cerita</title>
    
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
            width: 76px;
            height: 76px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--mint) 0%, var(--pine) 100%);
            display: grid;
            place-items: center;
            font-size: 40px;
            font-weight: 800;
            color: #061512;
            box-shadow: 0 0 35px var(--mint-glow);
            margin-bottom: 20px;
            animation: pulseCrest 1.8s infinite ease-in-out;
        }
        @keyframes pulseCrest { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        .loader-bar-wrap {
            width: 240px;
            height: 6px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 14px;
        }
        .loader-fill {
            width: 0%;
            height: 100%;
            background: var(--mint);
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
            padding: 16px 24px;
            background: linear-gradient(180deg, rgba(8, 26, 22, 0.88) 0%, rgba(8, 26, 22, 0) 100%);
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
            gap: 10px;
            pointer-events: auto;
            flex-wrap: wrap;
        }
        .hud-btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 999px;
            font-family: "Outfit", sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            background: rgba(10, 38, 32, 0.78);
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
            opacity: 0.85;
            transition: opacity 0.2s ease;
        }
        .touch-btn {
            background: rgba(14, 46, 38, 0.82);
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

        /* Right Touch Action Buttons */
        .touch-actions-group {
            position: absolute;
            bottom: 24px;
            right: 24px;
            z-index: 25;
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: flex-end;
        }
        .action-circle-btn {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: rgba(14, 46, 38, 0.85);
            border: 2px solid var(--mint);
            color: #fff;
            font-size: 24px;
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4), 0 0 15px var(--mint-glow);
            backdrop-filter: blur(8px);
            touch-action: manipulation;
        }
        .action-circle-btn:active {
            background: var(--mint);
            color: #061512;
            transform: scale(0.92);
        }

        /* Modal Overlay For School Content */
        .campus-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(6, 18, 15, 0.85);
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
            max-width: 780px;
            max-height: 86vh;
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

        /* Persona Selection Grid & Rich Character Cards */
        .persona-pick-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin: 18px 0;
        }
        .persona-btn {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(45, 226, 166, 0.25);
            color: #fff;
            font-family: "Outfit", sans-serif;
            cursor: pointer;
            text-align: left;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .persona-btn:hover {
            background: rgba(45, 226, 166, 0.15);
            border-color: var(--mint);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(45, 226, 166, 0.2);
        }
        .persona-btn.selected {
            background: linear-gradient(135deg, rgba(20, 77, 64, 0.95) 0%, rgba(10, 38, 32, 0.95) 100%);
            border: 2px solid var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244, 196, 68, 0.35);
        }
        .persona-btn.selected::after {
            content: '✓ Dipilih';
            position: absolute;
            top: 8px;
            right: 10px;
            font-size: 10px;
            font-weight: 800;
            background: var(--gold);
            color: #061512;
            padding: 2px 7px;
            border-radius: 999px;
            letter-spacing: 0.04em;
        }
        .persona-avatar-img {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid rgba(244, 196, 68, 0.6);
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            background: #0d2a23;
        }
        .persona-tag-pill {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--gold);
            background: rgba(244, 196, 68, 0.15);
            padding: 2px 8px;
            border-radius: 999px;
            margin-bottom: 4px;
        }

        /* Speaker Visual Novel Dialogue Header */
        .speaker-vn-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: linear-gradient(135deg, rgba(20, 77, 64, 0.55) 0%, rgba(10, 38, 32, 0.75) 100%);
            border: 1.5px solid var(--mint);
            border-radius: 20px;
            padding: 16px 20px;
            margin-bottom: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .speaker-vn-avatar {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            object-fit: cover;
            border: 2.5px solid var(--gold);
            box-shadow: 0 6px 20px rgba(0,0,0,0.5), 0 0 14px rgba(244, 196, 68, 0.4);
            flex-shrink: 0;
            background: #0d2a23;
        }
        .speaker-vn-info h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            margin: 0 0 4px 0;
            color: #fff;
        }
        .speaker-vn-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--mint);
            background: rgba(45, 226, 166, 0.18);
            border: 1px solid rgba(45, 226, 166, 0.35);
            padding: 2px 10px;
            border-radius: 999px;
        }

        /* Confetti */
        #confetti-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 99999;
        }

        @media(max-width: 768px) {
            .hud-top-bar { padding: 12px 16px; }
            .persona-pick-grid { grid-template-columns: 1fr; }
            .campus-modal-box { padding: 24px 20px; }
            #proximity-prompt { width: min(calc(100% - 32px), 380px); font-size: 13px; padding: 10px 18px; bottom: 85px; }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-overlay">
        <div class="loader-crest">إ</div>
        <h2 style="font-family:'Outfit',sans-serif;font-size:22px;letter-spacing:-0.01em;margin-bottom:4px;">
            Memuat Ekosistem 3D Kampus Islami
        </h2>
        <p style="color:var(--mint);font-size:13px;">{{ $school->school_name }} &bull; Tekstur &amp; Arsitektur Nyata</p>
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
                <small><span class="live-dot"></span> Ekosistem 3D &bull; Arsitektur Nyata</small>
            </div>
        </a>

        <div class="hud-actions">
            <div class="hud-btn-pill gold" id="bintang-badge" title="Bintang Prestasi Santri">
                <span>⭐</span> <span id="hud-star-count">0</span> <span>Bintang</span>
            </div>
            <div class="hud-btn-pill mint" id="hud-explore-badge" title="Status Penjelajahan Kampus">
                <span>🗺️</span> <span id="hud-explore-text">0/8 Dijelajahi</span>
            </div>
            <button type="button" class="hud-btn-pill" onclick="openModal('persona')" title="Ganti karakter santri">
                <img id="char-avatar-hud" src="{{ asset('images/campus/fatih_avatar.jpg') }}" alt="Avatar" class="hud-avatar-thumb">
                <span id="char-name-hud">Fatih (OSIS)</span>
            </button>
            <button type="button" class="hud-btn-pill" onclick="toggleCameraView()" title="Ganti sudut kamera">
                <span>📷</span> <span id="camera-mode-text">Kamera: TPV Normal</span>
            </button>
            <button type="button" class="hud-btn-pill" onclick="toggleAudio()" title="Suara langkah kaki dan lonceng">
                <span id="audio-icon-box">🔊</span> <span id="audio-text-box">Suara: On</span>
            </button>
            <button type="button" class="hud-btn-pill" onclick="toggleFullScreen()" title="Mode Layar Penuh">
                <span>⛶</span> Fullscreen
            </button>
            <a href="{{ route('landing') }}" class="hud-btn-pill">
                <span>🏛️</span> Mode Web Biasa
            </a>
        </div>
    </header>

    <!-- Grounded Proximity Action Prompt -->
    <div id="proximity-prompt" onclick="triggerActiveCheckpoint()">
        <div class="key-badge">E</div>
        <div>
            <strong style="display:block;font-size:13px;" id="prompt-action-title">Masuk ke Gedung</strong>
            <small style="color:var(--mint);font-size:11px;" id="prompt-action-subtitle">Tekan [E] atau Sentuh di sini</small>
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

    <!-- Right Touch Action Buttons -->
    <div class="touch-actions-group">
        <button type="button" class="action-circle-btn" id="btn-touch-interact" onclick="triggerActiveCheckpoint()" title="Interaksi">
            💬
        </button>
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
                    <p style="color:var(--mint);font-size:12px;margin:0;">Karakter 3D Santri &bull; Bintang Pelajar</p>
                </div>
            </div>
            <p style="color:rgba(255,255,255,0.8);font-size:13.5px;line-height:1.6;">
                Pilih persona karakter yang akan mendampingi penjelajahanmu di ekosistem kampus Islami {{ $school->school_name }}:
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
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;padding:10px 22px;">
                    Gunakan Karakter Ini &rarr;
                </button>
            </div>
        </div>

        <!-- 2. Mosque (Visi & Misi) -->
        <div id="tpl-mosque">
            <div class="speaker-vn-card">
                <img src="{{ asset('images/campus/ustadz_avatar.jpg') }}" alt="Ustadz Pembina" class="speaker-vn-avatar">
                <div class="speaker-vn-info">
                    <span class="speaker-vn-badge">🕌 Dewan Pembina Tahfiz &bull; Ruhiyah Santri</span>
                    <h3>Ustadz Salman Al-Farisi, Lc.</h3>
                    <p style="margin:0;color:rgba(255,255,255,0.8);font-size:12.5px;">Pembina Karakter Islami &bull; Masjid Kampus {{ $school->school_name }}</p>
                </div>
            </div>
            <div style="background:linear-gradient(135deg, rgba(20,77,64,0.45) 0%, rgba(10,38,32,0.45) 100%);border:1.5px solid var(--mint);border-radius:18px;padding:22px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--gold);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">VISI PENDIDIKAN QURANI:</span>
                <h3 style="font-family:'Newsreader',serif;font-size:23px;color:#fff;font-style:italic;margin-top:6px;line-height:1.4;">
                    &ldquo;{{ $page->vision ?: 'Generasi qurani berkarakter mulia.' }}&rdquo;
                </h3>
            </div>
            <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.15);border-radius:18px;padding:20px;">
                <span style="font-size:11px;color:var(--mint);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;display:block;margin-bottom:10px;">MISI STRATEGIS:</span>
                <p style="font-size:13.5px;color:rgba(255,255,255,0.85);line-height:1.75;margin:0;white-space:pre-line;">{{ $page->mission }}</p>
            </div>
        </div>

        <!-- 3. Cinema Screen (Video Profil) -->
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

        <!-- 4. Trophy Podium (Prestasi & Champions) -->
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
                        <p style="font-size:12.5px;color:rgba(255,255,255,0.75);margin:0;">Catatan medali olimpiade dan musabaqah tahfidz terbaru santri.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 5. Academic Hall (Programs & Tahfiz) -->
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

        <!-- 6. Admission Gate -->
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

        <!-- 7. Monument (Medali Balairung & Filosofi Keislaman) -->
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
        <div id="tpl-fountain" style="display:none;"></div>

        <!-- 8. Gazebo (Saung Tahfiz & Literasi) -->
        <div id="tpl-gazebo">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🛖</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Saung Tahfiz &amp; Literasi Qur'an</h2>
                    <p style="color:var(--mint);font-size:12px;margin:0;">Halaqah Santri &bull; Muroja'ah Terbuka</p>
                </div>
            </div>
            <div style="background:linear-gradient(135deg, rgba(244,196,68,0.15) 0%, rgba(20,77,64,0.3) 100%);border:1.5px solid var(--gold);border-radius:18px;padding:20px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--gold);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">RUANG HALAQAH OUTDOOR:</span>
                <p style="font-size:13.5px;color:#fff;line-height:1.7;margin-top:6px;">
                    Saung kayu asri di bawah naungan pohon rindang menjadi tempat favorit santri menyetorkan hafalan Al-Qur'an (talaqqi), muroja'ah bersama musyrif, dan membaca buku literasi sains.
                </p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;margin-bottom:16px;">
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--gold);font-size:13px;display:block;margin-bottom:4px;">📖 Talaqqi Bersanad</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Bimbingan langsung dari asatidz hafizh Qur'an dengan makharijul huruf dan tajwid presisi.</p>
                </div>
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--mint);font-size:13px;display:block;margin-bottom:4px;">🤝 Belajar Berkelompok</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Kultur santri saling menyimak (tasmi') bacaan teman sebaya melatih ukhuwah dan kesabaran.</p>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;">
                    Tutup &rarr;
                </button>
            </div>
        </div>

        <!-- 9. Sports Corner (Arena Olahraga & Ekstrakurikuler) -->
        <div id="tpl-sports">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <span style="font-size:32px;">🏀</span>
                <div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:24px;margin:0;">Arena Olahraga &amp; Ekstrakurikuler</h2>
                    <p style="color:var(--mint);font-size:12px;margin:0;">Kebugaran Jasmani &bull; Sportivitas Santri</p>
                </div>
            </div>
            <div style="background:linear-gradient(135deg, rgba(255,118,68,0.15) 0%, rgba(20,77,64,0.3) 100%);border:1.5px solid var(--orange);border-radius:18px;padding:20px;margin-bottom:16px;">
                <span style="font-size:11px;color:var(--orange);font-weight:800;letter-spacing:0.08em;text-transform:uppercase;">FISIK KUAT &amp; SEHAT:</span>
                <p style="font-size:13.5px;color:#fff;line-height:1.7;margin-top:6px;">
                    &ldquo;Mukmin yang kuat lebih dicintai Allah daripada mukmin yang lemah.&rdquo; Lapangan olahraga serbaguna menunjang kebugaran fisik, kekompakan tim, dan kesehatan mental santri.
                </p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:12px;margin-bottom:16px;">
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--orange);font-size:13px;display:block;margin-bottom:4px;">⚽ Futsal &amp; Basket</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Kompetisi liga antar kelas mingguan menumbuhkan jiwa kepemimpinan dan pantang menyerah.</p>
                </div>
                <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:var(--mint);font-size:13px;display:block;margin-bottom:4px;">🏹 Sunnah Sports &amp; Bela Diri</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Ekskul memanah (archery), pencak silat tapak suci, serta lari sehat rutin santri.</p>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;">
                    Tutup &rarr;
                </button>
            </div>
        </div>

        <!-- 10. Notice Board (Papan Pengumuman & Mading Santri) -->
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
                    <strong style="color:#fff;font-size:13.5px;display:block;margin-bottom:4px;">Festival Literasi &amp; Seni Islami</strong>
                    <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:0;">Lomba pidato 3 bahasa, kaligrafi kontemporer, dan pameran karya ilmiah santri.</p>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:var(--mint);color:#061512;">
                    Tutup &rarr;
                </button>
            </div>
        </div>

        <!-- 11. Reception Desk (Meja Resepsionis & Profil Sekolah) -->
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
                    🎓 Lihat Paket Biaya &amp; Admisi
                </button>
                <button type="button" class="hud-btn-pill" onclick="closeModal()" style="background:rgba(255,255,255,0.1);font-size:13.5px;padding:10px 20px;">
                    Tutup
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
         THREE.JS 3D ENGINE WITH TEXTURED ARCHITECTURE
         ============================================================== -->
    <script>
        let audioSoundOn = true;
        let cameraDistanceMode = 0; // 0: Normal TPV, 1: Close TPV, 2: Bird-Eye
        let activeCheckpointKey = null;
        let audioCtx = null;

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

        function playFanfare() {
            const freqs = [523.25, 659.25, 783.99, 1046.5];
            freqs.forEach((f, i) => {
                setTimeout(() => playTone(f, 'triangle', 0.3, 0.18), i * 110);
            });
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

        // Three.js Scene Setup (Indoor Modern Islamic School Hall & Atrium)
        const container = document.getElementById('game-canvas-container');
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0xd9ebe6);
        scene.fog = new THREE.FogExp2(0xd9ebe6, 0.009);

        const camera = new THREE.PerspectiveCamera(52, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: 'high-performance' });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.18;
        container.appendChild(renderer.domElement);

        // Indoor Lighting Setup
        const ambientLight = new THREE.AmbientLight(0xfff7ee, 1.15);
        scene.add(ambientLight);

        // Skylight & Sun Stream
        const sunLight = new THREE.DirectionalLight(0xfffaec, 1.45);
        sunLight.position.set(25, 45, 20);
        sunLight.castShadow = true;
        sunLight.shadow.mapSize.width = 2048;
        sunLight.shadow.mapSize.height = 2048;
        sunLight.shadow.camera.near = 5;
        sunLight.shadow.camera.far = 120;
        const d = 42;
        sunLight.shadow.camera.left = -d;
        sunLight.shadow.camera.right = d;
        sunLight.shadow.camera.top = d;
        sunLight.shadow.camera.bottom = -d;
        scene.add(sunLight);

        // Soft Accent Spotlights inside school
        const lobbySpot = new THREE.PointLight(0xffe6b3, 1.2, 28);
        lobbySpot.position.set(0, 7.5, 20);
        scene.add(lobbySpot);

        const musalaSpot = new THREE.PointLight(0x99ffdd, 1.0, 24);
        musalaSpot.position.set(0, 7.0, -22);
        scene.add(musalaSpot);

        // ==============================================================
        // TEXTURE LOADER & MATERIALS
        // ==============================================================
        const textureLoader = new THREE.TextureLoader();

        // 1. Ceramic Floor Tile Texture throughout the entire school
        const tileTexture = textureLoader.load('{{ asset("images/campus/courtyard_tiles.jpg") }}');
        tileTexture.wrapS = THREE.RepeatWrapping;
        tileTexture.wrapT = THREE.RepeatWrapping;
        tileTexture.repeat.set(16, 18);

        // 2. Architectural Textures
        const mosqueTexture = textureLoader.load('{{ asset("images/campus/mosque_facade.jpg") }}');
        const schoolTexture = textureLoader.load('{{ asset("images/campus/school_facade.jpg") }}');

        // 3. High-Fidelity AI Generated Architectural Assets
        const carpetTexture = textureLoader.load('{{ asset("images/campus/prayer_carpet.jpg") }}');
        carpetTexture.wrapS = THREE.RepeatWrapping;
        carpetTexture.wrapT = THREE.RepeatWrapping;
        carpetTexture.repeat.set(4, 3);

        const tvTexture = textureLoader.load('{{ asset("images/campus/smart_tv_screen.jpg") }}');

        const lobbyWallTexture = textureLoader.load('{{ asset("images/campus/school_lobby_wall.jpg") }}');

        const hallFloorEmblemTexture = textureLoader.load('{{ asset("images/campus/hall_floor_emblem.png") }}');

        // ==============================================================
        // DYNAMIC CANVAS TEXTURES (Tuition Board, Mading, Whiteboard)
        // ==============================================================
        const tuitionData = @json($packagesJson ?? []);

        function createTuitionBoardTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1400;
            const ctx = canvas.getContext('2d');

            // Elegant deep pine gradient
            const grad = ctx.createLinearGradient(0, 0, 0, 1400);
            grad.addColorStop(0, '#0a2620');
            grad.addColorStop(0.5, '#144d40');
            grad.addColorStop(1, '#061a15');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, 1024, 1400);

            // Gold ornamental double border
            ctx.strokeStyle = '#f4c444';
            ctx.lineWidth = 14;
            ctx.strokeRect(24, 24, 976, 1352);
            ctx.lineWidth = 4;
            ctx.strokeRect(42, 42, 940, 1316);

            // Header Tag
            ctx.fillStyle = '#2de2a6';
            ctx.font = 'bold 34px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('★ PENERIMAAN SANTRI BARU (PPDB) ★', 512, 115);

            // Main Title
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 50px "Outfit", sans-serif';
            ctx.fillText('RINCIAN PAKET BIAYA PENDIDIKAN', 512, 190);

            ctx.fillStyle = '#f4c444';
            ctx.font = 'italic 32px "Newsreader", serif';
            ctx.fillText('{{ $school->school_name }} — Investasi Masa Depan Qurani', 512, 245);

            ctx.strokeStyle = 'rgba(45, 226, 166, 0.6)';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(100, 275);
            ctx.lineTo(924, 275);
            ctx.stroke();

            let curY = 325;
            if (tuitionData && tuitionData.length > 0) {
                tuitionData.slice(0, 3).forEach((pkg, idx) => {
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.08)';
                    ctx.beginPath();
                    ctx.roundRect(75, curY, 874, 220, 20);
                    ctx.fill();

                    ctx.strokeStyle = idx === 0 ? '#f4c444' : '#2de2a6';
                    ctx.lineWidth = 3;
                    ctx.stroke();

                    ctx.textAlign = 'left';
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 42px "Outfit", sans-serif';
                    ctx.fillText(pkg.name, 115, curY + 65);

                    ctx.fillStyle = '#2de2a6';
                    ctx.font = '26px "Plus Jakarta Sans", sans-serif';
                    ctx.fillText('(' + (pkg.billing_period || 'Periode Biaya') + ')', 115, curY + 112);

                    ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                    ctx.font = '24px "Plus Jakarta Sans", sans-serif';
                    const desc = (pkg.description || 'Fasilitas pembelajaran terpadu, tahfiz & asrama').substring(0, 48);
                    ctx.fillText(desc, 115, curY + 165);

                    ctx.textAlign = 'right';
                    ctx.fillStyle = '#f4c444';
                    ctx.font = 'bold 48px "Outfit", sans-serif';
                    ctx.fillText(pkg.price, 910, curY + 110);

                    curY += 255;
                });
            } else {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.08)';
                ctx.beginPath();
                ctx.roundRect(75, curY, 874, 220, 20);
                ctx.fill();
                ctx.strokeStyle = '#f4c444';
                ctx.lineWidth = 3;
                ctx.stroke();

                ctx.textAlign = 'left';
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 42px "Outfit", sans-serif';
                ctx.fillText('Paket Pendidikan Reguler', 115, curY + 65);
                ctx.fillStyle = '#2de2a6';
                ctx.font = '26px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('(Per Bulan)', 115, curY + 112);
                ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                ctx.font = '24px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('Pembelajaran sains, hafalan tahfiz mutqin & adab', 115, curY + 165);

                ctx.textAlign = 'right';
                ctx.fillStyle = '#f4c444';
                ctx.font = 'bold 48px "Outfit", sans-serif';
                ctx.fillText('Rp 1.500.000', 910, curY + 110);
            }

            ctx.textAlign = 'center';
            ctx.fillStyle = '#2de2a6';
            ctx.font = 'bold 34px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('👉 Tekan [E] atau Sentuh untuk Formulir & Konsultasi', 512, 1260);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        function createNoticeBoardTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 1200;
            canvas.height = 760;
            const ctx = canvas.getContext('2d');

            // Corkboard base
            ctx.fillStyle = '#c19665';
            ctx.fillRect(0, 0, 1200, 760);
            for (let i = 0; i < 4000; i++) {
                ctx.fillStyle = Math.random() > 0.5 ? 'rgba(75, 45, 18, 0.12)' : 'rgba(255, 240, 200, 0.12)';
                ctx.fillRect(Math.random() * 1200, Math.random() * 760, 3, 3);
            }

            // Dark Walnut Frame
            ctx.strokeStyle = '#4a2e18';
            ctx.lineWidth = 28;
            ctx.strokeRect(14, 14, 1172, 732);

            // Mading Header Banner
            ctx.fillStyle = '#144d40';
            ctx.fillRect(60, 45, 1080, 85);
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 40px "Outfit", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('📌 PAPAN PENGUMUMAN & MADING SANTRI', 600, 102);

            // Sticky Note 1: Jadwal Tahfiz (Yellow)
            ctx.save();
            ctx.translate(120, 180);
            ctx.rotate(-0.02);
            ctx.fillStyle = '#fff785';
            ctx.fillRect(0, 0, 280, 270);
            ctx.fillStyle = '#ea4335';
            ctx.beginPath(); ctx.arc(140, 16, 9, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 22px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText('JADWAL TAHFIZ', 22, 55);
            ctx.font = '16px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('• 05.30: Murojaah Pagi', 22, 95);
            ctx.fillText('• 16.00: Talaqqi Mutqin', 22, 135);
            ctx.fillText('• 20.00: Tasmi Mandiri', 22, 175);
            ctx.fillStyle = '#144d40';
            ctx.font = 'bold 15px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('Bintang Pelajar Qurani!', 22, 230);
            ctx.restore();

            // Paper 2: Agenda Sekolah (White Document)
            ctx.save();
            ctx.translate(450, 170);
            ctx.rotate(0.015);
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 330, 380);
            ctx.fillStyle = '#4285f4';
            ctx.beginPath(); ctx.arc(165, 16, 9, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#144d40';
            ctx.font = 'bold 22px "Outfit", sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText('KALENDER AKADEMIK', 22, 55);
            ctx.fillStyle = '#334155';
            ctx.font = '16px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('1. Ujian Hifzhil Quran 5-10 Juz', 22, 100);
            ctx.fillText('2. Edukasi Sains & Outing Class', 22, 140);
            ctx.fillText('3. Lomba Pidato Bahasa Arab', 22, 180);
            ctx.fillText('4. Penerimaan Santri Baru PPDB', 22, 220);
            ctx.fillStyle = '#f4c444';
            ctx.fillRect(22, 270, 286, 50);
            ctx.fillStyle = '#061512';
            ctx.font = 'bold 16px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('★ Bintang Prestasi Membanggakan', 30, 302);
            ctx.restore();

            // Sticky Note 3: Prestasi Terkini (Cyan)
            ctx.save();
            ctx.translate(820, 190);
            ctx.rotate(-0.025);
            ctx.fillStyle = '#a7ffeb';
            ctx.fillRect(0, 0, 260, 260);
            ctx.fillStyle = '#fbbc04';
            ctx.beginPath(); ctx.arc(130, 16, 9, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#004d40';
            ctx.font = 'bold 20px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText('🏆 PRESTASI BARU', 20, 55);
            ctx.font = '15px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('Medali Emas Olimpiade', 20, 95);
            ctx.fillText('Sains Tingkat Kota &', 20, 125);
            ctx.fillText('Juara 1 Musabaqah', 20, 155);
            ctx.fillText('Tahfidz Al-Quran 2026', 20, 185);
            ctx.restore();

            // Bottom CTA
            ctx.fillStyle = 'rgba(20, 77, 64, 0.88)';
            ctx.fillRect(60, 640, 1080, 55);
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 22px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Tekan [E] atau Sentuh untuk Melihat Berita & Informasi Lengkap', 600, 676);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        function createWhiteboardTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 1200;
            canvas.height = 700;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#f8fafc';
            ctx.fillRect(0, 0, 1200, 700);

            // Metal border
            ctx.strokeStyle = '#94a3b8';
            ctx.lineWidth = 24;
            ctx.strokeRect(12, 12, 1176, 676);

            // Bismillah
            ctx.fillStyle = '#0f766e';
            ctx.font = 'bold 44px "Outfit", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 600, 80);

            ctx.textAlign = 'left';
            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 36px "Outfit", sans-serif';
            ctx.fillText('Ruang Kelas Pembelajaran &bull; {{ $school->school_name }}', 70, 150);

            ctx.fillStyle = '#0d9488';
            ctx.font = '24px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('Materi: Adab Menuntut Ilmu, Kurikulum Sains & Tahfiz Bersanad', 70, 195);

            ctx.strokeStyle = '#cbd5e1';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(70, 220);
            ctx.lineTo(1130, 220);
            ctx.stroke();

            ctx.fillStyle = '#334155';
            ctx.font = '24px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('1. Tahfiz Cilik Gemilang & Mutqin 30 Juz', 80, 270);
            ctx.fillText('2. Kurikulum Terpadu Sains, Matematika & Bahasa Asing', 80, 320);
            ctx.fillText('3. Pembiasaan Karakter Santun & Adab Keseharian', 80, 370);
            ctx.fillText('4. Eksplorasi Eksperimen Laboratorium Digital', 80, 420);

            // Science diagram box
            ctx.strokeStyle = '#0284c7';
            ctx.lineWidth = 2;
            ctx.strokeRect(810, 260, 310, 210);
            ctx.fillStyle = '#0284c7';
            ctx.font = 'bold 20px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('Sains & Logika', 830, 295);
            ctx.fillStyle = '#475569';
            ctx.font = '18px "JetBrains Mono", monospace';
            ctx.fillText('E = mc²', 850, 335);
            ctx.fillText('a² + b² = c²', 850, 375);
            ctx.fillText('∫ f(x) dx', 850, 415);

            ctx.fillStyle = '#0f766e';
            ctx.font = 'italic 25px "Newsreader", serif';
            ctx.fillText('“Mendidik dengan Teladan, Generasi Qurani Berkarakter Mulia”', 90, 520);

            ctx.textAlign = 'center';
            ctx.fillStyle = '#64748b';
            ctx.font = 'bold 22px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('Tekan [E] atau Sentuh untuk Detail Program & Kurikulum', 600, 620);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        function createSpeechBubbleTexture(headerText, mainText, subText) {
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 256;
            const ctx = canvas.getContext('2d');

            ctx.clearRect(0, 0, 512, 256);

            // Soft drop shadow for floating dialogue bubble
            ctx.shadowColor = 'rgba(6, 40, 30, 0.28)';
            ctx.shadowBlur = 14;
            ctx.shadowOffsetY = 8;

            // Rounded Speech Bubble Path with Downward Pointer Tail
            const x = 24, y = 16, w = 464, h = 175, r = 26;
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.lineTo(x + w - r, y);
            ctx.quadraticCurveTo(x + w, y, x + w, y + r);
            ctx.lineTo(x + w, y + h - r);
            ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
            
            // Pointer tail pointing down towards receptionist's head
            ctx.lineTo(256 + 28, y + h);
            ctx.lineTo(256, y + h + 38);
            ctx.lineTo(256 - 28, y + h);
            
            ctx.lineTo(x + r, y + h);
            ctx.quadraticCurveTo(x, y + h, x, y + h - r);
            ctx.lineTo(x, y + r);
            ctx.quadraticCurveTo(x, y, x + r, y);
            ctx.closePath();

            // Background Fill (Clean white/ivory gradient)
            const grad = ctx.createLinearGradient(0, y, 0, y + h);
            grad.addColorStop(0, '#ffffff');
            grad.addColorStop(1, '#f0fdf8');
            ctx.fillStyle = grad;
            ctx.fill();

            // Border
            ctx.shadowColor = 'transparent';
            ctx.strokeStyle = '#144d40';
            ctx.lineWidth = 6;
            ctx.stroke();

            // Top Header Pill Badge
            ctx.fillStyle = '#144d40';
            ctx.beginPath();
            const hx = 45, hy = 32, hw = 190, hh = 28, hr = 14;
            ctx.moveTo(hx + hr, hy);
            ctx.lineTo(hx + hw - hr, hy);
            ctx.quadraticCurveTo(hx + hw, hy, hx + hw, hy + hr);
            ctx.lineTo(hx + hw, hy + hh - hr);
            ctx.quadraticCurveTo(hx + hw, hy + hh, hx + hw - hr, hy + hh);
            ctx.lineTo(hx + hr, hy + hh);
            ctx.quadraticCurveTo(hx, hy + hh, hx, hy + hh - hr);
            ctx.lineTo(hx, hy + hr);
            ctx.quadraticCurveTo(hx, hy, hx + hr, hy);
            ctx.closePath();
            ctx.fill();

            ctx.fillStyle = '#2de2a6';
            ctx.font = 'bold 15px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(headerText || '🧕 RESEPSIONIS', hx + hw / 2, hy + hh / 2);

            // Main Chat Text ("Selamat Datang! 👋")
            ctx.fillStyle = '#063529';
            ctx.font = 'bold 36px "Outfit", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(mainText || 'Selamat Datang! 👋', 256, 102);

            // Subtitle Text ("Tekan [E] untuk Profil")
            ctx.fillStyle = '#047857';
            ctx.font = 'bold 18px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(subText || 'Tekan [E] atau Sentuh untuk Profil', 256, 148);

            const tex = new THREE.CanvasTexture(canvas);
            tex.needsUpdate = true;
            return tex;
        }

        // ==============================================================
        // 1. INDOOR CERAMIC FLOOR (SEPANJANG DIDALAM SEKOLAH)
        // ==============================================================
        const floorGeo = new THREE.PlaneGeometry(66, 76);
        const floorMat = new THREE.MeshStandardMaterial({
            map: tileTexture,
            roughness: 0.28,
            metalness: 0.1,
        });
        const floor = new THREE.Mesh(floorGeo, floorMat);
        floor.rotation.x = -Math.PI / 2;
        floor.receiveShadow = true;
        scene.add(floor);

        // Ground Interaction Rings Array
        const groundRings = [];
        function createGroundedRing(x, z) {
            const ringGeo = new THREE.RingGeometry(1.2, 2.7, 32);
            const ringMat = new THREE.MeshBasicMaterial({ 
                color: 0x2de2a6, 
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
        // 1B. CENTRAL ATRIUM ISLAMIC FLOOR MEDALLION (MEDALI BALAIRUNG UTAMA)
        // ==============================================================
        const hallMedallionGroup = new THREE.Group();
        hallMedallionGroup.position.set(0, 0, 8);

        // Intricate Islamic Geometric Floor Medallion Decal
        const medallionGeo = new THREE.CircleGeometry(4.6, 64);
        const medallionMat = new THREE.MeshStandardMaterial({
            map: hallFloorEmblemTexture,
            transparent: true,
            roughness: 0.35,
            metalness: 0.2,
            polygonOffset: true,
            polygonOffsetFactor: -1,
            polygonOffsetUnits: -1
        });
        const medallionMesh = new THREE.Mesh(medallionGeo, medallionMat);
        medallionMesh.rotation.x = -Math.PI / 2;
        medallionMesh.position.y = 0.035;
        medallionMesh.receiveShadow = true;
        hallMedallionGroup.add(medallionMesh);

        // Polished Brass Outer Border Ring
        const ringOuterGeo = new THREE.RingGeometry(4.55, 4.85, 64);
        const ringOuterMat = new THREE.MeshStandardMaterial({
            color: 0xf4c444,
            roughness: 0.25,
            metalness: 0.85,
            side: THREE.DoubleSide
        });
        const ringOuter = new THREE.Mesh(ringOuterGeo, ringOuterMat);
        ringOuter.rotation.x = -Math.PI / 2;
        ringOuter.position.y = 0.038;
        hallMedallionGroup.add(ringOuter);

        // Thin Inner Gold Accent Ring
        const ringInnerGeo = new THREE.RingGeometry(2.35, 2.45, 48);
        const ringInner = new THREE.Mesh(ringInnerGeo, ringOuterMat);
        ringInner.rotation.x = -Math.PI / 2;
        ringInner.position.y = 0.039;
        hallMedallionGroup.add(ringInner);

        // 4 Architectural Brass Floor Uplights at Cardinal Edges
        const uplightPositions = [
            [0, 5.2], [0, -5.2], [5.2, 0], [-5.2, 0]
        ];
        uplightPositions.forEach(([ux, uz]) => {
            const upLightHousing = new THREE.Mesh(
                new THREE.CylinderGeometry(0.24, 0.28, 0.08, 16),
                new THREE.MeshLambertMaterial({ color: 0x144d40 })
            );
            upLightHousing.position.set(ux, 0.04, uz);
            hallMedallionGroup.add(upLightHousing);

            const upLightLens = new THREE.Mesh(
                new THREE.CircleGeometry(0.18, 16),
                new THREE.MeshBasicMaterial({ color: 0xfff3d1 })
            );
            upLightLens.rotation.x = -Math.PI / 2;
            upLightLens.position.set(ux, 0.082, uz);
            hallMedallionGroup.add(upLightLens);

            const upLight = new THREE.PointLight(0xfff1cc, 0.75, 8.5);
            upLight.position.set(ux, 0.6, uz);
            hallMedallionGroup.add(upLight);
        });

        scene.add(hallMedallionGroup);

        // Ground Interaction Ring on Floor Medallion
        createGroundedRing(0, 8);

        checkpoints.push({
            name: "Medali Balairung &amp; Filosofi Keislaman",
            subtitle: "Tekan [E] atau Sentuh untuk membaca Filosofi Kampus",
            type: "monument",
            x: 0,
            z: 8,
            radius: 4.8
        });

        // ==============================================================
        // 1C. AMBIENT SUNBEAM DUST MOTES & SPARKLES (PARTIKEL HIDUP ATRIUM)
        // ==============================================================
        const dustCount = 140;
        const dustGeo = new THREE.BufferGeometry();
        const dustPositions = new Float32Array(dustCount * 3);
        const dustVelocities = [];

        for (let i = 0; i < dustCount; i++) {
            dustPositions[i * 3] = (Math.random() - 0.5) * 36;      // x: -18 to 18
            dustPositions[i * 3 + 1] = 0.5 + Math.random() * 8.0;   // y: 0.5 to 8.5
            dustPositions[i * 3 + 2] = (Math.random() - 0.5) * 44;  // z: -22 to 22
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
            opacity: 0.72,
            blending: THREE.AdditiveBlending
        });
        const dustParticles = new THREE.Points(dustGeo, dustMat);
        scene.add(dustParticles);

        // ==============================================================
        // 2. ENCLOSING WALLS & SKIRTING BOARDS (DINDING INTERIOR)
        // ==============================================================
        const wallMat = new THREE.MeshLambertMaterial({ color: 0xf6f3ea });
        const skirtingMat = new THREE.MeshLambertMaterial({ color: 0x144d40 });

        function buildWall(x, z, w, h, d) {
            const wall = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), wallMat);
            wall.position.set(x, h / 2, z);
            wall.receiveShadow = true;
            wall.castShadow = true;
            scene.add(wall);

            // Skirting baseboard at bottom
            const skirt = new THREE.Mesh(new THREE.BoxGeometry(w + 0.05, 0.45, d + 0.05), skirtingMat);
            skirt.position.set(x, 0.225, z);
            scene.add(skirt);
            return wall;
        }

        // North Wall (Behind Musala)
        buildWall(6.1, -38, 53.8, 9.0, 1.0);
        // South Wall (Front Entrance Wall with glass doors opening)
        buildWall(-13.9, 38, 13.8, 9.0, 1.0);
        buildWall(20, 38, 26, 9.0, 1.0);
        // East Wall (Classroom exterior side)
        buildWall(33, 0, 1.0, 9.0, 76);
        // West Wall (Corridor & Lockers side - Mentok tepat di belakang lemari, mading & piala)
        buildWall(-20.8, 0, 0.8, 9.0, 76);

        // Full Indoor Ceiling Plane (Menutup Seluruh Atap Aula Agar Tidak Tembus Langit Luar)
        const ceilingGeo = new THREE.PlaneGeometry(66, 76);
        const ceilingMat = new THREE.MeshLambertMaterial({ color: 0xf3efe6, side: THREE.DoubleSide });
        const indoorCeiling = new THREE.Mesh(ceilingGeo, ceilingMat);
        indoorCeiling.rotation.x = Math.PI / 2;
        indoorCeiling.position.set(0, 9.0, 0);
        scene.add(indoorCeiling);

        // Ceiling Rafters & Skylight Rafter Beams
        for (let bz = -30; bz <= 30; bz += 12) {
            const beam = new THREE.Mesh(new THREE.BoxGeometry(66, 0.65, 0.8), new THREE.MeshLambertMaterial({ color: 0x1b4d3e }));
            beam.position.set(0, 8.8, bz);
            scene.add(beam);

            // Ceiling Recessed Downlights
            [-16, -6, 6, 16].forEach(bx => {
                const lamp = new THREE.Mesh(new THREE.CylinderGeometry(0.35, 0.35, 0.1, 16), new THREE.MeshBasicMaterial({ color: 0xfff6dd }));
                lamp.position.set(bx, 8.45, bz);
                scene.add(lamp);
            });
        }

        // Stately Pillars with Golden Capital Rings (Pilar lobi dan depan kelas telah dihilangkan)
        const pillarMat = new THREE.MeshLambertMaterial({ color: 0xede6d8 });
        const goldMat = new THREE.MeshLambertMaterial({ color: 0xf4c444 });
        const pillarPositions = [
            [-10, 12], [10, 12],
            [-10, 0],
            [-10, -12], [10, -12]
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
        // 3. LOBI UTAMA & PAPAN PAKET BIAYA PENDIDIKAN (ADMISION SECTION)
        // ==============================================================
        // Standing Display Board for Tuition Packages (Papan Harga Sekolah)
        const tuitionBoardGroup = new THREE.Group();
        tuitionBoardGroup.position.set(-8, 0, 20);

        const tPlinth = new THREE.Mesh(new THREE.BoxGeometry(6.4, 0.35, 1.2), new THREE.MeshLambertMaterial({ color: 0x1f2937 }));
        tPlinth.position.y = 0.18;
        tuitionBoardGroup.add(tPlinth);

        const tPoles = [-2.8, 2.8];
        tPoles.forEach(px => {
            const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.08, 7.8, 12), new THREE.MeshLambertMaterial({ color: 0xf4c444 }));
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

        createGroundedRing(-8, 22);

        checkpoints.push({
            name: "Papan Rincian Paket Biaya &amp; Admisi",
            subtitle: "Tekan [E] atau Sentuh untuk Konsultasi Pendaftaran",
            type: "admission",
            x: -8,
            z: 22,
            radius: 5.5
        });

        // Meja Resepsionis & Front Desk
        const receptionGroup = new THREE.Group();
        receptionGroup.position.set(8, 0, 20);

        const rDesk = new THREE.Mesh(new THREE.BoxGeometry(6.4, 2.2, 1.8), new THREE.MeshLambertMaterial({ color: 0x244238 }));
        rDesk.position.y = 1.1;
        receptionGroup.add(rDesk);

        const rTop = new THREE.Mesh(new THREE.BoxGeometry(6.6, 0.15, 2.0), new THREE.MeshLambertMaterial({ color: 0xded5c3 }));
        rTop.position.y = 2.25;
        receptionGroup.add(rTop);

        // Brass Plaque on Front of Counter: "PUSAT INFORMASI & PROFIL"
        const rPlaque = new THREE.Mesh(new THREE.BoxGeometry(3.2, 0.6, 0.08), goldMat);
        rPlaque.position.set(0, 1.35, 0.95);
        receptionGroup.add(rPlaque);

        // Computer monitor shifted to the right of the desk
        const monitor = new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.8, 0.1), new THREE.MeshLambertMaterial({ color: 0x111111 }));
        monitor.position.set(1.9, 2.75, -0.2);
        monitor.rotation.y = -0.15;
        receptionGroup.add(monitor);

        const screen = new THREE.Mesh(new THREE.PlaneGeometry(1.1, 0.7), new THREE.MeshBasicMaterial({ color: 0x2de2a6 }));
        screen.position.set(1.9, 2.75, -0.14);
        screen.rotation.y = -0.15;
        receptionGroup.add(screen);

        // Open Guest Book Register notebook on counter
        const guestBook = new THREE.Mesh(new THREE.BoxGeometry(0.75, 0.04, 0.52), new THREE.MeshLambertMaterial({ color: 0xfffff0 }));
        guestBook.position.set(-0.6, 2.34, 0.15);
        guestBook.rotation.y = 0.12;
        receptionGroup.add(guestBook);

        // Gold Service Desk Call Bell
        const serviceBell = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.18, 0.14, 14), goldMat);
        serviceBell.position.set(-1.5, 2.39, 0.25);
        receptionGroup.add(serviceBell);

        // Elegant Flower Vase with Roses on left corner
        const rVase = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.15, 0.48, 14), new THREE.MeshLambertMaterial({ color: 0xffffff }));
        rVase.position.set(-2.5, 2.56, -0.1);
        receptionGroup.add(rVase);

        const flowerBloom = new THREE.Mesh(new THREE.SphereGeometry(0.22, 10, 8), new THREE.MeshLambertMaterial({ color: 0xec4899 }));
        flowerBloom.position.set(-2.5, 2.9, -0.1);
        receptionGroup.add(flowerBloom);

        scene.add(receptionGroup);

        createGroundedRing(8, 22.5);

        checkpoints.push({
            name: "Meja Resepsionis &amp; Profil Sekolah",
            subtitle: "Disambut Ustadzah &bull; Tekan [E] atau Sentuh untuk Profil Sekolah",
            type: "reception",
            x: 8,
            z: 22.5,
            radius: 4.8
        });

        // Standing National Flag & School Crest beside front desk
        const flagPoleGroup = new THREE.Group();
        flagPoleGroup.position.set(12.5, 0, 21);
        const fStand = new THREE.Mesh(new THREE.CylinderGeometry(0.35, 0.45, 0.3, 16), goldMat);
        fStand.position.y = 0.15;
        flagPoleGroup.add(fStand);
        const fRod = new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.04, 5.0, 12), goldMat);
        fRod.position.y = 2.5;
        flagPoleGroup.add(fRod);

        // Merah Putih Flag
        const flagRed = new THREE.Mesh(new THREE.PlaneGeometry(1.6, 0.5), new THREE.MeshLambertMaterial({ color: 0xdd2222, side: THREE.DoubleSide }));
        flagRed.position.set(0.8, 4.4, 0);
        flagPoleGroup.add(flagRed);
        const flagWhite = new THREE.Mesh(new THREE.PlaneGeometry(1.6, 0.5), new THREE.MeshLambertMaterial({ color: 0xffffff, side: THREE.DoubleSide }));
        flagWhite.position.set(0.8, 3.9, 0);
        flagPoleGroup.add(flagWhite);
        scene.add(flagPoleGroup);

        // ==============================================================
        // ENCLOSED RECEPTION OFFICE & RIGHT WALL (DINDING RESEPSIONIS TERTUTUP)
        // ==============================================================
        // 1. Dinding Kanan Resepsionis (Menutup sisi kanan agar tidak tembus pandang)
        buildWall(15.2, 19.1, 0.6, 9.0, 10.2);

        // 2. Dinding Belakang Resepsionis & Tata Usaha (Penuh hingga plafon 9.0m)
        buildWall(17.7, 14.0, 30.6, 9.0, 0.6);

        // 3. Kolom Pilar Pembatas Kiri Resepsionis
        const rColL = new THREE.Mesh(new THREE.CylinderGeometry(0.45, 0.52, 9.0, 16), pillarMat);
        rColL.position.set(2.4, 4.5, 14.0);
        scene.add(rColL);

        // 4. Panel Grafis Arsitektural Mewah di Belakang Resepsionis
        const lobbyFeatureWall = new THREE.Group();
        lobbyFeatureWall.position.set(8.5, 0, 14.32);

        // Fluted Wood Slat Wall Backing
        const wallBack = new THREE.Mesh(new THREE.BoxGeometry(11.4, 8.4, 0.25), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        wallBack.position.y = 4.4;
        wallBack.receiveShadow = true;
        lobbyFeatureWall.add(wallBack);

        // High-Resolution Architectural Graphic Panel
        const wallFace = new THREE.Mesh(new THREE.PlaneGeometry(11.2, 8.2), new THREE.MeshLambertMaterial({ map: lobbyWallTexture }));
        wallFace.position.set(0, 4.4, 0.14);
        lobbyFeatureWall.add(wallFace);

        // Golden Trim Molding Frame Top & Base
        const fTrimTop = new THREE.Mesh(new THREE.BoxGeometry(11.6, 0.25, 0.32), goldMat);
        fTrimTop.position.set(0, 8.6, 0);
        lobbyFeatureWall.add(fTrimTop);

        const fTrimBase = new THREE.Mesh(new THREE.BoxGeometry(11.6, 0.45, 0.32), skirtingMat);
        fTrimBase.position.set(0, 0.225, 0);
        lobbyFeatureWall.add(fTrimBase);

        // Warm Cove LED Uplight for the Feature Wall
        const coveLight = new THREE.PointLight(0xffecd1, 0.95, 14);
        coveLight.position.set(0, 4.5, 1.2);
        lobbyFeatureWall.add(coveLight);

        scene.add(lobbyFeatureWall);

        // Lobby Waiting Sofas
        [-18, 18].forEach(sx => {
            const sofa = new THREE.Mesh(new THREE.BoxGeometry(4.2, 1.4, 1.8), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
            sofa.position.set(sx, 0.7, 26);
            scene.add(sofa);

            const sBack = new THREE.Mesh(new THREE.BoxGeometry(4.2, 1.6, 0.5), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
            sBack.position.set(sx, 1.5, 26.65);
            scene.add(sBack);
        });

        // ==============================================================
        // 4. PAPAN PENGUMUMAN & MADING SANTRI (NOTICE BOARD)
        // ==============================================================
        const noticeGroup = new THREE.Group();
        noticeGroup.position.set(-20, 0, 10);

        const nFrame = new THREE.Mesh(new THREE.BoxGeometry(0.2, 5.2, 9.2), new THREE.MeshLambertMaterial({ color: 0x4a2e18 }));
        nFrame.position.set(0, 4.2, 0);
        noticeGroup.add(nFrame);

        const nFace = new THREE.Mesh(new THREE.PlaneGeometry(9.0, 5.0), new THREE.MeshLambertMaterial({ map: createNoticeBoardTexture() }));
        nFace.rotation.y = Math.PI / 2;
        nFace.position.set(0.12, 4.2, 0);
        noticeGroup.add(nFace);

        scene.add(noticeGroup);

        createGroundedRing(-15, 10);

        checkpoints.push({
            name: "Papan Pengumuman &amp; Mading Sekolah",
            subtitle: "Tekan [E] atau Sentuh untuk membaca Agenda &amp; Kabar Santri",
            type: "notice",
            x: -15,
            z: 10,
            radius: 5.5
        });

        // Student School Lockers beside Mading
        const lockerGroup = new THREE.Group();
        lockerGroup.position.set(-20, 0, 2);
        for (let lz = -3; lz <= 3; lz += 1.5) {
            const locker = new THREE.Mesh(new THREE.BoxGeometry(0.8, 4.8, 1.3), new THREE.MeshLambertMaterial({ color: (lz % 3 === 0) ? 0x1f5c4d : 0x5a6e67 }));
            locker.position.set(0, 2.4, lz);
            lockerGroup.add(locker);

            const handle = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.4, 0.08), goldMat);
            handle.position.set(0.45, 2.4, lz);
            lockerGroup.add(handle);
        }
        scene.add(lockerGroup);

        // ==============================================================
        // 5. RUANG KELAS PEMBELAJARAN (INTERACTIVE CLASSROOM)
        // ==============================================================
        const classGroup = new THREE.Group();
        classGroup.position.set(22, 0, -2);

        // Classroom Glass Partition Wall & Open Doorway
        const partWallN = new THREE.Mesh(new THREE.BoxGeometry(0.3, 8.5, 6.0), wallMat);
        partWallN.position.set(-8, 4.25, -6.5);
        classGroup.add(partWallN);

        const partWallS = new THREE.Mesh(new THREE.BoxGeometry(0.3, 8.5, 6.0), wallMat);
        partWallS.position.set(-8, 4.25, 6.5);
        classGroup.add(partWallS);

        const doorHeader = new THREE.Mesh(new THREE.BoxGeometry(0.3, 2.5, 7.0), wallMat);
        doorHeader.position.set(-8, 7.25, 0);
        classGroup.add(doorHeader);

        // Dinding Kanan Kelas (Sisi Selatan) - Menutup penuh mentok hingga dinding luar timur
        const classWallS = new THREE.Mesh(new THREE.BoxGeometry(19.0, 9.0, 0.5), wallMat);
        classWallS.position.set(1.5, 4.5, 9.5);
        classWallS.castShadow = true;
        classWallS.receiveShadow = true;
        classGroup.add(classWallS);

        const classWallSSkirt = new THREE.Mesh(new THREE.BoxGeometry(19.1, 0.45, 0.55), skirtingMat);
        classWallSSkirt.position.set(1.5, 0.225, 9.5);
        classGroup.add(classWallSSkirt);

        // Dinding Kiri Kelas (Sisi Utara) - Menutup penuh mentok hingga dinding luar timur
        const classWallN = new THREE.Mesh(new THREE.BoxGeometry(19.0, 9.0, 0.5), wallMat);
        classWallN.position.set(1.5, 4.5, -9.5);
        classWallN.castShadow = true;
        classWallN.receiveShadow = true;
        classGroup.add(classWallN);

        const classWallNSkirt = new THREE.Mesh(new THREE.BoxGeometry(19.1, 0.45, 0.55), skirtingMat);
        classWallNSkirt.position.set(1.5, 0.225, -9.5);
        classGroup.add(classWallNSkirt);

        // Plafon Tertutup Ruang Kelas
        const classCeiling = new THREE.Mesh(new THREE.PlaneGeometry(19.0, 19.0), new THREE.MeshLambertMaterial({ color: 0xf8f6f0, side: THREE.DoubleSide }));
        classCeiling.rotation.x = Math.PI / 2;
        classCeiling.position.set(1.5, 8.95, 0);
        classGroup.add(classCeiling);

        const classLight = new THREE.PointLight(0xfff5e0, 0.95, 18);
        classLight.position.set(1.5, 7.5, 0);
        classGroup.add(classLight);

        // Classroom Plaque Sign
        const classSign = new THREE.Mesh(new THREE.BoxGeometry(0.45, 0.9, 4.6), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        classSign.position.set(-8.15, 6.2, 0);
        classGroup.add(classSign);

        // Big Whiteboard on North Wall of Classroom
        const wBoard = new THREE.Mesh(new THREE.BoxGeometry(9.6, 5.0, 0.15), new THREE.MeshLambertMaterial({ color: 0x94a3b8 }));
        wBoard.position.set(0, 4.2, -9.2);
        classGroup.add(wBoard);

        const wFace = new THREE.Mesh(new THREE.PlaneGeometry(9.4, 4.8), new THREE.MeshLambertMaterial({ map: createWhiteboardTexture() }));
        wFace.position.set(0, 4.2, -9.1);
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

                // Book on desk
                const sBook = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.08, 0.65), goldMat);
                sBook.position.set(dx, 1.24, dz);
                classGroup.add(sBook);

                // Chair
                const sChair = new THREE.Mesh(new THREE.BoxGeometry(0.9, 0.8, 0.9), chairMat);
                sChair.position.set(dx, 0.4, dz + 0.95);
                classGroup.add(sChair);
            });
        });

        // Classroom Bookshelf
        const bShelf = new THREE.Mesh(new THREE.BoxGeometry(1.4, 5.2, 3.8), new THREE.MeshLambertMaterial({ color: 0x5a381d }));
        bShelf.position.set(7.5, 2.6, 6.0);
        classGroup.add(bShelf);

        scene.add(classGroup);

        createGroundedRing(14, -2);

        checkpoints.push({
            name: "Ruang Kelas &bull; Program &amp; Tahfiz",
            subtitle: "Tekan [E] atau Sentuh untuk melihat Kurikulum &amp; Program Kelas",
            type: "program",
            x: 14,
            z: -2,
            radius: 6.0
        });

        // ==============================================================
        // 6. ETALASE LEMARI PIALA PRESTASI (TROPHY SHOWCASE GALLERY)
        // ==============================================================
        const trophyGroup = new THREE.Group();
        trophyGroup.position.set(-20, 0, -6);

        // Showcase Cabinet Frame
        const tCabinet = new THREE.Mesh(new THREE.BoxGeometry(0.8, 6.0, 7.8), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        tCabinet.position.set(0, 3.0, 0);
        trophyGroup.add(tCabinet);

        // Red Carpet Runner leading to showcase
        const runner = new THREE.Mesh(new THREE.PlaneGeometry(5.0, 7.8), new THREE.MeshLambertMaterial({ color: 0x991b1b }));
        runner.rotation.x = -Math.PI / 2;
        runner.position.set(2.8, 0.02, 0);
        runner.receiveShadow = true;
        trophyGroup.add(runner);

        // Golden Trophies inside showcase
        [-2.4, -0.8, 0.8, 2.4].forEach(tz => {
            const tBase = new THREE.Mesh(new THREE.CylinderGeometry(0.4, 0.5, 0.6, 16), new THREE.MeshLambertMaterial({ color: 0x1f2937 }));
            tBase.position.set(0.15, 2.0, tz);
            trophyGroup.add(tBase);

            const cup = new THREE.Mesh(new THREE.CylinderGeometry(0.5, 0.25, 1.2, 16), goldMat);
            cup.position.set(0.15, 2.9, tz);
            trophyGroup.add(cup);
        });

        scene.add(trophyGroup);

        createGroundedRing(-14, -6);

        checkpoints.push({
            name: "Etalase Prestasi &amp; Lemari Piala",
            subtitle: "Tekan [E] atau Sentuh untuk melihat Etalase Juara",
            type: "trophy",
            x: -14,
            z: -6,
            radius: 5.5
        });

        // Architectural Masterplan Campus Mural Gallery Frame
        const muralGroup = new THREE.Group();
        muralGroup.position.set(-20, 0, -15);

        const mFrame = new THREE.Mesh(new THREE.BoxGeometry(0.2, 4.4, 7.2), new THREE.MeshLambertMaterial({ color: 0x1f2937 }));
        mFrame.position.set(0, 4.2, 0);
        muralGroup.add(mFrame);

        const mGoldBorder = new THREE.Mesh(new THREE.BoxGeometry(0.22, 4.48, 7.28), goldMat);
        mGoldBorder.position.set(0.01, 4.2, 0);
        muralGroup.add(mGoldBorder);

        const mArt = new THREE.Mesh(new THREE.PlaneGeometry(7.0, 4.2), new THREE.MeshLambertMaterial({ map: schoolTexture }));
        mArt.rotation.y = Math.PI / 2;
        mArt.position.set(0.12, 4.2, 0);
        muralGroup.add(mArt);

        // Brass Plaque beneath mural: "Masterplan Kawasan Kampus Terpadu"
        const mPlaque = new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.45, 3.2), goldMat);
        mPlaque.position.set(0.1, 1.7, 0);
        muralGroup.add(mPlaque);

        // Art Gallery Spotlight
        const mSpot = new THREE.PointLight(0xfff6dd, 0.85, 11);
        mSpot.position.set(1.4, 5.5, 0);
        muralGroup.add(mSpot);

        scene.add(muralGroup);

        // ==============================================================
        // 7. LAYAR DIGITAL SMART TV LOBI (VIDEO PROFIL SEKOLAH UNGGULAN)
        // ==============================================================
        // Diposisikan di Ruang Tamu Lobi Barat, menghadap ke selatan (jelas terlihat dari pintu masuk)
        const tvGroup = new THREE.Group();
        tvGroup.position.set(-17, 0, 18);

        // Kios / Stand Media Lantai
        const tvStandBase = new THREE.Mesh(new THREE.BoxGeometry(6.6, 0.12, 1.4), new THREE.MeshLambertMaterial({ color: 0x1f2937 }));
        tvStandBase.position.y = 0.06;
        tvGroup.add(tvStandBase);

        [-2.2, 2.2].forEach(px => {
            const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.09, 0.09, 3.6, 16), new THREE.MeshLambertMaterial({ color: 0x111827 }));
            pole.position.set(px, 1.8, 0);
            tvGroup.add(pole);
        });

        // Frame TV Layar Lebar 16:9
        const tvFrame = new THREE.Mesh(new THREE.BoxGeometry(7.4, 4.4, 0.18), new THREE.MeshLambertMaterial({ color: 0x0f172a }));
        tvFrame.position.set(0, 4.2, 0.05);
        tvGroup.add(tvFrame);

        // Slim Golden Outer Bezel Trim
        const tvBezel = new THREE.Mesh(new THREE.BoxGeometry(7.46, 4.46, 0.19), goldMat);
        tvBezel.position.set(0, 4.2, 0.04);
        tvGroup.add(tvBezel);

        // Layar Video Digital 16:9 Menghadap ke Selatan (+Z) - Tulisan Normal & Tidak Terbalik
        const tvScreen = new THREE.Mesh(new THREE.PlaneGeometry(7.2, 4.2), new THREE.MeshBasicMaterial({ map: tvTexture }));
        tvScreen.position.set(0, 4.2, 0.16);
        tvGroup.add(tvScreen);

        // Lampu Pendaran Layar (Ambient Screen Glow) ke Area Ruang Tunggu
        const tvGlow = new THREE.PointLight(0x2de2a6, 0.95, 14);
        tvGlow.position.set(0, 4.2, 1.6);
        tvGroup.add(tvGlow);

        scene.add(tvGroup);

        createGroundedRing(-17, 21.5);

        checkpoints.push({
            name: "Smart TV &bull; Profil Sekolah",
            subtitle: "Tekan [E] atau Sentuh untuk memutar Video Profil",
            type: "cinema",
            x: -17,
            z: 21.5,
            radius: 5.5
        });

        // ==============================================================
        // 8. MUSALA SEKOLAH (PUSAT IBADAH & RUHIYAH DALAM SEKOLAH)
        // ==============================================================
        const musalaGroup = new THREE.Group();
        musalaGroup.position.set(0, 0, -25);

        // Grand Moorish Arch Entrance
        const mArchColL = new THREE.Mesh(new THREE.CylinderGeometry(0.8, 0.9, 8.5, 16), pillarMat);
        mArchColL.position.set(-8.5, 4.25, 7.0);
        musalaGroup.add(mArchColL);

        const mArchColR = new THREE.Mesh(new THREE.CylinderGeometry(0.8, 0.9, 8.5, 16), pillarMat);
        mArchColR.position.set(8.5, 4.25, 7.0);
        musalaGroup.add(mArchColR);

        const mArchHeader = new THREE.Mesh(new THREE.BoxGeometry(19, 1.8, 1.6), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        mArchHeader.position.set(0, 7.8, 7.0);
        musalaGroup.add(mArchHeader);

        // Prayer Carpet (Sajadah Hijau Zamrud - Luxury Arched Velvet Motif)
        const carpet = new THREE.Mesh(new THREE.PlaneGeometry(16, 13), new THREE.MeshLambertMaterial({ 
            map: carpetTexture,
            roughness: 0.85,
            metalness: 0.05
        }));
        carpet.rotation.x = -Math.PI / 2;
        carpet.position.set(0, 0.02, 0);
        carpet.receiveShadow = true;
        musalaGroup.add(carpet);

        // Mihrab Wall with Mosque Texture
        const mihrab = new THREE.Mesh(new THREE.PlaneGeometry(14, 8.2), new THREE.MeshLambertMaterial({ map: mosqueTexture }));
        mihrab.position.set(0, 4.1, -6.4);
        musalaGroup.add(mihrab);

        // Crescent on Mihrab
        const mCrescent = new THREE.Mesh(new THREE.TorusGeometry(0.8, 0.16, 10, 20, Math.PI * 1.5), goldMat);
        mCrescent.position.set(0, 7.5, -6.3);
        musalaGroup.add(mCrescent);

        scene.add(musalaGroup);

        createGroundedRing(0, -17);

        checkpoints.push({
            name: "Musala Al-Ibrahim &bull; Visi &amp; Misi",
            subtitle: "Tekan [E] atau Sentuh untuk membuka Visi & Misi",
            type: "mosque",
            x: 0,
            z: -17,
            radius: 6.0
        });

        // ==============================================================
        // 9. INDOOR POTTED PLANTS & PROPS (ASRI DI SEPANJANG KORIDOR)
        // ==============================================================
        const plantPositions = [
            [-12, 19], [12, 19],
            [-10, 8],  [10, 8],
            [-12, -4], [12, -4],
            [-18, 30], [18, 30],
            [-18, 14], [22, 18]
        ];
        plantPositions.forEach(([px, pz]) => {
            const pot = new THREE.Mesh(new THREE.CylinderGeometry(0.5, 0.4, 0.8, 16), new THREE.MeshLambertMaterial({ color: 0xfafafa }));
            pot.position.set(px, 0.4, pz);
            pot.castShadow = true;
            scene.add(pot);

            // Soil
            const soil = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.48, 0.05, 16), new THREE.MeshLambertMaterial({ color: 0x3d2716 }));
            soil.position.set(px, 0.78, pz);
            scene.add(soil);

            // Plant leaves (cluster of emerald spheres & fronds)
            const foliage = new THREE.Group();
            foliage.position.set(px, 1.2, pz);
            for (let i = 0; i < 5; i++) {
                const leaf = new THREE.Mesh(
                    new THREE.SphereGeometry(0.45, 10, 10),
                    new THREE.MeshLambertMaterial({ color: i % 2 === 0 ? 0x22c55e : 0x15803d })
                );
                leaf.position.set((Math.random() - 0.5) * 0.5, (Math.random() - 0.5) * 0.4, (Math.random() - 0.5) * 0.5);
                leaf.scale.set(1.0, 1.4, 0.6);
                foliage.add(leaf);
            }
            scene.add(foliage);
        });

        // Suspended Ceiling Directional Signs
        const signGroup = new THREE.Group();
        signGroup.position.set(0, 7.8, 12);
        const sBar = new THREE.Mesh(new THREE.BoxGeometry(7.2, 0.6, 0.1), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        signGroup.add(sBar);
        scene.add(signGroup);

        // ==============================================================
        // 11. INDOOR CAMPUS NPCS (LIVELY TEACHER & STUDENTS)
        // ==============================================================
        // NPC 1: Ustadzah Resepsionis at front desk
        const npcDesk = new THREE.Group();
        npcDesk.position.set(8, 0, 18.2);

        // Warm spotlight illuminating the receptionist and front desk
        const deskSpot = new THREE.PointLight(0xfffaed, 0.9, 14);
        deskSpot.position.set(0, 4.8, 1.5);
        npcDesk.add(deskSpot);

        // Ergonomic office swivel stool behind counter
        const stoolBase = new THREE.Mesh(new THREE.CylinderGeometry(0.35, 0.45, 0.8, 16), new THREE.MeshLambertMaterial({ color: 0x1f2937 }));
        stoolBase.position.y = 0.4;
        npcDesk.add(stoolBase);

        // Body / Pine green formal uniform dress
        const ndBody = new THREE.Mesh(new THREE.CylinderGeometry(0.44, 0.48, 1.35, 18), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        ndBody.position.y = 1.95;
        ndBody.castShadow = true;
        npcDesk.add(ndBody);

        // Gold receptionist nametag badge on chest
        const ndTag = new THREE.Mesh(new THREE.BoxGeometry(0.24, 0.11, 0.04), goldMat);
        ndTag.position.set(0.2, 2.15, 0.47);
        npcDesk.add(ndTag);

        // Hijab mantle draped around neck and shoulders
        const ndHijabMat = new THREE.MeshLambertMaterial({ color: 0x2de2a6 });
        const ndMantle = new THREE.Mesh(new THREE.CylinderGeometry(0.38, 0.74, 0.8, 20), ndHijabMat);
        ndMantle.position.y = 2.48;
        npcDesk.add(ndMantle);

        const ndBrooch = new THREE.Mesh(new THREE.SphereGeometry(0.055, 12, 12), goldMat);
        ndBrooch.position.set(0.24, 2.58, 0.42);
        npcDesk.add(ndBrooch);

        // --------------------------------------------------------------
        // UNIFIED HEAD GROUP (Rotates with greeting gaze, contains Face, Eyes, Hijab)
        // --------------------------------------------------------------
        const ndHeadGroup = new THREE.Group();
        ndHeadGroup.position.set(0, 3.08, 0);

        // 1. Soft Warm Face Skin Sphere (Smooth 24x24)
        const skinMat = new THREE.MeshLambertMaterial({ color: 0xfce1cc });
        const faceSkin = new THREE.Mesh(new THREE.SphereGeometry(0.48, 24, 24), skinMat);
        faceSkin.position.set(0, 0, 0.02);
        faceSkin.castShadow = true;
        ndHeadGroup.add(faceSkin);

        // 2. Hijab Back & Sides Hood (Centered slightly back so face skin protrudes cleanly in front)
        const hijabBack = new THREE.Mesh(new THREE.SphereGeometry(0.51, 24, 24), ndHijabMat);
        hijabBack.position.set(0, 0.03, -0.10);
        ndHeadGroup.add(hijabBack);

        // 3. Hijab Face Framing Cowl (A rounded torus frame hugging the face oval cleanly)
        const hijabFrame = new THREE.Mesh(new THREE.TorusGeometry(0.45, 0.055, 12, 32), ndHijabMat);
        hijabFrame.position.set(0, -0.01, 0.18);
        ndHeadGroup.add(hijabFrame);

        // 4. Inner Ciput Under-Scarf Band (Dark teal trim framing the forehead)
        const ciputMat = new THREE.MeshLambertMaterial({ color: 0x144d40 });
        const ciputBand = new THREE.Mesh(new THREE.TorusGeometry(0.45, 0.028, 10, 24, Math.PI * 0.85), ciputMat);
        ciputBand.rotation.x = 0.38;
        ciputBand.position.set(0, 0.18, 0.19);
        ndHeadGroup.add(ciputBand);

        // 5. Large Expressive Chibi Anime Eyes with Sparkles
        const eyeMat = new THREE.MeshBasicMaterial({ color: 0x0f172a }); // Deep dark glossy iris
        const spkMat = new THREE.MeshBasicMaterial({ color: 0xffffff }); // Catchlight reflections
        const blushMat = new THREE.MeshBasicMaterial({ color: 0xf87171, transparent: true, opacity: 0.85 }); // Rosy peach blush
        const browMat = new THREE.MeshBasicMaterial({ color: 0x334155 }); // Friendly soft eyebrows
        const mouthMat = new THREE.MeshBasicMaterial({ color: 0xbe123c }); // Sweet welcoming smile

        [-0.16, 0.16].forEach(ex => {
            // Main Eye Oval (Anime Chibi Pupil/Iris)
            const eye = new THREE.Mesh(new THREE.SphereGeometry(0.08, 16, 16), eyeMat);
            eye.scale.set(0.85, 1.35, 0.35);
            eye.position.set(ex, 0.04, 0.478);
            eye.rotation.z = (ex > 0 ? -0.06 : 0.06);
            ndHeadGroup.add(eye);

            // Primary Big Sparkle Catchlight (Top-Outer)
            const spk1 = new THREE.Mesh(new THREE.SphereGeometry(0.026, 10, 10), spkMat);
            spk1.position.set(ex + (ex > 0 ? 0.024 : -0.024), 0.082, 0.498);
            ndHeadGroup.add(spk1);

            // Secondary Small Sparkle Catchlight (Bottom-Inner)
            const spk2 = new THREE.Mesh(new THREE.SphereGeometry(0.015, 8, 8), spkMat);
            spk2.position.set(ex - (ex > 0 ? 0.016 : -0.016), 0.002, 0.498);
            ndHeadGroup.add(spk2);

            // Eyelash upper contour line
            const lash = new THREE.Mesh(new THREE.TorusGeometry(0.078, 0.016, 6, 12, Math.PI * 0.65), eyeMat);
            lash.rotation.z = ex > 0 ? Math.PI * 0.85 : Math.PI * 0.50;
            lash.position.set(ex, 0.12, 0.478);
            ndHeadGroup.add(lash);

            // Gentle Arching Eyebrow
            const brow = new THREE.Mesh(new THREE.BoxGeometry(0.13, 0.024, 0.03), browMat);
            brow.rotation.z = ex > 0 ? -0.12 : 0.12;
            brow.position.set(ex, 0.19, 0.455);
            ndHeadGroup.add(brow);

            // Rosy Blushing Cheek
            const blush = new THREE.Mesh(new THREE.SphereGeometry(0.075, 10, 8), blushMat);
            blush.scale.set(1.4, 0.65, 0.3);
            blush.position.set(ex * 1.55, -0.07, 0.435);
            ndHeadGroup.add(blush);
        });

        // Sweet Warm Smile
        const mouth = new THREE.Mesh(new THREE.TorusGeometry(0.065, 0.018, 6, 14, Math.PI), mouthMat);
        mouth.rotation.x = Math.PI * 0.15;
        mouth.rotation.z = Math.PI;
        mouth.position.set(0, -0.14, 0.478);
        ndHeadGroup.add(mouth);

        npcDesk.add(ndHeadGroup);

        // Left arm resting gracefully forward on counter
        const ndArmL = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.11, 0.85, 12), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        ndArmL.position.set(-0.62, 2.05, 0.35);
        ndArmL.rotation.x = -0.4;
        npcDesk.add(ndArmL);

        // Right arm pivot group (Welcoming greeting wave gesture)
        const ndArmRGroup = new THREE.Group();
        ndArmRGroup.position.set(0.62, 2.3, 0.1);

        const ndArmR = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.11, 0.85, 12), new THREE.MeshLambertMaterial({ color: 0x144d40 }));
        ndArmR.position.y = -0.42;
        ndArmRGroup.add(ndArmR);

        const ndHandR = new THREE.Mesh(new THREE.SphereGeometry(0.12, 10, 10), new THREE.MeshLambertMaterial({ color: 0xfce1cc }));
        ndHandR.position.y = -0.88;
        ndArmRGroup.add(ndHandR);

        npcDesk.add(ndArmRGroup);

        // Floating 3D "Selamat Datang!" Billboard Speech Bubble
        const bubbleTexture = createSpeechBubbleTexture('🧕 RESEPSIONIS', 'Selamat Datang! 👋', 'Tekan [E] untuk Profil Sekolah');
        const bubbleMat = new THREE.SpriteMaterial({ map: bubbleTexture, transparent: true });
        const bubbleSprite = new THREE.Sprite(bubbleMat);
        bubbleSprite.scale.set(3.8, 1.9, 1.0);
        bubbleSprite.position.set(0, 4.35, 0);
        npcDesk.add(bubbleSprite);

        npcDesk.userData = { head: ndHeadGroup, rightArm: ndArmRGroup, bubble: bubbleSprite };
        scene.add(npcDesk);

        // NPC 2: Ustadz Pembina near Musala
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
        const nuArmR = new THREE.Mesh(new THREE.CylinderGeometry(0.14, 0.12, 1.05, 12), new THREE.MeshLambertMaterial({ color: 0xf8fafc }));
        nuArmR.position.set(0.72, 1.6, 0);
        npcUstadz.add(nuArmR);
        npcUstadz.userData = { head: nuHead, rightArm: nuArmR };
        scene.add(npcUstadz);

        // NPC 3: Santri in Classroom (Ahmad - Belajar di meja)
        const npcStudentClass = new THREE.Group();
        npcStudentClass.position.set(18.8, 0, -4.0);
        const nscBody = new THREE.Mesh(new THREE.CylinderGeometry(0.42, 0.45, 1.15, 16), new THREE.MeshLambertMaterial({ color: 0x1b5e4b }));
        nscBody.position.y = 1.2;
        npcStudentClass.add(nscBody);
        const nscHead = new THREE.Mesh(new THREE.SphereGeometry(0.44, 16, 16), new THREE.MeshLambertMaterial({ color: 0xfce1cc }));
        nscHead.position.y = 2.0;
        npcStudentClass.add(nscHead);
        const nscPeci = new THREE.Mesh(new THREE.CylinderGeometry(0.44, 0.46, 0.28, 16), new THREE.MeshLambertMaterial({ color: 0x111111 }));
        nscPeci.position.y = 2.3;
        npcStudentClass.add(nscPeci);
        scene.add(npcStudentClass);

        // ==============================================================
        // 3D THIRD-PERSON PLAYER CHARACTER (ANIMATED STUDENT)
        // ==============================================================
        const player = {
            group: new THREE.Group(),
            x: 0,
            z: 24, // Spawn in Front Entrance Lobby
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
                hat: null,
                peciGroup: null,
                peciMat: null,
                hijabGroup: null,
                hijabMat: null,
                leftSleeve: null,
                rightSleeve: null,
                shoulderL: null,
                shoulderR: null
            }
        };

        function buildPlayer() {
            while (player.group.children.length > 0) {
                player.group.remove(player.group.children[0]);
            }

            const vestColor = 0x144d40; // Pine Green Uniform Vest
            const skinColor = 0xfce1cc; // Warm soft skin
            const darkPantsColor = 0x1a2723;

            // Shared materials
            const skinMat = new THREE.MeshLambertMaterial({ color: skinColor });
            const vestMat = new THREE.MeshLambertMaterial({ color: vestColor });
            const shirtMat = new THREE.MeshLambertMaterial({ color: 0xffffff });
            const pantsMat = new THREE.MeshLambertMaterial({ color: darkPantsColor });
            const shoeMat = new THREE.MeshLambertMaterial({ color: 0x1f2421 });
            const soleMat = new THREE.MeshLambertMaterial({ color: 0xf5f5f5 });
            const goldMat = new THREE.MeshLambertMaterial({ color: 0xecb22e });
            const packMat = new THREE.MeshLambertMaterial({ color: 0xc49b4b });
            const peciMat = new THREE.MeshLambertMaterial({ color: 0x111111 });
            const hijabMat = new THREE.MeshLambertMaterial({ color: 0xfdfdfd });

            // 1. TORSO & BELT (Tapered & Rounded)
            // Belt / Pelvis
            const belt = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.49, 0.18, 20), pantsMat);
            belt.position.y = 1.34;
            belt.castShadow = true;
            player.group.add(belt);

            const buckle = new THREE.Mesh(new THREE.BoxGeometry(0.18, 0.12, 0.08), goldMat);
            buckle.position.set(0, 1.34, 0.48);
            player.group.add(buckle);

            // Upper Torso / Vest (Smooth tapered cylinder)
            const torsoGroup = new THREE.Group();
            const body = new THREE.Mesh(new THREE.CylinderGeometry(0.55, 0.48, 1.05, 20), vestMat);
            body.position.y = 1.88;
            body.castShadow = true;
            torsoGroup.add(body);

            // Inner White Shirt Chest Insert (V-neck vest collar)
            const shirtV = new THREE.Mesh(new THREE.CylinderGeometry(0.26, 0.18, 0.72, 16), shirtMat);
            shirtV.position.set(0, 2.05, 0.32);
            shirtV.rotation.x = 0.18;
            torsoGroup.add(shirtV);

            // White Shirt Collar Ring
            const collar = new THREE.Mesh(new THREE.CylinderGeometry(0.24, 0.28, 0.26, 16), shirtMat);
            collar.position.y = 2.45;
            torsoGroup.add(collar);

            // Gold School Crest / Badge on Left Chest
            const badge = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.08, 0.03, 12), goldMat);
            badge.rotation.x = Math.PI / 2;
            badge.position.set(-0.28, 2.08, 0.48);
            torsoGroup.add(badge);

            // Gold Uniform Buttons
            for (let b = 0; b < 2; b++) {
                const btn = new THREE.Mesh(new THREE.SphereGeometry(0.035, 8, 8), goldMat);
                btn.position.set(0, 1.72 + b * 0.22, 0.52);
                torsoGroup.add(btn);
            }

            player.group.add(torsoGroup);
            player.limbs.body = body;
            player.limbs.vestMat = vestMat;

            // 2. CONTOURED SCHOOL BACKPACK
            const backpackGroup = new THREE.Group();
            backpackGroup.position.set(0, 1.88, -0.52);

            // Main Pouch (Rounded cylinder scaled flat on Z)
            const packBody = new THREE.Mesh(new THREE.CylinderGeometry(0.42, 0.44, 0.95, 18), packMat);
            packBody.scale.set(1.0, 1.0, 0.65);
            packBody.castShadow = true;
            backpackGroup.add(packBody);

            // Dome Top of Backpack
            const packTop = new THREE.Mesh(new THREE.SphereGeometry(0.42, 18, 12), packMat);
            packTop.scale.set(1.0, 0.45, 0.65);
            packTop.position.y = 0.48;
            backpackGroup.add(packTop);

            // Front Pocket
            const packPock = new THREE.Mesh(new THREE.CylinderGeometry(0.3, 0.32, 0.48, 16), new THREE.MeshLambertMaterial({ color: 0xaa8338 }));
            packPock.scale.set(0.95, 1.0, 0.45);
            packPock.position.set(0, -0.15, -0.22);
            backpackGroup.add(packPock);

            // Top Grab Loop Handle
            const packHandle = new THREE.Mesh(new THREE.TorusGeometry(0.14, 0.035, 8, 14), new THREE.MeshLambertMaterial({ color: 0x6e5223 }));
            packHandle.position.set(0, 0.66, 0);
            packHandle.rotation.x = Math.PI / 2;
            backpackGroup.add(packHandle);

            player.group.add(backpackGroup);

            // 3. HEAD & CUTE EXPRESSIVE FACE
            const headGroup = new THREE.Group();
            headGroup.position.set(0, 3.08, 0);

            // Smooth Head Sphere
            const head = new THREE.Mesh(new THREE.SphereGeometry(0.66, 24, 24), skinMat);
            head.castShadow = true;
            headGroup.add(head);
            player.limbs.head = head;

            // Big Expressive Eyes with Sparkle Catchlights
            const eyeMat = new THREE.MeshBasicMaterial({ color: 0x111827 });
            const sparkleMat = new THREE.MeshBasicMaterial({ color: 0xffffff });
            const blushMat = new THREE.MeshBasicMaterial({ color: 0xfca5a5 });

            // Left Eye
            const eyeL = new THREE.Mesh(new THREE.SphereGeometry(0.08, 12, 12), eyeMat);
            eyeL.scale.set(0.9, 1.3, 0.35);
            eyeL.position.set(-0.21, 0.04, 0.6);
            headGroup.add(eyeL);

            const spkL = new THREE.Mesh(new THREE.SphereGeometry(0.026, 8, 8), sparkleMat);
            spkL.position.set(-0.19, 0.09, 0.63);
            headGroup.add(spkL);

            // Right Eye
            const eyeR = new THREE.Mesh(new THREE.SphereGeometry(0.08, 12, 12), eyeMat);
            eyeR.scale.set(0.9, 1.3, 0.35);
            eyeR.position.set(0.21, 0.04, 0.6);
            headGroup.add(eyeR);

            const spkR = new THREE.Mesh(new THREE.SphereGeometry(0.026, 8, 8), sparkleMat);
            spkR.position.set(0.23, 0.09, 0.63);
            headGroup.add(spkR);

            // Cute Blush Cheeks
            const blushL = new THREE.Mesh(new THREE.SphereGeometry(0.08, 10, 8), blushMat);
            blushL.scale.set(1.4, 0.7, 0.3);
            blushL.position.set(-0.35, -0.1, 0.54);
            headGroup.add(blushL);

            const blushR = new THREE.Mesh(new THREE.SphereGeometry(0.08, 10, 8), blushMat);
            blushR.scale.set(1.4, 0.7, 0.3);
            blushR.position.set(0.35, -0.1, 0.54);
            headGroup.add(blushR);

            // Cute Gentle Smile
            const mouthMat = new THREE.MeshBasicMaterial({ color: 0xc25e4c });
            const mouth = new THREE.Mesh(new THREE.TorusGeometry(0.065, 0.02, 6, 12, Math.PI), mouthMat);
            mouth.rotation.x = Math.PI * 0.12;
            mouth.rotation.z = Math.PI;
            mouth.position.set(0, -0.18, 0.62);
            headGroup.add(mouth);

            // Cute Ears
            const earGeo = new THREE.SphereGeometry(0.12, 10, 10);
            const earL = new THREE.Mesh(earGeo, skinMat);
            earL.scale.set(0.35, 0.8, 0.6);
            earL.position.set(-0.64, 0, 0);
            headGroup.add(earL);

            const earR = new THREE.Mesh(earGeo, skinMat);
            earR.scale.set(0.35, 0.8, 0.6);
            earR.position.set(0.64, 0, 0);
            headGroup.add(earR);

            // 4. BOY HEADGEAR: VELVET SONGKOK / PECI + HAIR FRINGE
            const peciGroup = new THREE.Group();
            
            // Peci velvet body
            const peciBody = new THREE.Mesh(new THREE.CylinderGeometry(0.63, 0.66, 0.44, 24), peciMat);
            peciBody.position.set(0, 0.46, -0.02);
            peciGroup.add(peciBody);

            // Rounded top dome of peci
            const peciTop = new THREE.Mesh(new THREE.SphereGeometry(0.63, 20, 10), peciMat);
            peciTop.scale.set(1.0, 0.22, 1.0);
            peciTop.position.set(0, 0.68, -0.02);
            peciGroup.add(peciTop);

            // Golden embroidery trim band at base of peci
            const peciTrim = new THREE.Mesh(new THREE.TorusGeometry(0.66, 0.022, 8, 24), goldMat);
            peciTrim.rotation.x = Math.PI / 2;
            peciTrim.position.set(0, 0.24, -0.02);
            peciGroup.add(peciTrim);

            // Hair Fringe / Bangs peeking out naturally under songkok
            const hairMat = new THREE.MeshLambertMaterial({ color: 0x1f1916 });
            const bangs = [
                [-0.24, 0.24, 0.58, 0.12],
                [0.0, 0.27, 0.62, 0.14],
                [0.24, 0.24, 0.58, 0.12]
            ];
            bangs.forEach(([bx, by, bz, br]) => {
                const fringe = new THREE.Mesh(new THREE.SphereGeometry(br, 10, 8), hairMat);
                fringe.position.set(bx, by, bz);
                fringe.scale.set(1.2, 0.8, 0.7);
                peciGroup.add(fringe);
            });

            headGroup.add(peciGroup);
            player.limbs.peciGroup = peciGroup;
            player.limbs.peciMat = peciMat;
            player.limbs.hat = peciBody;

            // 5. GIRL HEADGEAR: DRAPED MUSLIMAH HIJAB & BROOCH PIN
            const hijabGroup = new THREE.Group();
            hijabGroup.visible = false; // Hidden initially for default Fatih boy persona

            // Inner Hood Dome enclosing back and sides of head
            const hijabDome = new THREE.Mesh(new THREE.SphereGeometry(0.70, 24, 24), hijabMat);
            hijabDome.position.set(0, 0.04, -0.16);
            hijabGroup.add(hijabDome);

            // Draped framing cowl opening for the face
            const pCowl = new THREE.Mesh(new THREE.TorusGeometry(0.62, 0.06, 12, 32), hijabMat);
            pCowl.position.set(0, -0.01, 0.22);
            hijabGroup.add(pCowl);

            // Draped Mantle / Khimar Scarf around neck and shoulders
            const hijabMantle = new THREE.Mesh(new THREE.CylinderGeometry(0.48, 0.85, 0.85, 24), hijabMat);
            hijabMantle.position.set(0, -0.62, 0);
            hijabGroup.add(hijabMantle);

            // Hijab Golden Brooch Pin on Right Scarf Fold
            const brooch = new THREE.Mesh(new THREE.SphereGeometry(0.06, 12, 12), goldMat);
            brooch.position.set(0.38, -0.52, 0.42);
            hijabGroup.add(brooch);

            headGroup.add(hijabGroup);
            player.limbs.hijabGroup = hijabGroup;
            player.limbs.hijabMat = hijabMat;

            player.group.add(headGroup);

            // 6. ARMS WITH SHOULDER PIVOTS (ROUNDED & NATURAL SWING)
            // Left Arm Group (pivots at shoulder socket)
            const leftArmGroup = new THREE.Group();
            leftArmGroup.position.set(-0.72, 2.26, 0);

            const shoulderL = new THREE.Mesh(new THREE.SphereGeometry(0.2, 14, 14), vestMat);
            leftArmGroup.add(shoulderL);

            const leftSleeve = new THREE.Mesh(new THREE.CylinderGeometry(0.18, 0.16, 0.82, 14), vestMat);
            leftSleeve.position.y = -0.41;
            leftSleeve.castShadow = true;
            leftArmGroup.add(leftSleeve);

            const cuffL = new THREE.Mesh(new THREE.CylinderGeometry(0.17, 0.17, 0.09, 14), shirtMat);
            cuffL.position.y = -0.82;
            leftArmGroup.add(cuffL);

            const handL = new THREE.Mesh(new THREE.SphereGeometry(0.15, 14, 14), skinMat);
            handL.position.y = -0.96;
            handL.castShadow = true;
            leftArmGroup.add(handL);

            player.group.add(leftArmGroup);
            player.limbs.leftArm = leftArmGroup;
            player.limbs.leftSleeve = leftSleeve;
            player.limbs.shoulderL = shoulderL;

            // Right Arm Group (pivots at shoulder socket)
            const rightArmGroup = new THREE.Group();
            rightArmGroup.position.set(0.72, 2.26, 0);

            const shoulderR = new THREE.Mesh(new THREE.SphereGeometry(0.2, 14, 14), vestMat);
            rightArmGroup.add(shoulderR);

            const rightSleeve = new THREE.Mesh(new THREE.CylinderGeometry(0.18, 0.16, 0.82, 14), vestMat);
            rightSleeve.position.y = -0.41;
            rightSleeve.castShadow = true;
            rightArmGroup.add(rightSleeve);

            const cuffR = new THREE.Mesh(new THREE.CylinderGeometry(0.17, 0.17, 0.09, 14), shirtMat);
            cuffR.position.y = -0.82;
            rightArmGroup.add(cuffR);

            const handR = new THREE.Mesh(new THREE.SphereGeometry(0.15, 14, 14), skinMat);
            handR.position.y = -0.96;
            handR.castShadow = true;
            rightArmGroup.add(handR);

            player.group.add(rightArmGroup);
            player.limbs.rightArm = rightArmGroup;
            player.limbs.rightSleeve = rightSleeve;
            player.limbs.shoulderR = shoulderR;

            // 7. LEGS & SNEAKERS WITH HIP PIVOTS (ROUNDED & NATURAL WALK)
            // Left Leg Group (pivots at hip socket)
            const leftLegGroup = new THREE.Group();
            leftLegGroup.position.set(-0.31, 1.25, 0);

            const legL = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.17, 0.94, 14), pantsMat);
            legL.position.y = -0.47;
            legL.castShadow = true;
            leftLegGroup.add(legL);

            const hemL = new THREE.Mesh(new THREE.TorusGeometry(0.17, 0.02, 8, 14), pantsMat);
            hemL.rotation.x = Math.PI / 2;
            hemL.position.y = -0.94;
            leftLegGroup.add(hemL);

            // Left Sneaker Shoe Body
            const shoeL = new THREE.Mesh(new THREE.SphereGeometry(0.19, 14, 12), shoeMat);
            shoeL.scale.set(1.05, 0.9, 1.55);
            shoeL.position.set(0, -1.06, 0.09);
            shoeL.castShadow = true;
            leftLegGroup.add(shoeL);

            // White Rubber Sole
            const soleL = new THREE.Mesh(new THREE.CylinderGeometry(0.21, 0.23, 0.1, 14), soleMat);
            soleL.scale.set(1.0, 1.0, 1.45);
            soleL.position.set(0, -1.18, 0.09);
            leftLegGroup.add(soleL);

            // White Toe Bumper Cap
            const toeL = new THREE.Mesh(new THREE.SphereGeometry(0.11, 10, 8), soleMat);
            toeL.scale.set(1.1, 0.7, 0.7);
            toeL.position.set(0, -1.1, 0.28);
            leftLegGroup.add(toeL);

            player.group.add(leftLegGroup);
            player.limbs.leftLeg = leftLegGroup;

            // Right Leg Group (pivots at hip socket)
            const rightLegGroup = new THREE.Group();
            rightLegGroup.position.set(0.31, 1.25, 0);

            const legR = new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.17, 0.94, 14), pantsMat);
            legR.position.y = -0.47;
            legR.castShadow = true;
            rightLegGroup.add(legR);

            const hemR = new THREE.Mesh(new THREE.TorusGeometry(0.17, 0.02, 8, 14), pantsMat);
            hemR.rotation.x = Math.PI / 2;
            hemR.position.y = -0.94;
            rightLegGroup.add(hemR);

            // Right Sneaker Shoe Body
            const shoeR = new THREE.Mesh(new THREE.SphereGeometry(0.19, 14, 12), shoeMat);
            shoeR.scale.set(1.05, 0.9, 1.55);
            shoeR.position.set(0, -1.06, 0.09);
            shoeR.castShadow = true;
            rightLegGroup.add(shoeR);

            // White Rubber Sole
            const soleR = new THREE.Mesh(new THREE.CylinderGeometry(0.21, 0.23, 0.1, 14), soleMat);
            soleR.scale.set(1.0, 1.0, 1.45);
            soleR.position.set(0, -1.18, 0.09);
            rightLegGroup.add(soleR);

            // White Toe Bumper Cap
            const toeR = new THREE.Mesh(new THREE.SphereGeometry(0.11, 10, 8), soleMat);
            toeR.scale.set(1.1, 0.7, 0.7);
            toeR.position.set(0, -1.1, 0.28);
            rightLegGroup.add(toeR);

            player.group.add(rightLegGroup);
            player.limbs.rightLeg = rightLegGroup;

            player.group.position.set(player.x, 0, player.z);
            scene.add(player.group);
        }
        buildPlayer();

        // ==============================================================
        // INPUT CONTROLS (KEYBOARD & TOUCH)
        // ==============================================================
        const keys = { w: false, a: false, s: false, d: false };

        window.addEventListener('keydown', (e) => {
            const k = e.key.toLowerCase();
            if (k === 'w' || k === 'arrowup') keys.w = true;
            if (k === 's' || k === 'arrowdown') keys.s = true;
            if (k === 'a' || k === 'arrowleft') keys.a = true;
            if (k === 'd' || k === 'arrowright') keys.d = true;
            if (k === 'e' || k === 'enter') triggerActiveCheckpoint();
            if (k === 'p') openModal('persona');
        });

        window.addEventListener('keyup', (e) => {
            const k = e.key.toLowerCase();
            if (k === 'w' || k === 'arrowup') keys.w = false;
            if (k === 's' || k === 'arrowdown') keys.s = false;
            if (k === 'a' || k === 'arrowleft') keys.a = false;
            if (k === 'd' || k === 'arrowright') keys.d = false;
        });

        function bindTouch(id, keyName) {
            const el = document.getElementById(id);
            if (!el) return;
            const start = (e) => { e.preventDefault(); keys[keyName] = true; initAudio(); };
            const end = (e) => { e.preventDefault(); keys[keyName] = false; };
            el.addEventListener('touchstart', start, { passive: false });
            el.addEventListener('touchend', end, { passive: false });
            el.addEventListener('mousedown', start);
            el.addEventListener('mouseup', end);
        }
        bindTouch('btn-up', 'w');
        bindTouch('btn-down', 's');
        bindTouch('btn-left', 'a');
        bindTouch('btn-right', 'd');

        // ==============================================================
        // ==============================================================
        // MOUSE CAMERA ORBIT SYSTEM (Azimuth, Pitch, Zoom & Pointer Lock)
        // ==============================================================
        let isDragging = false;
        let isPointerLocked = false;
        let prevMouseX = 0;
        let prevMouseY = 0;

        let targetAzimuth = 0;
        let cameraAzimuth = 0;
        let targetPitch = 0.32; // initial top-down tilt angle
        let cameraPitch = 0.32;
        let targetZoom = 11.5;  // initial distance
        let cameraZoom = 11.5;

        // Dedicated smoothed camera target (Anchors both eye orbit and lookAt, eliminating camera jitter)
        const cameraTarget = new THREE.Vector3(player.x, player.y + 1.85, player.z);

        // Pointer Lock detection
        document.addEventListener('pointerlockchange', () => {
            isPointerLocked = (document.pointerLockElement === renderer.domElement || document.pointerLockElement === container);
        });

        // Click on 3D Canvas to capture cursor for seamless 360° mouse look
        renderer.domElement.addEventListener('click', (e) => {
            const modal = document.getElementById('campus-modal');
            if (modal && modal.classList.contains('open')) return;
            if (e.target.closest('#proximity-prompt') || e.target.closest('.hud-actions') || e.target.closest('.touch-controls-pad')) return;

            if (!isPointerLocked && renderer.domElement.requestPointerLock) {
                renderer.domElement.requestPointerLock();
            }
        });

        // Dragging & Mouse Move
        window.addEventListener('mousedown', (e) => {
            if (e.target.tagName !== 'BUTTON' && !e.target.closest('#campus-modal') && !e.target.closest('.hud-actions')) {
                isDragging = true;
                prevMouseX = e.clientX;
                prevMouseY = e.clientY;
            }
        });

        window.addEventListener('mouseup', () => { 
            isDragging = false; 
        });

        window.addEventListener('mousemove', (e) => {
            const modal = document.getElementById('campus-modal');
            if (modal && modal.classList.contains('open')) return;

            // 1. If Pointer is locked (Free 360 mouse look)
            if (isPointerLocked) {
                targetAzimuth -= (e.movementX || 0) * 0.0028;
                targetPitch += (e.movementY || 0) * 0.0022;
                targetPitch = Math.max(-0.10, Math.min(1.15, targetPitch));
            } 
            // 2. If user is dragging with mouse button held
            else if (isDragging) {
                const deltaX = e.clientX - prevMouseX;
                const deltaY = e.clientY - prevMouseY;
                targetAzimuth -= deltaX * 0.0040;
                targetPitch += deltaY * 0.0030;
                targetPitch = Math.max(-0.10, Math.min(1.15, targetPitch));
                prevMouseX = e.clientX;
                prevMouseY = e.clientY;
            }
            // Note: Passive hover rotation is removed to guarantee rock-solid screen stability without unwanted camera shaking.
        });

        // Mouse Wheel Zoom
        window.addEventListener('wheel', (e) => {
            if (e.target.closest('#campus-modal')) return;
            targetZoom += e.deltaY * 0.008;
            targetZoom = Math.max(5.0, Math.min(22.0, targetZoom));
        }, { passive: true });

        // Touch Drag for Mobile / Touchscreens
        let touchStartX = 0;
        let touchStartY = 0;
        window.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1 && !e.target.closest('.touch-controls-pad') && !e.target.closest('#campus-modal') && !e.target.closest('.hud-actions') && !e.target.closest('#proximity-prompt')) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        window.addEventListener('touchmove', (e) => {
            if (e.touches.length === 1 && !e.target.closest('.touch-controls-pad') && !e.target.closest('#campus-modal') && !e.target.closest('.hud-actions') && !e.target.closest('#proximity-prompt')) {
                const deltaX = e.touches[0].clientX - touchStartX;
                const deltaY = e.touches[0].clientY - touchStartY;
                targetAzimuth -= deltaX * 0.0045;
                targetPitch += deltaY * 0.0035;
                targetPitch = Math.max(-0.10, Math.min(1.15, targetPitch));
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        function toggleCameraView() {
            cameraDistanceMode = (cameraDistanceMode + 1) % 3;
            const label = document.getElementById('camera-mode-text');
            if (cameraDistanceMode === 0) {
                targetZoom = 11.5; targetPitch = 0.32;
                label.textContent = 'Kamera: TPV Normal';
            }
            if (cameraDistanceMode === 1) {
                targetZoom = 6.5; targetPitch = 0.20;
                label.textContent = 'Kamera: TPV Dekat';
            }
            if (cameraDistanceMode === 2) {
                targetZoom = 19.0; targetPitch = 0.85;
                label.textContent = 'Kamera: Bird-Eye';
            }
            playTone(480, 'sine', 0.1, 0.08);
        }

        // ==============================================================
        // ANIMATION LOOP & THIRD-PERSON FOLLOW
        // ==============================================================
        let clock = new THREE.Clock();
        let stepSoundTimer = 0;

        function animate() {
            requestAnimationFrame(animate);
            const rawDelta = clock.getDelta();
            const delta = Math.min(rawDelta, 0.06); // Protect against tab switch physics spikes
            const time = clock.getElapsedTime();

            // 1. Movement Input & Smooth Acceleration/Inertia
            let inputX = 0;
            let inputZ = 0;

            if (keys.w) inputZ -= 1;
            if (keys.s) inputZ += 1;
            if (keys.a) inputX -= 1;
            if (keys.d) inputX += 1;

            const isInputActive = (inputX !== 0 || inputZ !== 0);

            if (isInputActive) {
                // Movement direction relative to camera orbit yaw
                player.moveAngle = Math.atan2(inputX, inputZ) + cameraAzimuth;
                player.targetRotation = player.moveAngle;
            }

            // Smooth Acceleration and Deceleration (No stiffness, natural smooth glide)
            const maxSpeed = 13.5;
            const targetSpeed = isInputActive ? maxSpeed : 0;
            const accelRate = isInputActive ? 12.0 : 14.0;
            player.speed += (targetSpeed - player.speed) * Math.min(1, accelRate * delta);
            if (player.speed < 0.005) player.speed = 0;

            // Apply displacement
            if (player.speed > 0) {
                player.x += Math.sin(player.moveAngle) * player.speed * delta;
                player.z += Math.cos(player.moveAngle) * player.speed * delta;

                // Hall boundaries (disesuaikan dengan dinding barat yang telah dimajukan)
                player.x = Math.max(-18.8, Math.min(28, player.x));
                player.z = Math.max(-33, Math.min(33, player.z));
            }

            // Smooth rotation towards travel vector (frame-rate independent)
            let diffRot = player.targetRotation - player.rotation;
            while (diffRot < -Math.PI) diffRot += Math.PI * 2;
            while (diffRot > Math.PI) diffRot -= Math.PI * 2;
            player.rotation += diffRot * Math.min(1, 14 * delta);

            // 2. Smooth Organic Limb Animation & Stride
            const speedRatio = player.speed / maxSpeed;
            if (speedRatio > 0.01) {
                player.walkCycle += delta * (speedRatio * 11.5);
                stepSoundTimer += delta * (speedRatio * 3.6);
                if (stepSoundTimer > 1.0) {
                    playStepSound();
                    stepSoundTimer = 0;
                }
            } else {
                stepSoundTimer = 0.7; // Primed for first step
                // Subtle rhythmic idle breathing & posture
                const idleBreathe = Math.sin(time * 2.8) * 0.018;
                player.currentLimb.bounce += (idleBreathe - player.currentLimb.bounce) * Math.min(1, 8 * delta);
                if (player.limbs.leftArm && player.limbs.rightArm) {
                    const idleArmSway = Math.sin(time * 2.0) * 0.035;
                    player.limbs.leftArm.rotation.z = 0.05 + idleArmSway;
                    player.limbs.rightArm.rotation.z = -0.05 - idleArmSway;
                }
            }

            const targetLegSwing = Math.sin(player.walkCycle) * 0.65 * speedRatio;
            const targetArmSwing = Math.cos(player.walkCycle) * 0.65 * speedRatio;
            const targetBounce = Math.abs(Math.sin(player.walkCycle * 2)) * 0.05 * speedRatio;

            // Smooth damping for limbs so stopping never snaps
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

            // 3. Ultra-Smooth Cinematic Follow Camera (Rock-Solid & Jitter-Free)
            // A. Smooth camera angles (azimuth & pitch)
            const angleDamp = Math.min(1, 18 * delta);
            cameraAzimuth += (targetAzimuth - cameraAzimuth) * angleDamp;
            cameraPitch += (targetPitch - cameraPitch) * angleDamp;
            cameraZoom += (targetZoom - cameraZoom) * Math.min(1, 12 * delta);

            // B. Smooth camera target (follows player center with gentle damping)
            const targetDamp = Math.min(1, 12 * delta);
            cameraTarget.x += (player.x - cameraTarget.x) * targetDamp;
            cameraTarget.y += (player.y + 1.85 - cameraTarget.y) * targetDamp;
            cameraTarget.z += (player.z - cameraTarget.z) * targetDamp;

            // C. Spherical orbit anchored to cameraTarget
            const horizDist = cameraZoom * Math.cos(cameraPitch);
            const vertDist = cameraZoom * Math.sin(cameraPitch);

            const desiredCamX = cameraTarget.x + Math.sin(cameraAzimuth) * horizDist;
            const desiredCamY = cameraTarget.y + vertDist;
            const desiredCamZ = cameraTarget.z + Math.cos(cameraAzimuth) * horizDist;

            // D. Smooth camera eye position
            const camEyeDamp = Math.min(1, 12 * delta);
            camera.position.x += (desiredCamX - camera.position.x) * camEyeDamp;
            camera.position.y += (desiredCamY - camera.position.y) * camEyeDamp;
            camera.position.z += (desiredCamZ - camera.position.z) * camEyeDamp;

            // E. Look directly at the smoothed camera target
            camera.lookAt(cameraTarget.x, cameraTarget.y, cameraTarget.z);

            // 4. Ground Interaction Rings Pulse Animation
            groundRings.forEach((ring, idx) => {
                ring.scale.setScalar(1 + Math.sin(time * 2 + idx) * 0.08);
            });

            // 4B. Animate Floating Sunbeam Dust Motes / Sparkles
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

            // 5. Indoor Campus NPCs Animation
            if (typeof npcUstadz !== 'undefined' && npcUstadz) {
                const distU = Math.hypot(player.x - (-4), player.z - (-18));
                if (distU < 7.5) {
                    npcUstadz.rotation.y = Math.atan2(player.x - (-4), player.z - (-18));
                    npcUstadz.userData.rightArm.rotation.x = -1.2 + Math.sin(time * 6) * 0.4;
                    npcUstadz.userData.rightArm.rotation.z = -0.4;
                } else {
                    npcUstadz.rotation.y = 0;
                    npcUstadz.userData.rightArm.rotation.x = -0.5 + Math.sin(time * 1.5) * 0.05;
                    npcUstadz.userData.rightArm.rotation.z = -0.15;
                }
                npcUstadz.userData.head.rotation.y = Math.sin(time * 1.2) * 0.12;
            }
            if (typeof npcStudentClass !== 'undefined' && npcStudentClass) {
                npcStudentClass.rotation.y = Math.sin(time * 1.0) * 0.06;
            }
            if (typeof npcDesk !== 'undefined' && npcDesk) {
                const distD = Math.hypot(player.x - 8, player.z - 22.5);
                if (distD < 7.5) {
                    npcDesk.rotation.y = Math.atan2(player.x - 8, player.z - 18.2);
                    if (npcDesk.userData.rightArm) {
                        npcDesk.userData.rightArm.rotation.x = -1.2 + Math.sin(time * 5.5) * 0.3;
                        npcDesk.userData.rightArm.rotation.z = -0.3;
                    }
                } else {
                    npcDesk.rotation.y = Math.sin(time * 1.0) * 0.06;
                    if (npcDesk.userData.rightArm) {
                        npcDesk.userData.rightArm.rotation.x = -0.3 + Math.sin(time * 1.5) * 0.04;
                        npcDesk.userData.rightArm.rotation.z = -0.1;
                    }
                }
                if (npcDesk.userData.head) {
                    npcDesk.userData.head.rotation.y = Math.sin(time * 1.2) * 0.08;
                }
                if (npcDesk.userData.bubble) {
                    npcDesk.userData.bubble.position.y = 4.35 + Math.sin(time * 2.8) * 0.08;
                }
            }

            // 6. Proximity Detection & Exploration Recording
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

            renderer.render(scene, camera);
        }

        // ==============================================================
        // CHECKPOINT MODAL CONTROLS
        // ==============================================================
        function triggerActiveCheckpoint() {
            if (activeCheckpointKey) {
                openModal(activeCheckpointKey);
            }
        }

        function openModal(type) {
            if (document.exitPointerLock) {
                try { document.exitPointerLock(); } catch(e) {}
            }
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

        // ==============================================================
        // EXPLORATION TRACKER & ACHIEVEMENTS
        // ==============================================================
        const discoveredCheckpoints = new Set();
        let starCount = 0;

        function updateExplorationHUD() {
            const exploreText = document.getElementById('hud-explore-text');
            const starText = document.getElementById('hud-star-count');
            if (exploreText) {
                exploreText.textContent = `${discoveredCheckpoints.size}/${checkpoints.length} Dijelajahi`;
            }
            if (starText) {
                starText.textContent = starCount;
            }
        }

        function showCampusToast(message) {
            const toast = document.getElementById('campus-toast');
            if (!toast) return;
            toast.innerHTML = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3200);
        }

        function recordCheckpointDiscovery(cp) {
            if (!discoveredCheckpoints.has(cp.type)) {
                discoveredCheckpoints.add(cp.type);
                starCount++;
                updateExplorationHUD();
                playTone(784, 'triangle', 0.15, 0.12);
                setTimeout(() => playTone(1046.5, 'sine', 0.22, 0.14), 100);
                showCampusToast(`⭐ <strong>Bintang Prestasi!</strong> &bull; Menemukan ${cp.name}`);
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

            const colorHex = (typeof vestColor === 'string') ? parseInt(vestColor.replace('#', '0x')) : vestColor;

            if (player.limbs.vestMat) {
                player.limbs.vestMat.color.set(colorHex);
            } else if (player.limbs.body) {
                player.limbs.body.material.color.set(colorHex);
            }
            if (player.limbs.shoulderL) player.limbs.shoulderL.material.color.set(colorHex);
            if (player.limbs.shoulderR) player.limbs.shoulderR.material.color.set(colorHex);
            if (player.limbs.leftSleeve) player.limbs.leftSleeve.material.color.set(colorHex);
            if (player.limbs.rightSleeve) player.limbs.rightSleeve.material.color.set(colorHex);

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
            const colors = ['#2de2a6', '#f4c444', '#ff7644', '#ffffff', '#70c4ff'];
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

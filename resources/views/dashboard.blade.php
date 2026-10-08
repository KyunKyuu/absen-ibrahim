<x-layouts.app title="Dashboard Eksekutif">
    <style>
        /* ==============================================================
           EXECUTIVE DASHBOARD LUXURY STYLING
           ============================================================== */
        :root {
            --dash-primary: #10b981;
            --dash-primary-dark: #0d2e24;
            --dash-gold: #f59e0b;
            --dash-gold-light: #fef3c7;
            --dash-surface: #ffffff;
            --dash-border: rgba(16, 185, 129, 0.15);
            --dash-shadow: 0 10px 30px -10px rgba(16, 35, 28, 0.08), 0 2px 6px -1px rgba(16, 35, 28, 0.04);
            --dash-shadow-hover: 0 16px 36px -10px rgba(16, 185, 129, 0.18), 0 4px 12px -2px rgba(16, 35, 28, 0.06);
        }

        /* 1. Hero Welcome & Executive Command Header */
        .dash-hero-header {
            background: radial-gradient(120% 140% at 85% 15%, rgba(45, 226, 166, 0.18) 0%, rgba(13, 46, 36, 0.98) 55%, #071914 100%);
            border: 1.5px solid rgba(45, 226, 166, 0.28);
            border-radius: 22px;
            padding: 26px 30px;
            color: #fff;
            box-shadow: 0 20px 50px -15px rgba(6, 26, 20, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .dash-hero-header::before {
            content: '';
            position: absolute;
            right: -60px;
            bottom: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.14) 0%, transparent 70%);
            pointer-events: none;
        }
        .dash-hero-left {
            position: relative;
            z-index: 2;
            max-width: 680px;
        }
        .dash-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 4px 12px;
            border-radius: 999px;
            background: rgba(45, 226, 166, 0.15);
            border: 1px solid rgba(45, 226, 166, 0.35);
            color: #a7f3d0;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .dash-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2de2a6;
            box-shadow: 0 0 10px #2de2a6;
            animation: dashPulse 2s infinite ease-in-out;
        }
        @keyframes dashPulse { 0%, 100% { opacity: 0.4; } 50% { opacity: 1; transform: scale(1.25); } }

        .dash-hero-title {
            margin: 0 0 6px 0;
            font-size: clamp(22px, 2.8vw, 30px);
            font-weight: 850;
            letter-spacing: -0.025em;
            color: #ffffff;
            line-height: 1.2;
        }
        .dash-hero-title span {
            color: #fde047;
            text-shadow: 0 0 18px rgba(253, 224, 71, 0.35);
        }
        .dash-hero-desc {
            margin: 0;
            color: rgba(255, 255, 255, 0.78);
            font-size: 13.5px;
            line-height: 1.55;
        }
        .dash-hero-right {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
        }
        .dash-date-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.9);
            font-size: 12.5px;
            font-weight: 700;
        }
        .dash-actions-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .dash-btn-glow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }
        .dash-btn-glow.tour {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: 1.5px solid #6ee7b7;
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }
        .dash-btn-glow.tour:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }
        .dash-btn-glow.ghost {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #ffffff;
            backdrop-filter: blur(8px);
        }
        .dash-btn-glow.ghost:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }

        /* 2. Executive KPI Stat Cards */
        .dash-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }
        .dash-kpi-card {
            background: var(--dash-surface);
            border: 1px solid rgba(22, 101, 72, 0.12);
            border-radius: 18px;
            padding: 18px 20px;
            box-shadow: var(--dash-shadow);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .dash-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--dash-shadow-hover);
            border-color: rgba(16, 185, 129, 0.35);
        }
        .dash-kpi-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .dash-kpi-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dash-kpi-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 20px;
            flex-shrink: 0;
            background: #f0fdf4;
            border: 1px solid #d1fae5;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.1);
        }
        .dash-kpi-icon.gold { background: #fffbeb; border-color: #fde68a; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.12); }
        .dash-kpi-icon.blue { background: #f0f9ff; border-color: #bae6fd; box-shadow: 0 4px 10px rgba(56, 189, 248, 0.12); }
        .dash-kpi-icon.amber { background: #fff7ed; border-color: #fed7aa; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.12); }

        .dash-kpi-value {
            font-size: 28px;
            font-weight: 900;
            color: #111827;
            letter-spacing: -0.035em;
            line-height: 1;
            margin-bottom: 8px;
        }
        .dash-kpi-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-top: 4px;
            padding-top: 10px;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
            font-size: 11.5px;
            font-weight: 650;
            color: var(--muted);
        }
        .dash-kpi-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 800;
        }
        .dash-kpi-chip.emerald { background: #d1fae5; color: #065f46; }
        .dash-kpi-chip.gold { background: #fef3c7; color: #92400e; }
        .dash-kpi-chip.blue { background: #e0f2fe; color: #0369a1; }
        .dash-kpi-chip.slate { background: #f1f5f9; color: #475569; }

        /* 3. Challenge / Attendance Goal Bar */
        .dash-challenge-box {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 20px;
            align-items: center;
            padding: 20px 24px;
            border-radius: 18px;
            background: linear-gradient(135deg, #f0fdf4 0%, #e6f7ef 45%, #effaf5 100%);
            border: 1.5px solid #a7f3d0;
            box-shadow: 0 4px 20px -4px rgba(16, 185, 129, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.85);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .dash-challenge-icon {
            width: 52px;
            height: 52px;
            border-radius: 15px;
            display: grid;
            place-items: center;
            font-size: 26px;
            background: #fff;
            border: 1px solid #bbf7d0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
            flex-shrink: 0;
        }
        .dash-challenge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 99px;
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #15803d;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .dash-challenge-box h2 {
            margin: 0 0 5px;
            font-size: 18px;
            font-weight: 850;
            color: #14532d;
            letter-spacing: -0.015em;
        }
        .dash-challenge-box p {
            margin: 0;
            color: #374151;
            font-size: 13.5px;
            line-height: 1.5;
        }
        .dash-challenge-progress {
            min-width: 220px;
            background: #fff;
            padding: 12px 18px;
            border-radius: 14px;
            border: 1px solid #d1fae5;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
        }
        .dash-challenge-p-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 6px;
        }
        .dash-challenge-progress strong {
            font-size: 24px;
            font-weight: 900;
            color: #166534;
            line-height: 1;
        }
        .dash-challenge-ratio {
            font-size: 11.5px;
            font-weight: 750;
            color: #15803d;
            background: #f0fdf4;
            padding: 3px 9px;
            border-radius: 999px;
        }
        .dash-challenge-track {
            height: 10px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
            position: relative;
        }
        .dash-challenge-track span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            border-radius: 999px;
            transition: width 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.45);
        }

        /* 4. Interactive Visual Charts Row */
        .dash-charts-row {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
            gap: 18px;
            margin-bottom: 24px;
        }
        .dash-chart-card {
            background: var(--dash-surface);
            border: 1px solid rgba(22, 101, 72, 0.12);
            border-radius: 20px;
            padding: 22px 24px;
            box-shadow: var(--dash-shadow);
            position: relative;
        }
        .dash-chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f0f3f1;
        }
        .dash-chart-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 850;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.015em;
        }
        .dash-chart-sub {
            font-size: 11.5px;
            color: var(--muted);
            font-weight: 600;
        }
        .dash-chart-body {
            position: relative;
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
        }
        .dash-chart-canvas-wrap {
            position: relative;
            width: 190px;
            height: 190px;
            flex-shrink: 0;
        }
        .dash-chart-legend-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
            flex: 1;
        }
        .dash-chart-legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 6px 10px;
            border-radius: 8px;
            background: #f9fafb;
            font-size: 12px;
            transition: background 0.18s ease;
        }
        .dash-chart-legend-item:hover {
            background: #f0fdf4;
        }
        .dash-chart-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        /* 5. Hall of Fame / Superadmin Podium */
        .admin-podium-panel {
            margin-bottom: 24px;
            padding: 28px 24px 34px;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #fff;
            background: radial-gradient(110% 120% at 50% 0%, #1a4d3f 0%, #0d2e24 45%, #071914 100%);
            box-shadow: 0 20px 45px -15px rgba(5, 20, 16, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.18);
            position: relative;
            overflow: hidden;
        }
        .admin-podium-panel::before {
            content: '';
            position: absolute;
            top: -25%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 320px;
            background: radial-gradient(ellipse at center, rgba(234, 179, 8, 0.16) 0%, rgba(16, 185, 129, 0.08) 50%, transparent 70%);
            pointer-events: none;
        }
        .podium-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
            position: relative;
            z-index: 2;
        }
        .podium-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 11px;
            border-radius: 99px;
            background: rgba(253, 224, 71, 0.15);
            border: 1px solid rgba(253, 224, 71, 0.35);
            color: #fef08a;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .admin-podium-panel h2 {
            color: #fff;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 4px;
            letter-spacing: -0.02em;
        }
        .admin-podium-panel .muted {
            color: rgba(255, 255, 255, 0.72);
            font-size: 13px;
        }
        .admin-podium-filter {
            display: flex;
            align-items: center;
        }
        .admin-podium-filter label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.85);
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .podium-select-wrap {
            position: relative;
            min-width: 220px;
        }
        .podium-select-wrap select {
            appearance: none;
            width: 100%;
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 11px;
            padding: 9px 36px 9px 14px;
            font-weight: 650;
            font-size: 13px;
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
        }
        .podium-select-wrap select:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .podium-select-wrap select:focus {
            border-color: #fde047;
            box-shadow: 0 0 0 3px rgba(253, 224, 71, 0.25);
        }
        .podium-select-wrap select option {
            background: #0a231b;
            color: #fff;
        }
        .podium-select-arrow {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 11px;
        }

        .admin-podium {
            min-height: 360px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 16px;
            padding: 36px 10px 0;
            position: relative;
            z-index: 2;
        }
        .admin-podium-place {
            width: min(31%, 240px);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            animation: admin-podium-enter 0.7s both cubic-bezier(0.2, 0.8, 0.2, 1);
            transition: transform 0.3s ease;
        }
        .admin-podium-place:nth-child(2) { order: 0; animation-delay: 0.12s; }
        .admin-podium-place:nth-child(1) { order: 1; z-index: 3; }
        .admin-podium-place:nth-child(3) { order: 2; animation-delay: 0.24s; }
        .admin-podium-place:hover { transform: translateY(-4px); }

        .podium-avatar-wrap { position: relative; margin-bottom: 14px; }
        .crown-float {
            position: absolute;
            top: -28px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 32px;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.45));
            animation: crown-sway 2.6s ease-in-out infinite;
            z-index: 4;
        }
        @keyframes crown-sway {
            0%, 100% { transform: translateX(-50%) translateY(0) rotate(0deg); }
            50% { transform: translateX(-50%) translateY(-5px) rotate(2deg); }
        }

        .admin-podium-avatar {
            width: 72px;
            height: 72px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            font-size: 26px;
            font-weight: 900;
            position: relative;
            transition: transform 0.3s ease;
        }
        .admin-podium-place:hover .admin-podium-avatar { transform: scale(1.05); }
        .admin-podium-place:first-child .admin-podium-avatar {
            width: 94px;
            height: 94px;
            font-size: 36px;
            color: #5c3b06;
            background: linear-gradient(135deg, #fffbeb 0%, #fde047 30%, #f59e0b 70%, #d97706 100%);
            border: 4px solid #fef08a;
            box-shadow: 0 0 35px rgba(245, 158, 11, 0.55), 0 10px 24px rgba(0, 0, 0, 0.35), inset 0 2px 6px rgba(255, 255, 255, 0.85);
        }
        .admin-podium-place:nth-child(2) .admin-podium-avatar {
            color: #1e293b;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 40%, #94a3b8 100%);
            border: 3.5px solid #f8fafc;
            box-shadow: 0 0 22px rgba(203, 213, 225, 0.4), 0 8px 18px rgba(0, 0, 0, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.9);
        }
        .admin-podium-place:nth-child(3) .admin-podium-avatar {
            color: #451a03;
            background: linear-gradient(135deg, #ffedd5 0%, #fb923c 40%, #c2410c 100%);
            border: 3.5px solid #fed7aa;
            box-shadow: 0 0 22px rgba(234, 88, 12, 0.35), 0 8px 18px rgba(0, 0, 0, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.8);
        }

        .avatar-badge-chip {
            position: absolute;
            bottom: -6px;
            right: -6px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 900;
            border: 2px solid #0d2e24;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
        .avatar-badge-chip.gold { background: linear-gradient(135deg, #fef08a, #f59e0b); color: #5c3b06; width: 30px; height: 30px; font-size: 13px; bottom: -8px; right: -8px; }
        .avatar-badge-chip.silver { background: linear-gradient(135deg, #f8fafc, #94a3b8); color: #0f172a; }
        .avatar-badge-chip.bronze { background: linear-gradient(135deg, #ffedd5, #ea580c); color: #fff; }

        .podium-student-name {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
            margin-bottom: 3px;
        }
        .admin-podium-place:first-child .podium-student-name { font-size: 17px; }
        .podium-student-class {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: rgba(255, 255, 255, 0.85);
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .admin-podium-points {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 99px;
            font-weight: 800;
            font-size: 12px;
            margin-bottom: 12px;
        }
        .admin-podium-place:first-child .admin-podium-points {
            background: linear-gradient(135deg, rgba(254, 240, 138, 0.22) 0%, rgba(245, 158, 11, 0.2) 100%);
            border: 1px solid rgba(250, 204, 21, 0.6);
            color: #fef08a;
            font-size: 13.5px;
            padding: 5px 14px;
            box-shadow: 0 0 16px rgba(245, 158, 11, 0.25);
        }
        .admin-podium-place:nth-child(2) .admin-podium-points {
            background: rgba(241, 245, 249, 0.14);
            border: 1px solid rgba(203, 213, 225, 0.45);
            color: #f1f5f9;
        }
        .admin-podium-place:nth-child(3) .admin-podium-points {
            background: rgba(254, 215, 170, 0.14);
            border: 1px solid rgba(251, 146, 60, 0.45);
            color: #fed7aa;
        }

        .admin-podium-step {
            width: 100%;
            display: grid;
            place-items: center;
            border-radius: 14px 14px 0 0;
            position: relative;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        }
        .admin-podium-step::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.65);
            border-radius: 14px 14px 0 0;
        }
        .admin-podium-step-num { font-weight: 900; line-height: 1; letter-spacing: -0.03em; }

        .admin-podium-place:first-child .admin-podium-step {
            height: 140px;
            background: linear-gradient(180deg, #fcd34d 0%, #f59e0b 35%, #b45309 85%, #78350f 100%);
            box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.6), 0 -8px 25px rgba(245, 158, 11, 0.3), 0 14px 30px rgba(0, 0, 0, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.7);
        }
        .admin-podium-place:first-child .admin-podium-step-num {
            font-size: 54px;
            color: #ffffff;
            text-shadow: 0 3px 12px rgba(120, 53, 15, 0.75);
        }
        .admin-podium-place:nth-child(2) .admin-podium-step {
            height: 98px;
            background: linear-gradient(180deg, #f8fafc 0%, #cbd5e1 35%, #64748b 85%, #334155 100%);
            box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.7), 0 10px 22px rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.8);
        }
        .admin-podium-place:nth-child(2) .admin-podium-step-num {
            font-size: 44px;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(51, 65, 85, 0.6);
        }
        .admin-podium-place:nth-child(3) .admin-podium-step {
            height: 72px;
            background: linear-gradient(180deg, #fed7aa 0%, #ea580c 35%, #9a3412 85%, #431407 100%);
            box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.55), 0 10px 22px rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.7);
        }
        .admin-podium-place:nth-child(3) .admin-podium-step-num {
            font-size: 38px;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(67, 20, 7, 0.6);
        }

        .podium-stage-base {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.18) 20%, rgba(255, 255, 255, 0.35) 50%, rgba(255, 255, 255, 0.18) 80%, transparent 100%);
            margin-top: -2px;
            position: relative;
            z-index: 1;
        }

        .podium-runners-card {
            max-width: 760px;
            margin: 28px auto 0;
            padding: 20px 22px;
            border-radius: 16px;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }
        .podium-runners-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .podium-runners-title {
            font-size: 13px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.02em;
        }
        .podium-runners-badge {
            font-size: 11px;
            font-weight: 700;
            color: #fde047;
            background: rgba(253, 224, 71, 0.12);
            border: 1px solid rgba(253, 224, 71, 0.25);
            padding: 2px 8px;
            border-radius: 99px;
        }
        .podium-runners-list { display: grid; gap: 7px; }
        .podium-runner-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 14px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.18s ease;
        }
        .podium-runner-row:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateX(3px);
        }
        .runner-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .runner-rank-num {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .runner-avatar-mini {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #10b981, #047857);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            flex-shrink: 0;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
        }
        .runner-meta { min-width: 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .runner-name {
            color: #fff;
            font-size: 13.5px;
            font-weight: 750;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .runner-class {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            font-size: 10.5px;
            font-weight: 600;
        }
        .runner-points {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 99px;
            background: rgba(253, 224, 71, 0.1);
            border: 1px solid rgba(253, 224, 71, 0.2);
            color: #fef08a;
            font-weight: 800;
            font-size: 12.5px;
            flex-shrink: 0;
        }

        /* 6. Lower Operational Intelligence Columns */
        .dash-columns {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.8fr);
            gap: 18px;
        }
        .dash-columns .panel {
            min-width: 0;
            border-radius: 20px;
            border: 1px solid rgba(22, 101, 72, 0.12);
            box-shadow: var(--dash-shadow);
        }
        .rank-chip {
            display: inline-grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e7f4ed;
            font-weight: 850;
            color: #176548;
            font-size: 12px;
        }
        .rank-chip.gold { background: linear-gradient(135deg, #fef08a, #f59e0b); color: #5c3b06; font-weight: 900; }
        .rank-chip.silver { background: linear-gradient(135deg, #f1f5f9, #94a3b8); color: #1e293b; font-weight: 900; }
        .rank-chip.bronze { background: linear-gradient(135deg, #ffedd5, #ea580c); color: #fff; font-weight: 900; }

        .leader-name { color: #111827; text-decoration: none; font-weight: 750; font-size: 13.5px; }
        .leader-name:hover { color: #10b981; text-decoration: underline; }

        .class-progress-list { display: grid; gap: 11px; }
        .class-progress-item {
            padding: 14px 16px;
            border-radius: 12px;
            background: #f8faf9;
            border: 1px solid #eef2f0;
            transition: transform 0.18s ease;
        }
        .class-progress-item:hover { transform: translateX(3px); background: #f0fdf4; border-color: #d1fae5; }
        .class-progress-head { display: flex; justify-content: space-between; align-items: baseline; gap: 10px; margin-bottom: 8px; }
        .class-progress-track { height: 8px; border-radius: 99px; background: #e2eae5; overflow: hidden; }
        .class-progress-track span { height: 100%; display: block; background: linear-gradient(90deg, #10b981, #059669); border-radius: 99px; }

        .attention-empty {
            padding: 20px;
            border-radius: 14px;
            background: #edf7f2;
            color: #166534;
            font-size: 13px;
            font-weight: 650;
            line-height: 1.6;
            border: 1px solid #bbf7d0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Recent Activity Feed */
        .dash-feed-list { display: grid; gap: 10px; margin-top: 10px; }
        .dash-feed-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #f9fafb;
            border: 1px solid #f0f3f1;
            font-size: 12.5px;
        }
        .dash-feed-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .dash-feed-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #d1fae5;
            color: #065f46;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 11px;
            flex-shrink: 0;
        }
        .dash-feed-text strong { display: block; color: #111827; font-size: 13px; }
        .dash-feed-text small { color: var(--muted); font-size: 11px; }

        @media(max-width: 1100px) {
            .dash-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .dash-charts-row { grid-template-columns: 1fr; }
            .dash-columns { grid-template-columns: 1fr; }
        }
        @media(max-width: 650px) {
            .dash-hero-header { padding: 20px 18px; }
            .dash-hero-right { align-items: flex-start; width: 100%; }
            .dash-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dash-challenge-box { grid-template-columns: 1fr; gap: 14px; padding: 18px; }
            .dash-challenge-progress { min-width: 0; }
            .admin-podium { min-height: 290px; gap: 8px; padding-inline: 0; }
            .admin-podium-avatar { width: 56px; height: 56px; font-size: 20px; }
            .admin-podium-place:first-child .admin-podium-avatar { width: 74px; height: 74px; font-size: 28px; }
            .crown-float { top: -22px; font-size: 24px; }
            .podium-student-name { font-size: 12px; }
            .admin-podium-place:first-child .podium-student-name { font-size: 13px; }
            .podium-student-class { font-size: 9.5px; padding: 1px 7px; }
            .admin-podium-points { font-size: 10.5px; padding: 3px 8px; }
            .admin-podium-place:first-child .admin-podium-points { font-size: 11.5px; padding: 4px 10px; }
            .admin-podium-step { height: 65px; }
            .admin-podium-place:first-child .admin-podium-step { height: 110px; }
            .admin-podium-place:nth-child(3) .admin-podium-step { height: 52px; }
            .admin-podium-place:first-child .admin-podium-step-num { font-size: 42px; }
            .admin-podium-place:nth-child(2) .admin-podium-step-num { font-size: 34px; }
            .admin-podium-place:nth-child(3) .admin-podium-step-num { font-size: 30px; }
            .podium-runners-card { padding: 14px; }
            .runner-meta { flex-direction: column; align-items: flex-start; gap: 2px; }
            .dash-chart-body { flex-direction: column; }
        }
    </style>

    <!-- ==============================================================
         1. EXECUTIVE COMMAND HERO HEADER
         ============================================================== -->
    <section class="dash-hero-header">
        <div class="dash-hero-left">
            <div class="dash-status-pill">
                <span class="dash-live-dot"></span>
                Sistem Terpadu &bull; Aktif &amp; Terpantau
            </div>
            <h1 class="dash-hero-title">
                Ahlan wa Sahlan, <span>{{ $user->name }}</span>
            </h1>
            <p class="dash-hero-desc">
                {{ $attendanceSetting->school_name ?? 'Sekolah Islam Terpadu' }} &bull; Pusat monitoring absensi, rekam jejak adab, akumulasi XP karakter, dan performa akademik santri secara *real-time*.
            </p>
        </div>

        <div class="dash-hero-right">
            <div class="dash-date-badge">
                <span>🗓️</span>
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="dash-actions-toolbar">
                <a href="{{ route('landing.story') }}" target="_blank" class="dash-btn-glow tour" title="Buka Ekosistem Petualangan 3D Kampus">
                    <span>🚀</span>
                    <span>Petualangan 3D</span>
                </a>
                <a href="{{ route('landing') }}" target="_blank" class="dash-btn-glow ghost" title="Buka Website Resmi Sekolah">
                    <span>🏛️</span>
                    <span>Situs Publik</span>
                </a>
                @if($user->canDo('reports.export'))
                    <a href="{{ route('reports.attitude') }}" class="dash-btn-glow ghost" title="Unduh Rekapitulasi Raport Sikap">
                        <span>📑</span>
                        <span>Export CSV</span>
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- ==============================================================
         2. PENDING PERMIT NOTIFICATION (GURU / ADMIN)
         ============================================================== -->
    @if(($pendingPermitsCount ?? 0) > 0)
        <div style="margin-bottom:20px;padding:14px 20px;border-radius:12px;background:#fffbeb;border:1px solid #fde68a;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 12px rgba(245,158,11,0.08);">
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:24px;">🔔</span>
                <div>
                    <strong style="color:#92400e;font-size:13.5px;">Terdapat {{ $pendingPermitsCount }} Pengajuan Izin Santri Menunggu Verifikasi</strong>
                    <p style="margin:2px 0 0;font-size:12px;color:#b45309;">Santri atau wali santri telah mengajukan izin sakit/berhalangan yang memerlukan verifikasi pihak sekolah.</p>
                </div>
            </div>
            <a class="btn primary small" href="{{ route('attendance.permissions.index', ['status' => 'pending']) }}" style="background:#f59e0b;border-color:#d97706;font-weight:750;white-space:nowrap;">
                Periksa Sekarang &rarr;
            </a>
        </div>
    @endif

    <!-- ==============================================================
         3. STUDENT / PARENT PROGRESS FILTERS
         ============================================================== -->
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

    <!-- ==============================================================
         4. ROLE-SPECIFIC VIEWS
         ============================================================== -->
    @if($user->role === 'student')
        <div class="stack">
            <section class="panel">
                <div class="topbar" style="margin-bottom:12px">
                    <div><span class="eyebrow">Informasi akademik</span><h2 style="margin:4px 0">Tugas & ulangan harian</h2><p class="muted" style="margin:0">Jadwal dari guru untuk kelas Anda.</p></div>
                    <span class="badge">{{ $announcedAssessments->count() }} jadwal</span>
                </div>
                <div class="activity-list">
                    @forelse($announcedAssessments as $assessment)
                        <div class="activity-row">
                            <span><strong>{{ $assessment->title }}</strong><small>{{ $assessment->subject?->name ?? 'Mata pelajaran' }} · {{ $assessment->teacher?->name ?? 'Guru' }}</small></span>
                            <span class="badge">{{ \App\Models\GradeAssessment::KINDS[$assessment->kind] ?? ucfirst($assessment->kind) }} · {{ $assessment->assessed_on?->format('d M Y') }}</span>
                        </div>
                    @empty
                        <div class="muted">Belum ada tugas atau ulangan yang dijadwalkan.</div>
                    @endforelse
                </div>
            </section>
            <section class="panel admin-podium-panel">
                <div class="podium-header">
                    <div>
                        <span class="podium-eyebrow">🏆 Peringkat kelas</span>
                        <h2>Podium siswa rajin &amp; teladan</h2>
                        <span class="muted">{{ $podiumClassLabel ? 'Kelas '.$podiumClassLabel.' · ' : '' }}berdasarkan poin positif</span>
                    </div>
                </div>
                @if($podium->isNotEmpty())
                    <div class="admin-podium" aria-label="Podium siswa di kelas {{ $podiumClassLabel }}">
                        @foreach($podium->take(3) as $summary)
                            <article class="admin-podium-place">
                                <div class="podium-avatar-wrap">
                                    @if($loop->iteration === 1)<div class="crown-float" aria-hidden="true">👑</div>@endif
                                    <div class="admin-podium-avatar" aria-hidden="true">{{ mb_substr($summary->student?->name ?? '?', 0, 1) }}</div>
                                    <div class="avatar-badge-chip {{ $loop->iteration === 1 ? 'gold' : ($loop->iteration === 2 ? 'silver' : 'bronze') }}" aria-hidden="true">{{ $loop->iteration === 1 ? '🥇' : ($loop->iteration === 2 ? '🥈' : '🥉') }}</div>
                                </div>
                                <strong class="podium-student-name">{{ $summary->student?->name }}</strong>
                                <small class="podium-student-class">{{ $summary->student?->studentProfile?->schoolClass?->name }}</small>
                                <span class="admin-podium-points">{{ number_format($summary->general_points) }} poin</span>
                                <div class="admin-podium-step" aria-label="Peringkat {{ $loop->iteration }}"><span class="admin-podium-step-num">{{ $loop->iteration }}</span></div>
                            </article>
                        @endforeach
                    </div>
                    <div class="podium-stage-base" aria-hidden="true"></div>
                @else
                    <div class="muted" style="padding:28px 20px;text-align:center">Belum ada poin positif yang tercatat di kelas Anda.</div>
                @endif
            </section>
            <section class="panel" style="display:flex;align-items:center;justify-content:space-between;gap:18px;padding:20px;background:linear-gradient(135deg,#e7f5ed,#fff);border-color:#b9dfca;">
                <div><span class="eyebrow">Absensi harian</span><h2 style="margin:5px 0 3px">Siap melakukan absensi?</h2><p class="muted" style="margin:0">Buka halaman absensi khusus agar proses GPS lebih mudah dari HP.</p></div>
                <a class="btn primary" href="{{ route('student.attendance') }}">Buka absensi →</a>
            </section>
            <x-student-progress :student="$user" :progress="$progress->get($user->id)" />
            <x-student-report :student="$user" :grades="$reportGrades->get($user->id, collect())" />
        </div>
    @elseif($user->role === 'parent')
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;padding:16px 20px;border-radius:14px;background:#f0fdf4;border:1px solid #bbf7d0;box-shadow:0 2px 10px rgba(16,185,129,0.05);">
            <div>
                <strong style="color:#166534;font-size:14px;">📋 Layanan Izin &amp; Sakit Anak</strong>
                <p style="margin:2px 0 0;font-size:12px;color:#15803d;">Jika putra/putri Anda berhalangan hadir (sakit atau ada keperluan penting), ajukan izin secara online dengan mudah dan cepat.</p>
            </div>
            <a class="btn primary small" href="{{ route('attendance.permissions.index') }}" style="background:#10b981;border-color:#10b981;font-weight:750;white-space:nowrap;">
                📝 Ajukan Izin Anak &rarr;
            </a>
        </div>
        <div class="stack progress-list">
            @forelse($children as $child)
                <x-student-progress :student="$child" :progress="$progress->get($child->id)" />
                <x-student-report :student="$child" :grades="$reportGrades->get($child->id, collect())" />
            @empty
                <section class="panel muted">Belum ada data anak yang terhubung ke akun ini.</section>
            @endforelse
        </div>
    @else
        <!-- ==============================================================
             4. EXECUTIVE KPI STAT CARDS (SUPERADMIN, GURU & TATA USAHA)
             ============================================================== -->
        <div class="dash-kpi-grid">
            <!-- Metric 1: Siswa Aktif -->
            <div class="dash-kpi-card">
                <div class="dash-kpi-head">
                    <span class="dash-kpi-label">Siswa Aktif</span>
                    <div class="dash-kpi-icon">👨‍🎓</div>
                </div>
                <div class="dash-kpi-value">{{ $stats['students'] }}</div>
                <div class="dash-kpi-foot">
                    <span>{{ $classProgress->count() }} Rombel Kelas</span>
                    <span class="dash-kpi-chip emerald">Terdaftar</span>
                </div>
            </div>

            <!-- Metric 2: Kehadiran Hari Ini -->
            <div class="dash-kpi-card">
                <div class="dash-kpi-head">
                    <span class="dash-kpi-label">Presensi Hari Ini</span>
                    <div class="dash-kpi-icon">🕒</div>
                </div>
                <div class="dash-kpi-value">{{ $stats['todayAttendance'] }} <small style="font-size:14px;color:var(--muted);font-weight:600;">/ {{ $stats['students'] }}</small></div>
                <div class="dash-kpi-foot">
                    <span>{{ $stats['checkInRate'] }}% Kehadiran</span>
                    <span class="dash-kpi-chip {{ $stats['checkInRate'] >= 80 ? 'emerald' : 'gold' }}">{{ $stats['checkInRate'] >= 80 ? 'Optimal' : 'Berjalan' }}</span>
                </div>
            </div>

            <!-- Metric 3: Total XP Karakter -->
            <div class="dash-kpi-card">
                <div class="dash-kpi-head">
                    <span class="dash-kpi-label">Total XP Siswa</span>
                    <div class="dash-kpi-icon gold">⭐</div>
                </div>
                <div class="dash-kpi-value">{{ number_format($stats['totalPoints']) }}</div>
                <div class="dash-kpi-foot">
                    <span>Rata-rata {{ $stats['students'] > 0 ? (int) round($stats['totalPoints'] / $stats['students']) : 0 }} XP</span>
                    <span class="dash-kpi-chip gold">Akumulasi</span>
                </div>
            </div>

            <!-- Metric 4: Pendidik & Wali -->
            <div class="dash-kpi-card">
                <div class="dash-kpi-head">
                    <span class="dash-kpi-label">Dewan Guru</span>
                    <div class="dash-kpi-icon blue">👨‍🏫</div>
                </div>
                <div class="dash-kpi-value">{{ $stats['teachers'] }}</div>
                <div class="dash-kpi-foot">
                    <span>{{ $stats['parents'] }} Wali Santri</span>
                    <span class="dash-kpi-chip blue">Terhubung</span>
                </div>
            </div>

            <!-- Metric 5: Radar Perhatian -->
            <div class="dash-kpi-card">
                <div class="dash-kpi-head">
                    <span class="dash-kpi-label">Perlu Pendampingan</span>
                    <div class="dash-kpi-icon amber">🎯</div>
                </div>
                <div class="dash-kpi-value" style="{{ $stats['priority'] > 0 ? 'color:#dc2626;' : '' }}">{{ $stats['priority'] }}</div>
                <div class="dash-kpi-foot">
                    <span>{{ $stats['priority'] > 0 ? 'Siswa prioritas guru' : 'Status kondusif' }}</span>
                    <span class="dash-kpi-chip {{ $stats['priority'] > 0 ? 'amber' : 'emerald' }}">{{ $stats['priority'] > 0 ? 'Perhatian' : 'Aman' }}</span>
                </div>
            </div>
        </div>

        <!-- ==============================================================
             5. CHALLENGE / DAILY CHECK-IN MONITOR
             ============================================================== -->
        <section class="dash-challenge-box">
            <div class="dash-challenge-icon" aria-hidden="true">🎯</div>
            <div>
                <span class="dash-challenge-tag"><span class="dash-live-dot" style="display:inline-block;"></span> TARGET PRESENSI HARIAN</span>
                <h2>Pencapaian Check-in Siswa Hari Ini</h2>
                <p>
                    <strong style="color:#15803d;font-weight:800;">{{ $stats['todayAttendance'] }}</strong> dari <strong style="color:#15803d;font-weight:800;">{{ $stats['students'] }}</strong> siswa telah menyelesaikan presensi harian.
                    <span style="color:#6b7280;font-size:12.5px;font-style:italic;margin-left:4px;">Kedisiplinan dibangun dari pembiasaan waktu fajar.</span>
                </p>
            </div>
            <div class="dash-challenge-progress">
                <div class="dash-challenge-p-head">
                    <strong>{{ $stats['checkInRate'] }}%</strong>
                    <span class="dash-challenge-ratio">{{ $stats['todayAttendance'] }}/{{ $stats['students'] }} Siswa</span>
                </div>
                <div class="dash-challenge-track">
                    <span style="width:{{ $stats['checkInRate'] }}%"></span>
                </div>
            </div>
        </section>

        <!-- ==============================================================
             6. INTERACTIVE VISUAL CHARTS SECTION (WAH CLIENT EFFECT)
             ============================================================== -->
        <section class="dash-charts-row">
            <!-- Chart Card 1: Presensi Hari Ini -->
            <div class="dash-chart-card">
                <div class="dash-chart-header">
                    <div>
                        <h3><span>📊</span> Komposisi Kehadiran Hari Ini</h3>
                        <span class="dash-chart-sub">Status absensi siswa secara real-time</span>
                    </div>
                    <span class="dash-kpi-chip emerald">{{ $stats['checkInRate'] }}% Tercatat</span>
                </div>
                <div class="dash-chart-body">
                    <div class="dash-chart-canvas-wrap">
                        <canvas id="attendanceDoughnutChart"></canvas>
                    </div>
                    <div class="dash-chart-legend-list">
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#10b981;"></span>
                                <strong>Hadir Tepat Waktu</strong>
                            </span>
                            <span style="font-weight:800;color:#065f46;">{{ $attendanceBreakdown['ontime'] }} Siswa</span>
                        </div>
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#f59e0b;"></span>
                                <strong>Terlambat</strong>
                            </span>
                            <span style="font-weight:800;color:#92400e;">{{ $attendanceBreakdown['late'] }} Siswa</span>
                        </div>
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#38bdf8;"></span>
                                <strong>Izin / Sakit</strong>
                            </span>
                            <span style="font-weight:800;color:#0369a1;">{{ $attendanceBreakdown['excused'] }} Siswa</span>
                        </div>
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#cbd5e1;"></span>
                                <strong>Belum Check-In</strong>
                            </span>
                            <span style="font-weight:800;color:#64748b;">{{ $attendanceBreakdown['unrecorded'] }} Siswa</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Card 2: Distribusi Level Karakter -->
            <div class="dash-chart-card">
                <div class="dash-chart-header">
                    <div>
                        <h3><span>🌟</span> Distribusi Karakter &amp; Level Santri</h3>
                        <span class="dash-chart-sub">Berdasarkan total perolehan XP adab &amp; prestasi</span>
                    </div>
                    <span class="dash-kpi-chip gold">{{ number_format($stats['totalPoints']) }} Total XP</span>
                </div>
                <div class="dash-chart-body">
                    <div class="dash-chart-canvas-wrap">
                        <canvas id="characterLevelChart"></canvas>
                    </div>
                    <div class="dash-chart-legend-list">
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#f59e0b;"></span>
                                <strong>Level Teladan (≥100 XP)</strong>
                            </span>
                            <span style="font-weight:800;color:#b45309;">{{ $levelDistribution['teladan'] }} Santri</span>
                        </div>
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#10b981;"></span>
                                <strong>Level Berkembang (50-99 XP)</strong>
                            </span>
                            <span style="font-weight:800;color:#047857;">{{ $levelDistribution['berkembang'] }} Santri</span>
                        </div>
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#34d399;"></span>
                                <strong>Level Pemula (0-49 XP)</strong>
                            </span>
                            <span style="font-weight:800;color:#0f766e;">{{ $levelDistribution['pemula'] }} Santri</span>
                        </div>
                        <div class="dash-chart-legend-item">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span class="dash-chart-dot" style="background:#ef4444;"></span>
                                <strong>Perlu Bimbingan (&lt;0 XP)</strong>
                            </span>
                            <span style="font-weight:800;color:#b91c1c;">{{ $levelDistribution['perhatian'] }} Santri</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==============================================================
             7. HALL OF FAME / SUPERADMIN PODIUM JUARA
             ============================================================== -->
        @if($user->isRole('superadmin'))
            <section class="panel admin-podium-panel">
                <div class="podium-header">
                    <div>
                        <span class="podium-eyebrow">✨ Leaderboard Siswa &bull; Bintang Prestasi</span>
                        <h2>🏆 Podium siswa rajin &amp; teladan</h2>
                        <span class="muted">Peringkat berdasarkan akumulasi poin periode kelas aktif</span>
                    </div>
                    <form class="admin-podium-filter" method="get" action="{{ route('dashboard') }}">
                        <label>
                            Filter Kelas
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
                                <span class="podium-runners-badge">{{ $podium->count() - 3 }} siswa berprestasi</span>
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
                                            {{ number_format($summary->general_points) }} XP
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

        <!-- ==============================================================
             8. OPERATIONAL INTELLIGENCE COLUMNS
             ============================================================== -->
        <div class="dash-columns">
            <!-- Left: Top 10 Papan Apresiasi -->
            <section class="panel">
                <div class="topbar">
                    <div>
                        <h2 style="font-size:18px;font-weight:850;display:flex;align-items:center;gap:8px;">
                            <span>🏆</span> Papan Apresiasi Santri
                        </h2>
                        <span class="muted">10 Siswa dengan akumulasi XP tertinggi</span>
                    </div>
                    <a class="btn small" href="{{ route('students.index') }}" style="border-radius:9px;font-weight:700;">Semua Siswa &rarr;</a>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:48px;">Rank</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Total XP</th>
                                <th>Level Karakter</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($leaderboard as $row)
                            @php
                                $pts = (int) ($row->general_points ?? 0);
                                $lvlClass = match (true) {
                                    $pts >= 100 => 'background:#fef3c7;color:#92400e;border:1px solid #fde68a;',
                                    $pts >= 50 => 'background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;',
                                    $pts >= 0 => 'background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;',
                                    default => 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;',
                                };
                                $lvlText = match (true) {
                                    $pts >= 100 => '🥇 Teladan',
                                    $pts >= 50 => '🥈 Berkembang',
                                    $pts >= 0 => '🥉 Pemula',
                                    default => '⚠️ Bimbingan',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="rank-chip {{ $loop->iteration === 1 ? 'gold' : ($loop->iteration === 2 ? 'silver' : ($loop->iteration === 3 ? 'bronze' : '')) }}">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>
                                <td>
                                    <a class="leader-name" href="{{ $row->student ? route('students.show', $row->student) : '#' }}">
                                        {{ $row->student?->name }}
                                    </a>
                                    <br>
                                    <small style="color:var(--muted);font-size:11px;">{{ $row->label ?: 'Santri Aktif' }}</small>
                                </td>
                                <td>
                                    <span class="badge" style="background:#f1f5f9;color:#334155;font-weight:700;">
                                        {{ $row->student?->studentProfile?->schoolClass?->name ?? 'Belum ada kelas' }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="font-size:15px;color:#10b981;">{{ number_format($row->general_points) }}</strong>
                                </td>
                                <td>
                                    <span class="badge" style="{{ $lvlClass }}">
                                        {{ $lvlText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted" style="text-align:center;padding:30px;">Belum ada catatan poin siswa.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Right: Class Progress + Attention Radar + Recent Feed -->
            <div class="stack">
                <!-- Card 1: Perkembangan Kelas -->
                <section class="panel">
                    <div class="topbar">
                        <div>
                            <h2 style="font-size:17px;font-weight:850;display:flex;align-items:center;gap:8px;">
                                <span>📊</span> Performa Poin Kelas
                            </h2>
                            <span class="muted">Rata-rata akumulasi XP per siswa</span>
                        </div>
                    </div>
                    <div class="class-progress-list">
                        @forelse($classProgress->take(6) as $class)
                            <div class="class-progress-item">
                                <div class="class-progress-head">
                                    <strong style="color:#111827;font-size:14px;">{{ $class->name }}</strong>
                                    <span class="muted" style="font-size:12px;">{{ $class->active_students }} siswa &bull; <strong style="color:#10b981;">{{ $class->average_points }} XP</strong></span>
                                </div>
                                <div class="class-progress-track">
                                    <span style="width:{{ min(100, max(0, $class->average_points)) }}%"></span>
                                </div>
                            </div>
                        @empty
                            <div class="muted" style="text-align:center;padding:20px;">Belum ada data kelas berjalan.</div>
                        @endforelse
                    </div>
                </section>

                <!-- Card 2: Radar Pendampingan Siswa -->
                <section class="panel">
                    <div class="topbar">
                        <div>
                            <h2 style="font-size:17px;font-weight:850;display:flex;align-items:center;gap:8px;">
                                <span>🎯</span> Radar Pendampingan Guru
                            </h2>
                            <span class="muted">Siswa dengan catatan poin di bawah nol</span>
                        </div>
                    </div>
                    @php
                        $priorityStudents = $students->filter(fn($s) => ($s->pointSummary?->general_points ?? 0) < 0)->take(6);
                    @endphp
                    @if($priorityStudents->isNotEmpty())
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Poin</th>
                                    <th>Status Bimbingan</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($priorityStudents as $student)
                                <tr>
                                    <td>
                                        <a class="leader-name" href="{{ route('students.show', $student) }}">{{ $student->name }}</a>
                                    </td>
                                    <td><strong style="color:#dc2626;">{{ $student->pointSummary?->general_points }}</strong></td>
                                    <td><span class="badge priority">{{ $student->pointSummary?->label }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="attention-empty">
                            <span style="font-size:24px;">🌱</span>
                            <div>
                                <strong>Alhamdulillah, Seluruh Siswa Kondusif!</strong>
                                <div style="font-size:12px;color:#15803d;margin-top:2px;">Tidak ada santri yang memiliki poin negatif saat ini. Terus jaga pembiasaan adab dan motivasi positif.</div>
                            </div>
                        </div>
                    @endif
                </section>

                <!-- Card 3: Aktivitas & Prestasi Terkini -->
                @if($recentActivityFeed->isNotEmpty())
                    <section class="panel">
                        <div class="topbar">
                            <div>
                                <h2 style="font-size:17px;font-weight:850;display:flex;align-items:center;gap:8px;">
                                    <span>⚡</span> Catatan Prestasi &amp; Poin Terkini
                                </h2>
                                <span class="muted">Aktivitas pembiasaan adab &amp; pencatatan terbaru</span>
                            </div>
                        </div>
                        <div class="dash-feed-list">
                            @foreach($recentActivityFeed as $act)
                                <div class="dash-feed-item">
                                    <div class="dash-feed-left">
                                        <div class="dash-feed-avatar" style="{{ $act->points < 0 ? 'background:#fee2e2;color:#991b1b;' : '' }}">
                                            {{ $act->points > 0 ? '+' : '' }}{{ $act->points }}
                                        </div>
                                        <div class="dash-feed-text">
                                            <strong>{{ $act->student?->name ?? 'Siswa' }}</strong>
                                            <small>{{ $act->description ?: 'Pencatatan perilaku' }} &bull; {{ $act->created_at?->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <span class="badge" style="{{ $act->points > 0 ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;' }}">
                                        {{ $act->type }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    @endif

    <!-- ==============================================================
         CHART.JS DATA INTEGRATION SCRIPT
         ============================================================== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Doughnut Chart: Komposisi Presensi Siswa Hari Ini
            const attCanvas = document.getElementById('attendanceDoughnutChart');
            if (attCanvas) {
                const attCtx = attCanvas.getContext('2d');
                const ontime = {{ $attendanceBreakdown['ontime'] ?? 0 }};
                const late = {{ $attendanceBreakdown['late'] ?? 0 }};
                const excused = {{ $attendanceBreakdown['excused'] ?? 0 }};
                const unrecorded = {{ $attendanceBreakdown['unrecorded'] ?? 0 }};
                const total = {{ $attendanceBreakdown['total'] ?? 0 }};

                // Fallback for visual demonstration if database has no records yet
                const dataValues = (total === 0) ? [1, 0, 0, 0] : [ontime, late, excused, unrecorded];

                new Chart(attCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Tepat Waktu', 'Terlambat', 'Izin / Sakit', 'Belum Check-In'],
                        datasets: [{
                            data: dataValues,
                            backgroundColor: ['#10b981', '#f59e0b', '#38bdf8', '#cbd5e1'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.raw + ' Siswa';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1200
                        }
                    }
                });
            }

            // 2. Doughnut Chart: Distribusi Level Karakter & Akhlak
            const charCanvas = document.getElementById('characterLevelChart');
            if (charCanvas) {
                const charCtx = charCanvas.getContext('2d');
                const teladan = {{ $levelDistribution['teladan'] ?? 0 }};
                const berkembang = {{ $levelDistribution['berkembang'] ?? 0 }};
                const pemula = {{ $levelDistribution['pemula'] ?? 0 }};
                const perhatian = {{ $levelDistribution['perhatian'] ?? 0 }};
                const sumLevel = teladan + berkembang + pemula + perhatian;

                const levelValues = (sumLevel === 0) ? [1, 0, 0, 0] : [teladan, berkembang, pemula, perhatian];

                new Chart(charCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Level Teladan', 'Level Berkembang', 'Level Pemula', 'Perlu Bimbingan'],
                        datasets: [{
                            data: levelValues,
                            backgroundColor: ['#f59e0b', '#10b981', '#34d399', '#ef4444'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.raw + ' Santri';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1200
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.app>

<x-layouts.app title="Pengajuan Izin & Sakit">
    <style>
        .permit-grid {
            display: grid;
            grid-template-columns: minmax(520px, 1.2fr) minmax(360px, .8fr);
            gap: 24px;
            align-items: start;
        }
        @media(max-width: 1200px) {
            .permit-grid { grid-template-columns: 1fr; }
        }

        /* Hero / Header Stats */
        .permit-stats-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .permit-stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            text-decoration: none;
            transition: all .15s ease;
        }
        .permit-stat-chip:hover {
            border-color: #94a3b8;
            transform: translateY(-1px);
        }
        .permit-stat-chip.active {
            background: #0f3d30;
            color: #fff;
            border-color: #0f3d30;
        }
        .permit-stat-chip .count-badge {
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            background: rgba(0, 0, 0, 0.08);
        }
        .permit-stat-chip.active .count-badge {
            background: rgba(255, 255, 255, 0.22);
            color: #fff;
        }

        /* Form Card */
        .permit-form-card {
            border: 1px solid #d1fae5;
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fafffc 100%);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.06);
            padding: 24px;
        }
        .permit-form-head {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .permit-form-head h2 {
            font-size: 18px;
            margin: 0 0 6px;
            color: #0f3d30;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .permit-form-head p {
            margin: 0;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        /* Type Radio Tiles */
        .permit-type-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 6px;
        }
        .permit-type-option {
            position: relative;
            cursor: pointer;
        }
        .permit-type-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .permit-type-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 14px 8px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: #fff;
            transition: all .16s ease;
        }
        .permit-type-tile .type-icon {
            font-size: 24px;
            margin-bottom: 6px;
        }
        .permit-type-tile strong {
            font-size: 12px;
            color: #1e293b;
        }
        .permit-type-tile small {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .permit-type-option input:checked + .permit-type-tile {
            border-color: #10b981;
            background: #f0fdf4;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        .permit-type-option input:checked + .permit-type-tile strong {
            color: #065f46;
        }

        /* Child Selector for Parents */
        .child-selector-box {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 6px;
        }
        .child-radio-option {
            position: relative;
            cursor: pointer;
            flex: 1 1 calc(50% - 6px);
            min-width: 140px;
        }
        .child-radio-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .child-radio-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 11px;
            border: 2px solid #e2e8f0;
            background: #fff;
            transition: all .15s ease;
        }
        .child-radio-card .child-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #e2e8f0;
            color: #334155;
            font-weight: 800;
            display: grid;
            place-items: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .child-radio-option input:checked + .child-radio-card {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .child-radio-option input:checked + .child-radio-card .child-avatar {
            background: #10b981;
            color: #fff;
        }

        /* Preset Date Chips */
        .preset-dates-bar {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }
        .btn-preset {
            padding: 5px 11px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            background: #fff;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            cursor: pointer;
            transition: all .15s ease;
        }
        .btn-preset:hover {
            border-color: #10b981;
            color: #065f46;
            background: #f0fdf4;
        }
        .dates-summary-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 750;
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            margin-top: 4px;
        }

        /* Dropzone Upload */
        .file-upload-box {
            position: relative;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all .16s ease;
        }
        .file-upload-box:hover {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .file-upload-box input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        /* History Cards */
        .permit-item {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            padding: 18px 20px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .permit-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .permit-item.pending {
            border-left: 4px solid #f59e0b;
        }
        .permit-item.approved {
            border-left: 4px solid #10b981;
        }
        .permit-item.rejected {
            border-left: 4px solid #ef4444;
        }
        .permit-item.cancelled {
            border-left: 4px solid #94a3b8;
            opacity: 0.75;
        }

        .permit-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }
        .permit-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 9px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 800;
        }
        .permit-type-badge.sick { background: #fee2e2; color: #991b1b; }
        .permit-type-badge.excused { background: #e0f2fe; color: #0369a1; }
        .permit-type-badge.other { background: #f1f5f9; color: #334155; }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }
        .status-pill.pending { background: #fef3c7; color: #92400e; }
        .status-pill.approved { background: #d1fae5; color: #065f46; }
        .status-pill.rejected { background: #fee2e2; color: #991b1b; }
        .status-pill.cancelled { background: #f1f5f9; color: #64748b; }

        .permit-dates-line {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .permit-reason {
            font-size: 13px;
            line-height: 1.55;
            color: #334155;
            margin: 8px 0;
            padding: 10px 14px;
            border-radius: 9px;
            background: #f8fafc;
            border-left: 3px solid #cbd5e1;
        }
        .permit-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
            font-size: 11.5px;
            color: var(--muted);
        }
        .permit-review-note {
            display: block;
            margin-top: 6px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 11.5px;
            background: #f1f5f9;
            color: #475569;
        }
        .review-action-box {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 8px;
        }
    </style>

    <div class="page-heading">
        <div class="page-heading-copy">
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>/</span>
                <span>Pengajuan Izin &amp; Sakit</span>
            </div>
            <h1>📋 Pengajuan Izin &amp; Sakit Santri</h1>
            <p class="muted">Layanan mandiri bagi santri dan wali santri untuk mengajukan izin sakit atau dispensasi secara cepat, transparan, dan terverifikasi.</p>
        </div>
        @if($isAdmin || ($isTeacher && $user->isHomeroomTeacher()))
            <div class="actions">
                <a class="btn" href="{{ route('attendance.index') }}">← Rekap Absensi</a>
            </div>
        @endif
    </div>

    <!-- Summary Filter Chips -->
    <div class="permit-stats-bar">
        <a class="permit-stat-chip {{ $statusFilter === 'all' ? 'active' : '' }}" href="{{ route('attendance.permissions.index', array_merge($filters, ['status' => 'all'])) }}">
            Semua Pengajuan
            <span class="count-badge">{{ $counts['all'] }}</span>
        </a>
        <a class="permit-stat-chip {{ $statusFilter === 'pending' ? 'active' : '' }}" href="{{ route('attendance.permissions.index', array_merge($filters, ['status' => 'pending'])) }}" style="{{ $counts['pending'] > 0 ? 'border-color:#f59e0b;color:#b45309;' : '' }}">
            ⏳ Menunggu Konfirmasi
            <span class="count-badge" style="{{ $counts['pending'] > 0 ? 'background:#fef3c7;color:#92400e;' : '' }}">{{ $counts['pending'] }}</span>
        </a>
        <a class="permit-stat-chip {{ $statusFilter === 'approved' ? 'active' : '' }}" href="{{ route('attendance.permissions.index', array_merge($filters, ['status' => 'approved'])) }}">
            ✅ Disetujui
            <span class="count-badge">{{ $counts['approved'] }}</span>
        </a>
        <a class="permit-stat-chip {{ $statusFilter === 'rejected' ? 'active' : '' }}" href="{{ route('attendance.permissions.index', array_merge($filters, ['status' => 'rejected'])) }}">
            ❌ Ditolak
            <span class="count-badge">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    <div class="permit-grid">
        <!-- ==============================================================
             LEFT COLUMN: FORMULIR PENGAJUAN IZIN (STREAMLINED UX)
             ============================================================== -->
        <div class="permit-form-card">
            <div class="permit-form-head">
                <h2>✨ Formulir Izin Baru</h2>
                <p>Silakan isi jenis izin, tanggal berhalangan, dan keterangan jelas agar dapat segera diverifikasi oleh wali kelas.</p>
            </div>

            <form class="stack" method="post" action="{{ route('attendance.permissions.store') }}" enctype="multipart/form-data" id="permit-form">
                @csrf

                <!-- 1. Siapa yang izin? (Untuk Orang Tua / Admin) -->
                @if($isParent)
                    <div>
                        <label>Pilih Anak yang Izin <span style="color:#ef4444;">*</span></label>
                        <div class="child-selector-box">
                            @foreach($children as $index => $child)
                                <label class="child-radio-option">
                                    <input type="radio" name="student_user_id" value="{{ $child->id }}" @checked(old('student_user_id', $filters['student_id'] ?? ($loop->first ? $child->id : '')) == $child->id) required>
                                    <div class="child-radio-card">
                                        <div class="child-avatar">{{ mb_substr($child->name, 0, 1) }}</div>
                                        <div>
                                            <strong style="display:block;font-size:12px;color:#1e293b;">{{ $child->name }}</strong>
                                            <small style="font-size:10.5px;color:var(--muted);">{{ $child->studentProfile?->schoolClass?->name ?? 'Belum ada kelas' }}</small>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @elseif($isAdmin || ($isTeacher && $user->isHomeroomTeacher()))
                    <div>
                        <label for="student_user_id">Pilih Siswa / Santri <span style="color:#ef4444;">*</span></label>
                        <select name="student_user_id" id="student_user_id" required>
                            <option value="">-- Pilih Nama Siswa --</option>
                            @foreach($studentsToApply as $st)
                                <option value="{{ $st->id }}" @selected(old('student_user_id') == $st->id)>
                                    {{ $st->name }} ({{ $st->studentProfile?->schoolClass?->name ?? 'Tanpa Kelas' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    {{-- Siswa login sendiri --}}
                    <input type="hidden" name="student_user_id" value="{{ $user->id }}">
                    <div style="padding:10px 14px;background:#f0fdf4;border:1px solid #d1fae5;border-radius:10px;font-size:12px;color:#065f46;">
                        👤 Mengajukan atas nama: <strong>{{ $user->name }}</strong> ({{ $user->studentProfile?->schoolClass?->name ?? 'Santri' }})
                    </div>
                @endif

                <!-- 2. Jenis Izin -->
                <div>
                    <label>Jenis Pengajuan <span style="color:#ef4444;">*</span></label>
                    <div class="permit-type-grid">
                        <label class="permit-type-option">
                            <input type="radio" name="type" value="sick" @checked(old('type', 'sick') === 'sick') required>
                            <div class="permit-type-tile">
                                <span class="type-icon">🤒</span>
                                <strong>Sakit</strong>
                                <small>Flu, Demam, dll</small>
                            </div>
                        </label>
                        <label class="permit-type-option">
                            <input type="radio" name="type" value="excused" @checked(old('type') === 'excused') required>
                            <div class="permit-type-tile">
                                <span class="type-icon">📝</span>
                                <strong>Izin</strong>
                                <small>Acara Keluarga</small>
                            </div>
                        </label>
                        <label class="permit-type-option">
                            <input type="radio" name="type" value="other" @checked(old('type') === 'other') required>
                            <div class="permit-type-tile">
                                <span class="type-icon">📌</span>
                                <strong>Lainnya</strong>
                                <small>Dispensasi</small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 3. Pilihan Tanggal & Presets -->
                <div>
                    <label>Pilihan Waktu Izin <span style="color:#ef4444;">*</span></label>
                    <div class="preset-dates-bar">
                        <button type="button" class="btn-preset" onclick="setDatesPreset(0, 0)">Hari Ini</button>
                        <button type="button" class="btn-preset" onclick="setDatesPreset(1, 1)">Besok</button>
                        <button type="button" class="btn-preset" onclick="setDatesPreset(0, 1)">2 Hari</button>
                        <button type="button" class="btn-preset" onclick="setDatesPreset(0, 2)">3 Hari</button>
                    </div>
                    <div class="form-grid">
                        <label>
                            Mulai Tanggal
                            <input type="date" name="start_date" id="permit_start_date" value="{{ old('start_date', today()->toDateString()) }}" onchange="updateDateSummary()" required>
                        </label>
                        <label>
                            Sampai Tanggal
                            <input type="date" name="end_date" id="permit_end_date" value="{{ old('end_date', today()->toDateString()) }}" onchange="updateDateSummary()" required>
                        </label>
                    </div>
                    <div id="date-summary-box" class="dates-summary-badge">
                        🗓️ Durasi izin: <span id="date-summary-text">1 hari</span>
                    </div>
                </div>

                <!-- 4. Alasan / Keterangan -->
                <div>
                    <label for="permit_reason">Alasan Lengkap <span style="color:#ef4444;">*</span></label>
                    <textarea name="reason" id="permit_reason" placeholder="Contoh: Mengalami demam dan pusing sejak kemarin malam. Disarankan dokter istirahat di rumah..." required>{{ old('reason') }}</textarea>
                    <span class="field-help">Jelaskan kondisi atau alasan dengan sopan dan jelas untuk bahan pertimbangan pihak sekolah.</span>
                </div>

                <!-- 5. Upload Bukti / Surat Dokter (Opsional) -->
                <div>
                    <label>Lampirkan Surat Dokter / Bukti Keterangan (Opsional)</label>
                    <div class="file-upload-box" id="dropzone-box">
                        <input type="file" name="attachment" id="permit_attachment" accept="image/*,.pdf" onchange="previewUpload(this)">
                        <div id="upload-preview-wrap">
                            <span style="font-size:24px;display:block;margin-bottom:4px;">📎</span>
                            <strong style="display:block;font-size:12.5px;color:#1e293b;" id="upload-label">Pilih foto atau dokumen PDF</strong>
                            <small style="color:var(--muted);font-size:11px;">Maksimal ukuran file 5 MB (JPG, PNG, PDF)</small>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button class="btn primary" type="submit" style="width:100%;min-height:46px;font-size:14px;border-radius:11px;margin-top:6px;">
                    🚀 Kirim Pengajuan Izin
                </button>
            </form>
        </div>

        <!-- ==============================================================
             RIGHT COLUMN: RIWAYAT & DAFTAR PENGAJUAN
             ============================================================== -->
        <div>
            <!-- Search & Class Filter (if admin/teacher) -->
            @if($isAdmin || $isTeacher)
                <div class="panel" style="padding:14px 18px;margin-bottom:16px;">
                    <form method="get" action="{{ route('attendance.permissions.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <div style="flex:1;min-width:180px;">
                            <label style="font-size:11px;">Cari Santri / Alasan
                                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Ketik nama atau kata kunci...">
                            </label>
                        </div>
                        <div style="min-width:150px;">
                            <label style="font-size:11px;">Filter Kelas
                                <select name="class_id" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" @selected(($filters['class_id'] ?? '') == $c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <button class="btn primary small" type="submit">Filter</button>
                        @if(array_filter($filters))
                            <a class="btn small" href="{{ route('attendance.permissions.index') }}">Reset</a>
                        @endif
                    </form>
                </div>
            @endif

            <h3 style="font-size:15px;margin:0 0 14px;color:#334155;display:flex;align-items:center;justify-content:space-between;">
                <span>Daftar Riwayat Pengajuan ({{ $permissions->total() }})</span>
                @if($counts['pending'] > 0 && ($isAdmin || $isTeacher))
                    <span style="font-size:11px;background:#fef3c7;color:#92400e;padding:3px 8px;border-radius:999px;font-weight:800;">
                        {{ $counts['pending'] }} Perlu Verifikasi
                    </span>
                @endif
            </h3>

            @forelse($permissions as $permit)
                <article class="permit-item {{ $permit->status }}">
                    <div class="permit-header">
                        <div>
                            <span class="permit-type-badge {{ $permit->type }}">
                                {{ $permit->typeEmoji() }} {{ $permit->typeLabel() }}
                            </span>
                            <strong style="margin-left:6px;font-size:13.5px;color:#0f172a;">
                                {{ $permit->student?->name }}
                            </strong>
                            <small class="muted" style="margin-left:4px;">
                                ({{ $permit->schoolClass?->name ?? 'Belum ada kelas' }})
                            </small>
                        </div>
                        <span class="status-pill {{ $permit->status }}">
                            @if($permit->isPending()) ⏳ @elseif($permit->isApproved()) ✅ @elseif($permit->isRejected()) ❌ @endif
                            {{ $permit->statusLabel() }}
                        </span>
                    </div>

                    <!-- Tanggal Izin -->
                    <div class="permit-dates-line">
                        <span>🗓️</span>
                        <span>
                            @if($permit->start_date->equalTo($permit->end_date))
                                {{ $permit->start_date->format('d M Y') }} (1 hari)
                            @else
                                {{ $permit->start_date->format('d M Y') }} s/d {{ $permit->end_date->format('d M Y') }}
                                <span style="font-size:12px;color:var(--muted);font-weight:600;">({{ $permit->durationDays() }} hari)</span>
                            @endif
                        </span>
                    </div>

                    <!-- Alasan -->
                    <div class="permit-reason">
                        "{{ $permit->reason }}"
                    </div>

                    <!-- Lampiran Surat Dokter / Bukti -->
                    @if($permit->attachment_path)
                        <div style="margin:8px 0;">
                            <a class="btn small" href="{{ route('attendance.permissions.attachment', $permit) }}" target="_blank" rel="noopener" style="font-size:11.5px;border-color:#cbd5e1;background:#f8fafc;">
                                📎 Lihat Lampiran / Surat Dokter &nearr;
                            </a>
                        </div>
                    @endif

                    <!-- Review Note jika sudah diproses -->
                    @if($permit->review_notes)
                        <div class="permit-review-note">
                            <strong>{{ $permit->isApproved() ? 'Persetujuan' : 'Catatan Penolakan' }}:</strong> {{ $permit->review_notes }}
                            @if($permit->reviewer)
                                <span class="muted">· oleh {{ $permit->reviewer->name }} ({{ $permit->reviewed_at?->diffForHumans() }})</span>
                            @endif
                        </div>
                    @endif

                    <!-- Footer Info & Actions -->
                    <div class="permit-footer">
                        <div>
                            Diajukan oleh: <strong>{{ $permit->submittedBy?->id === $permit->student_user_id ? 'Siswa Sendiri' : ($permit->submittedBy?->name ?? 'Wali Santri') }}</strong>
                            · <span title="{{ $permit->created_at }}">{{ $permit->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="actions">
                            {{-- Jika status pending dan user berwenang review (Homeroom Teacher / Admin) --}}
                            @if($permit->isPending() && ($isAdmin || ($isTeacher && $permit->school_class_id && $user->homeroomClasses()->where('id', $permit->school_class_id)->exists())))
                                <form method="post" action="{{ route('attendance.permissions.approve', $permit) }}" onsubmit="return confirm('Setujui izin {{ $permit->student?->name }} untuk {{ $permit->durationDays() }} hari?')">
                                    @csrf
                                    <button class="btn primary small" type="submit" style="background:#10b981;border-color:#10b981;">✓ Setujui</button>
                                </form>
                                <form method="post" action="{{ route('attendance.permissions.reject', $permit) }}" onsubmit="return confirm('Tolak pengajuan izin ini?')">
                                    @csrf
                                    <button class="btn warn small" type="submit">✕ Tolak</button>
                                </form>
                            @endif

                            {{-- Jika submitter ingin membatalkan saat masih pending --}}
                            @if($permit->isPending() && ($permit->submitted_by_user_id === $user->id || $permit->student_user_id === $user->id || $isAdmin))
                                <form method="post" action="{{ route('attendance.permissions.cancel', $permit) }}" onsubmit="return confirm('Batalkan pengajuan izin ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn small" type="submit" style="color:#ef4444;border-color:#fca5a5;">Batalkan</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-state" style="padding:40px 20px;">
                    <span style="font-size:36px;display:block;margin-bottom:8px;">🏖️</span>
                    <strong style="display:block;font-size:14px;color:#1e293b;">Belum ada riwayat pengajuan izin</strong>
                    <p class="muted" style="font-size:12px;margin:4px 0 0;">Pengajuan izin sakit atau keperluan lainnya akan dicatat dan ditampilkan di sini.</p>
                </div>
            @endforelse

            <div style="margin-top:16px;">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>

    <!-- Interactive Script for Form Presets & Live Date Summary -->
    <script>
        function setDatesPreset(offsetDaysStart, offsetDaysEnd) {
            const today = new Date();
            
            const start = new Date(today);
            start.setDate(today.getDate() + offsetDaysStart);

            const end = new Date(today);
            end.setDate(today.getDate() + offsetDaysEnd);

            const startStr = start.toISOString().split('T')[0];
            const endStr = end.toISOString().split('T')[0];

            const startInput = document.getElementById('permit_start_date');
            const endInput = document.getElementById('permit_end_date');

            if (startInput && endInput) {
                startInput.value = startStr;
                endInput.value = endStr;
                updateDateSummary();
            }
        }

        function updateDateSummary() {
            const startInput = document.getElementById('permit_start_date');
            const endInput = document.getElementById('permit_end_date');
            const summaryText = document.getElementById('date-summary-text');

            if (!startInput || !endInput || !summaryText) return;

            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);

            if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                summaryText.innerText = 'Pilih tanggal valid';
                return;
            }

            if (endDate < startDate) {
                endInput.value = startInput.value;
                summaryText.innerText = '1 hari (Tanggal akhir disesuaikan)';
                return;
            }

            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            const startStr = startDate.toLocaleDateString('id-ID', options);
            const endStr = endDate.toLocaleDateString('id-ID', options);

            if (diffDays === 1) {
                summaryText.innerText = `1 hari (${startStr})`;
            } else {
                summaryText.innerText = `${diffDays} hari (${startStr} s/d ${endStr})`;
            }
        }

        function previewUpload(input) {
            const previewWrap = document.getElementById('upload-preview-wrap');
            const label = document.getElementById('upload-label');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                label.innerText = `✓ File terpilih: ${file.name} (${(file.size / 1024).toFixed(0)} KB)`;
                label.style.color = '#065f46';
            }
        }

        document.addEventListener('DOMContentLoaded', updateDateSummary);
    </script>
</x-layouts.app>

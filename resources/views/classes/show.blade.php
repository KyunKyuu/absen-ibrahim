<x-layouts.app :title="$schoolClass->name">
    <style>
        .class-detail-grid{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(280px,.65fr);gap:18px;align-items:start}.student-list{display:grid;gap:0}.student-row{display:grid;grid-template-columns:28px minmax(0,1fr) auto;gap:12px;align-items:center;padding:13px 4px;border-bottom:1px solid var(--line)}.student-row:last-child{border-bottom:0}.student-row input{width:18px;height:18px;accent-color:var(--brand,#17765b)}.student-row strong,.student-row small{display:block}.student-row small{margin-top:3px;color:var(--muted)}.student-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:12px;background:#f4f7f5;border-radius:10px;margin:14px 0}.summary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:18px}.summary-tile{padding:15px;border:1px solid var(--line);border-radius:11px;background:#fff}.summary-tile small,.summary-tile strong{display:block}.summary-tile small{color:var(--muted);margin-bottom:5px}.summary-tile strong{font-size:17px}.promote-panel{position:sticky;top:18px}.promote-note{padding:12px;border-radius:9px;background:#edf7f2;color:#245b49;margin:14px 0}.attendance-row{grid-template-columns:minmax(0,1fr) minmax(125px,180px)}.attendance-row select{min-width:125px}.@media(max-width:850px){.class-detail-grid{grid-template-columns:1fr}.promote-panel{position:static}}@media(max-width:600px){.summary-grid{grid-template-columns:1fr}.student-row{grid-template-columns:24px minmax(0,1fr)}.attendance-row{grid-template-columns:1fr}}
    </style>

    <div class="page-heading">
        <div class="page-heading-copy">
            <div class="breadcrumb"><a href="{{ route('classes.index') }}">Akademik</a><span>/</span><a href="{{ route('classes.index') }}">Daftar kelas</a><span>/</span><span>{{ $schoolClass->name }}</span></div>
            <h1>{{ $schoolClass->name }}</h1>
            <p class="muted">Detail kelas, wali kelas, pengajar, dan daftar siswa.</p>
        </div>
        <a class="btn" href="{{ route('classes.index') }}">← Kembali ke kelas</a>
    </div>

    <div class="summary-grid">
        <div class="summary-tile"><small>Tahun ajaran</small><strong>{{ $schoolClass->academicYear?->name ?? '-' }}</strong></div>
        <div class="summary-tile"><small>Wali kelas</small><strong>{{ $schoolClass->homeroomTeacher?->name ?? 'Belum ditentukan' }}</strong></div>
        <div class="summary-tile"><small>Jumlah siswa</small><strong>{{ $students->count() }} siswa</strong></div>
    </div>

    <div class="class-detail-grid">
        <section class="panel">
            @if($schoolClass->homeroom_teacher_id === auth()->id() || $canManage)
                <div class="topbar"><div><h2>Absensi hari ini</h2><span class="muted">{{ now()->translatedFormat('l, d F Y') }} · pilih status setiap siswa</span></div></div>
                <p class="muted">Hadir, sakit, izin, atau alpa. Status yang sudah tercatat lewat GPS/fingerprint tidak dapat diubah dari sini.</p>
                <form method="post" action="{{ route('classes.attendance.store', $schoolClass) }}">
                    @csrf
                    <div class="student-list">
                        @foreach($students as $student)
                            @php($existingAttendance = $todayAttendances->get($student->id))
                            <label class="student-row attendance-row">
                                <span><strong>{{ $student->name }}</strong><small>{{ $existingAttendance ? 'Sudah tercatat · '.($existingAttendance->source === 'homeroom' ? 'wali kelas' : $existingAttendance->source) : 'Belum tercatat' }}</small></span>
                                @if($existingAttendance && $existingAttendance->source !== 'homeroom')
                                    <span class="badge">{{ ['present' => 'Hadir', 'late' => 'Terlambat', 'excused' => 'Izin', 'absent' => 'Alpa', 'sick' => 'Sakit'][$existingAttendance->status] ?? $existingAttendance->status }}</span>
                                @else
                                    <select name="attendance[{{ $student->id }}]" aria-label="Status absensi {{ $student->name }}" required>
                                        <option value="present" @selected(($existingAttendance?->status ?? 'present') === 'present')>Hadir</option>
                                        <option value="sick" @selected($existingAttendance?->status === 'sick')>Sakit</option>
                                        <option value="excused" @selected($existingAttendance?->status === 'excused')>Izin</option>
                                        <option value="absent" @selected($existingAttendance?->status === 'absent')>Alpa</option>
                                    </select>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @if($students->isNotEmpty())<button class="btn primary" type="submit" style="margin-top:16px">Simpan absensi hari ini</button>@endif
                </form>
                <hr style="margin:22px 0;border:0;border-top:1px solid var(--line)">
            @endif
            <div class="topbar"><div><h2>Daftar siswa</h2><span class="muted">{{ $students->count() }} siswa di kelas {{ $schoolClass->name }}</span></div></div>
            @if($canManage && $students->isNotEmpty())
                <div class="student-toolbar">
                    <label class="check-row"><input id="select-all-students" type="checkbox"> Pilih semua siswa</label>
                    <span class="muted"><strong id="selected-count">0</strong> dipilih</span>
                </div>
            @endif
            <div class="student-list">
                @forelse($students as $student)
                    <label class="student-row">
                        @if($canManage)<input class="student-checkbox" type="checkbox" name="student_ids[]" value="{{ $student->id }}" form="promote-students-form" aria-label="Pilih {{ $student->name }}">@endif
                        <span><strong>{{ $student->name }}</strong><small>NIS / ID siswa: {{ $student->studentProfile?->nis ?: '-' }}</small></span>
                        @if($canManage)<span class="badge">{{ $student->bills->where('status', '!=', 'paid')->count() }} tunggakan</span>@endif
                    </label>
                @empty
                    <div class="empty-state">Belum ada siswa di kelas ini.</div>
                @endforelse
            </div>
        </section>

        @if($canManage)
            <aside class="panel promote-panel">
                <h2>Kenaikan kelas</h2>
                <p class="muted">Pilih siswa yang naik bersama-sama. Siswa yang tidak dicentang tetap di kelas ini.</p>
                <div class="promote-note">Tagihan yang belum lunas tetap tersimpan sebagai tunggakan setelah siswa dipindahkan.</div>
                @if($students->isNotEmpty())
                    <form id="promote-students-form" class="stack" method="post" action="{{ route('classes.promote', $schoolClass) }}" onsubmit="return confirm('Pindahkan siswa yang dipilih ke kelas tujuan?')">
                        @csrf
                        <label>Kelas tujuan
                            <select name="destination_class_id" required>
                                <option value="">Pilih kelas tujuan</option>
                                @foreach($destinationClasses as $destinationClass)
                                    <option value="{{ $destinationClass->id }}">{{ $destinationClass->name }} · {{ $destinationClass->academicYear?->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button id="promote-submit" class="btn primary" type="submit" disabled>Pindahkan siswa terpilih</button>
                    </form>
                @endif
            </aside>
        @endif
    </div>

    @if($canManage && $students->isNotEmpty())
        <script>
            (() => {
                const checkboxes = [...document.querySelectorAll('.student-checkbox')];
                const selectAll = document.getElementById('select-all-students');
                const count = document.getElementById('selected-count');
                const submit = document.getElementById('promote-submit');
                const refresh = () => {
                    const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
                    count.textContent = selected;
                    submit.disabled = selected === 0;
                    selectAll.checked = selected === checkboxes.length;
                    selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
                };
                selectAll.addEventListener('change', () => {
                    checkboxes.forEach((checkbox) => { checkbox.checked = selectAll.checked; });
                    refresh();
                });
                checkboxes.forEach((checkbox) => checkbox.addEventListener('change', refresh));
                refresh();
            })();
        </script>
    @endif
</x-layouts.app>

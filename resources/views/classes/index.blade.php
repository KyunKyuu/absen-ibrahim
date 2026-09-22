<x-layouts.app title="Kelas & Pengajaran">
    <style>
        .class-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.class-card{padding:20px;border:1px solid var(--line);border-radius:13px;background:#fff;box-shadow:var(--shadow)}.class-head{display:flex;justify-content:space-between;gap:12px;margin-bottom:16px}.class-head h2{font-size:24px;margin:0}.assignment-list{display:grid;gap:7px;margin-top:13px}.assignment{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 10px;border-radius:8px;background:#f4f7f5}.assignment span,.assignment small{display:block}.assignment small{margin-top:2px;color:var(--muted)}.manage-grid{display:grid;grid-template-columns:1fr 1.5fr;gap:18px;margin-bottom:20px}
        @media(max-width:1000px){.class-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.manage-grid{grid-template-columns:1fr}}@media(max-width:620px){.class-grid{grid-template-columns:1fr}}
    </style>
    <div class="page-heading"><div class="page-heading-copy"><div class="breadcrumb"><span>Akademik</span><span>/</span><span>Kelas & pengajaran</span></div><h1>Kelas & pengajaran</h1><p class="muted">Lihat wali kelas, jumlah siswa, dan guru mata pelajaran pada setiap kelas.</p></div></div>

    @if($canManage)
        <div class="manage-grid">
            <section class="panel"><h2>Tambah mata pelajaran</h2><form class="stack" method="post" action="{{ route('admin.subjects.store') }}">@csrf<div class="form-grid"><label>Nama<input name="name" placeholder="Bahasa Inggris" required></label><label>Kode<input name="code" placeholder="BIG"></label></div><button class="btn" type="submit">Tambah mapel</button></form></section>
            <section class="panel"><h2>Tugaskan guru mata pelajaran</h2><form class="stack" method="post" action="{{ route('admin.teaching-assignments.store') }}">@csrf<div class="form-grid"><label>Guru<select name="teacher_user_id" required>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select></label><label>Mata pelajaran<select name="subject_id" required>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></label><label>Kelas<select name="school_class_id" required>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select></label></div><button class="btn primary" type="submit">Simpan penugasan</button></form></section>
        </div>
    @endif

    <div class="class-grid">
        @forelse($classes as $class)
            <article class="class-card">
                <div class="class-head"><div><span class="eyebrow">Tingkat {{ $class->grade_level ?: '-' }}</span><h2>{{ $class->name }}</h2><span class="muted">{{ $class->academicYear?->name }}</span></div><span class="badge">{{ $class->students->count() }} siswa</span></div>
                <div><span class="field-help">WALI KELAS</span><p style="margin:5px 0 0;font-weight:750">{{ $class->homeroomTeacher?->name ?? 'Belum ditentukan' }}</p></div>
                <div class="assignment-list">
                    <span class="field-help">GURU MATA PELAJARAN</span>
                    @forelse($class->teachingAssignments as $assignment)
                        <div class="assignment"><span><strong>{{ $assignment->subject?->name ?? 'Tanpa mapel' }}</strong><small>{{ $assignment->teacher?->name }}</small></span>
                            @if($canManage)<form method="post" action="{{ route('admin.teaching-assignments.destroy', $assignment) }}">@csrf @method('delete')<button class="btn warn small" type="submit" aria-label="Hapus penugasan">×</button></form>@endif
                        </div>
                    @empty<span class="muted">Belum ada penugasan mengajar.</span>@endforelse
                </div>
            </article>
        @empty<div class="empty-state">Belum ada kelas yang dapat ditampilkan.</div>@endforelse
    </div>
</x-layouts.app>

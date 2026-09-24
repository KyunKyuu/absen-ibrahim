@php
    $pageInfo = [
        'classes' => ['Daftar kelas', 'Lihat tingkat, wali kelas, dan jumlah siswa setiap kelas.'],
        'subjects' => ['Mata pelajaran', 'Kelola daftar mata pelajaran yang digunakan dalam penugasan guru.'],
        'teaching' => ['Penugasan guru', 'Atur guru mata pelajaran dan kelas yang mereka ajar.'],
        'promotions' => ['Kenaikan kelas', 'Pindahkan siswa ke kelas berikutnya dan simpan riwayat kelasnya.'],
    ];
    [$pageTitle, $pageDescription] = $pageInfo[$section];
@endphp
<x-layouts.app :title="$pageTitle">
    <style>
        .class-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.class-card{padding:20px;border:1px solid var(--line);border-radius:13px;background:#fff;box-shadow:var(--shadow);transition:transform .15s,border-color .15s}.class-head{display:flex;justify-content:space-between;gap:12px;margin-bottom:16px}.class-head h2{font-size:24px;margin:0}.assignment-list{display:grid;gap:7px;margin-top:13px}.assignment{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 10px;border-radius:8px;background:#f4f7f5}.assignment span,.assignment small{display:block}.assignment small{margin-top:2px;color:var(--muted)}.form-panel{max-width:850px}.subject-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.subject-card{padding:16px;border:1px solid var(--line);border-radius:11px;background:#fff}.subject-card strong,.subject-card small{display:block}.subject-card small{margin-top:5px;color:var(--muted)}.class-picker{display:flex;flex-wrap:wrap;gap:6px;margin-top:14px}.class-card-link{display:block;color:inherit;text-decoration:none}.class-card-link:hover .class-card{border-color:var(--brand,#17765b);transform:translateY(-2px)}.class-cta{display:inline-flex;margin-top:15px;font-weight:700;color:var(--brand,#17765b)}
        @media(max-width:1000px){.class-grid,.subject-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.class-grid,.subject-grid{grid-template-columns:1fr}}
    </style>
    <div class="page-heading"><div class="page-heading-copy"><div class="breadcrumb"><span>Akademik</span><span>/</span><span>{{ $pageTitle }}</span></div><h1>{{ $pageTitle }}</h1><p class="muted">{{ $pageDescription }}</p></div></div>
    <nav class="section-tabs" aria-label="Kelas dan pengajaran">
        <a class="{{ $section === 'classes' ? 'active' : '' }}" href="{{ route('classes.index') }}">Daftar kelas</a>
        @if($canManage)<a class="{{ $section === 'subjects' ? 'active' : '' }}" href="{{ route('classes.subjects') }}">Mata pelajaran</a><a class="{{ $section === 'teaching' ? 'active' : '' }}" href="{{ route('classes.teaching') }}">Penugasan guru</a><a class="{{ $section === 'promotions' ? 'active' : '' }}" href="{{ route('classes.promotions') }}">Kenaikan kelas</a>@endif
    </nav>

    @if($section === 'classes')
        <div class="class-grid">
            @forelse($classes as $class)
                <a class="class-card-link" href="{{ route('classes.show', $class) }}"><article class="class-card">
                    <div class="class-head"><div><span class="eyebrow">Tingkat {{ $class->grade_level ?: '-' }}</span><h2>{{ $class->name }}</h2><span class="muted">{{ $class->academicYear?->name }}</span></div><span class="badge">{{ $class->students->count() }} siswa</span></div>
                    <div><span class="field-help">WALI KELAS</span><p style="margin:5px 0 0;font-weight:750">{{ $class->homeroomTeacher?->name ?? 'Belum ditentukan' }}</p></div>
                    <div class="assignment-list"><span class="field-help">PENGAJAR</span>
                        @forelse($class->teachingAssignments as $assignment)<div class="assignment"><span><strong>{{ $assignment->subject?->name ?? 'Tanpa mapel' }}</strong><small>{{ $assignment->teacher?->name }}</small></span></div>
                        @empty<span class="muted">Belum ada penugasan mata pelajaran.</span>@endforelse
                    </div>
                    <span class="class-cta">Lihat detail kelas →</span>
                </article></a>
            @empty<div class="empty-state">Belum ada kelas yang dapat ditampilkan.</div>@endforelse
        </div>
    @elseif($section === 'subjects')
        <section class="panel form-panel" style="margin-bottom:18px"><h2>Tambah mata pelajaran</h2><form class="stack" method="post" action="{{ route('admin.subjects.store') }}">@csrf<div class="form-grid"><label>Nama mata pelajaran<input name="name" placeholder="Bahasa Inggris" required></label><label>Kode<input name="code" placeholder="BIG"><span class="field-help">Opsional, harus unik.</span></label></div><button class="btn primary" type="submit">Tambah mata pelajaran</button></form></section>
        <section class="panel"><div class="topbar"><div><h2>Daftar mata pelajaran</h2><span class="muted">{{ $subjects->count() }} mata pelajaran</span></div></div><div class="subject-grid">
            @forelse($subjects as $subject)<article class="subject-card"><span class="eyebrow">{{ $subject->code ?: 'MAPEL' }}</span><strong style="margin-top:7px">{{ $subject->name }}</strong><small>{{ $subject->teachingAssignments()->count() }} penugasan kelas</small></article>
            @empty<div class="empty-state">Belum ada mata pelajaran.</div>@endforelse
        </div></section>
    @elseif($section === 'teaching')
        <section class="panel form-panel" style="margin-bottom:18px"><h2>Buat penugasan</h2><form class="stack" method="post" action="{{ route('admin.teaching-assignments.store') }}">@csrf
            <div class="form-grid"><label>Guru<select name="teacher_user_id" required><option value="">Pilih guru</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select></label><label>Mata pelajaran<select name="subject_id" required><option value="">Pilih mata pelajaran</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></label></div>
            <label>Kelas yang diajar <span class="field-help">Pilih satu atau beberapa kelas. Guru bisa mengajar beberapa kelas.</span></label><div class="class-picker">@foreach($classes as $class)<label class="check-row" style="padding:9px 12px;border:1px solid var(--line);border-radius:9px"><input type="checkbox" name="school_class_ids[]" value="{{ $class->id }}"> {{ $class->name }}</label>@endforeach</div>
            <button class="btn primary" type="submit">Simpan penugasan</button>
        </form></section>
        <section class="panel"><div class="topbar"><div><h2>Penugasan saat ini</h2><span class="muted">{{ $assignments->count() }} penugasan</span></div></div><div class="table-scroll"><table><thead><tr><th>Guru</th><th>Mata pelajaran</th><th>Kelas</th><th></th></tr></thead><tbody>
            @forelse($assignments as $assignment)<tr><td><strong>{{ $assignment->teacher?->name }}</strong></td><td>{{ $assignment->subject?->name }}</td><td>{{ $assignment->schoolClass?->name }}</td><td><form method="post" action="{{ route('admin.teaching-assignments.destroy', $assignment) }}">@csrf @method('delete')<button class="btn warn small" type="submit">Hapus</button></form></td></tr>
            @empty<tr><td colspan="4" class="muted">Belum ada penugasan guru.</td></tr>@endforelse
        </tbody></table></div></section>
    @else
        <div class="alert" style="margin-bottom:16px">Pilih kelas asal untuk mengatur kenaikan secara kolektif. Di detail kelas, Anda bisa memilih semua siswa atau hanya siswa tertentu. Siswa yang tidak dicentang tetap di kelas asal.</div>
        <div class="class-grid">
            @forelse($classes as $class)
                <a class="class-card-link" href="{{ route('classes.show', $class) }}"><article class="class-card">
                    <div class="class-head"><div><span class="eyebrow">TINGKAT {{ $class->grade_level ?: '-' }}</span><h2>{{ $class->name }}</h2><span class="muted">{{ $class->academicYear?->name }}</span></div><span class="badge">{{ $class->students->count() }} siswa</span></div>
                    <span class="field-help">WALI KELAS</span><p style="margin:5px 0 0;font-weight:750">{{ $class->homeroomTeacher?->name ?? 'Belum ditentukan' }}</p>
                    <span class="class-cta">Pilih siswa untuk naik kelas →</span>
                </article></a>
            @empty<div class="empty-state">Belum ada kelas.</div>@endforelse
        </div>
    @endif
</x-layouts.app>

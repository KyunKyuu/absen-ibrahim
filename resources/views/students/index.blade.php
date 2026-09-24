<x-layouts.app title="Daftar siswa">
    <style>
        .student-filters{display:grid;grid-template-columns:minmax(240px,1fr) minmax(170px,.55fr) 125px auto;gap:10px;align-items:end;margin-bottom:18px}.student-results{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:14px}.roster{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.student-card{display:flex;align-items:center;gap:13px;padding:15px;border:1px solid var(--line);border-radius:12px;color:inherit;text-decoration:none;background:#fff;transition:transform .15s,border-color .15s}.student-card:hover{transform:translateY(-2px);border-color:var(--accent)}.student-avatar{width:48px;height:48px;display:grid;place-items:center;border-radius:15px;background:linear-gradient(140deg,#d4f5e5,#98ddbd);color:#14533d;font-size:17px;font-weight:850;flex:none}.student-card-main{min-width:0;flex:1}.student-card-main strong,.student-card-main small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.student-card-main small{margin-top:4px;color:var(--muted)}.student-xp{text-align:right}.student-xp strong,.student-xp small{display:block}.student-xp small{font-size:11px;color:var(--muted)}.pagination{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:18px}.pagination-pages{display:flex;gap:5px}.pagination a,.pagination span{display:inline-flex;align-items:center;justify-content:center;min-width:36px;min-height:36px;padding:7px 10px;border:1px solid var(--line);border-radius:8px;background:#fff;font-size:12px;font-weight:700}.pagination .active{background:var(--accent);color:#fff}.pagination .disabled{color:#aeb8b3;background:#f5f7f5}@media(max-width:800px){.student-filters{grid-template-columns:1fr 1fr}.roster{grid-template-columns:1fr}}@media(max-width:540px){.student-filters{grid-template-columns:1fr}.pagination{align-items:flex-start;flex-direction:column}}
    </style>
    <div class="page-heading"><div class="page-heading-copy"><div class="breadcrumb"><span>Akademik</span><span>/</span><span>Siswa</span></div><h1>Daftar siswa</h1><p class="muted">Cari siswa per nama atau NIS, filter berdasarkan kelas, lalu buka profil perkembangan mereka.</p></div></div>
    <section class="panel">
        <form class="student-filters" method="get" action="{{ route('students.index') }}">
            <label>Cari siswa<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama, username, atau NIS..."></label>
            <label>Kelas<select name="class_id"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(($filters['class_id'] ?? '') == $class->id)>{{ $class->name }}</option>@endforeach</select></label>
            <label>Per halaman<select name="per_page"><option value="20" @selected(($filters['per_page'] ?? 20) == 20)>20 siswa</option><option value="50" @selected(($filters['per_page'] ?? 20) == 50)>50 siswa</option><option value="100" @selected(($filters['per_page'] ?? 20) == 100)>100 siswa</option></select></label>
            <button class="btn primary" type="submit">Cari</button>
        </form>
        <div class="student-results"><div><h2 style="margin-bottom:4px">Siswa terdaftar</h2><span class="muted">{{ $students->total() }} siswa ditemukan</span></div>@if(array_filter($filters))<a class="btn small" href="{{ route('students.index') }}">Reset filter</a>@endif</div>
        <div class="roster">
            @forelse($students as $student)
                @php($points = $student->pointSummary?->general_points ?? 0)
                @php($level = match (true) { $points >= 100 => 'Teladan', $points >= 50 => 'Berkembang', $points >= 0 => 'Pemula', default => 'Mulai Bertumbuh' })
                <a class="student-card" href="{{ route('students.show', $student) }}">
                    <span class="student-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($student->name, 0, 1)) }}</span>
                    <span class="student-card-main"><strong>{{ $student->name }}</strong><small>{{ $student->studentProfile?->schoolClass?->name ?? 'Belum ada kelas' }} · NIS {{ $student->studentProfile?->nis ?? '-' }}</small></span>
                    <span class="student-xp"><strong>{{ $points }} XP</strong><small>{{ $level }}</small></span>
                </a>
            @empty<div class="empty-state">Tidak ada siswa yang cocok dengan pencarian ini.</div>@endforelse
        </div>
        @if($students->hasPages())<nav class="pagination" aria-label="Halaman siswa"><span>Halaman {{ $students->currentPage() }} dari {{ $students->lastPage() }}</span><div class="pagination-pages">@if($students->onFirstPage())<span class="disabled">←</span>@else<a href="{{ $students->previousPageUrl() }}">←</a>@endif @foreach($students->getUrlRange(max(1,$students->currentPage()-2),min($students->lastPage(),$students->currentPage()+2)) as $page=>$url) @if($page===$students->currentPage())<span class="active">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif @endforeach @if($students->hasMorePages())<a href="{{ $students->nextPageUrl() }}">→</a>@else<span class="disabled">→</span>@endif</div></nav>@endif
    </section>
</x-layouts.app>

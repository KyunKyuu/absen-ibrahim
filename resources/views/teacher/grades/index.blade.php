<x-layouts.app title="Nilai mata pelajaran">
    <div class="page-heading">
        <div><div class="breadcrumb">Penilaian / Nilai mata pelajaran</div><h1>{{ $section === 'create' ? 'Buat penilaian' : 'Nilai mata pelajaran' }}</h1><p class="muted">Nilai akademik 0–100, berdasarkan mata pelajaran dan kelas yang ditugaskan kepada guru.</p></div>
        @if($section === 'list' && $assignments->isNotEmpty())<a class="btn primary" href="{{ route('teacher.grades.create') }}">+ Buat penilaian</a>@else<a class="btn" href="{{ route('teacher.grades.index') }}">Daftar penilaian</a>@endif
    </div>
    @if($section === 'create')
        @if($assignments->isEmpty())
            <div class="empty-state">Belum ada penugasan mata pelajaran. Admin perlu menetapkan guru, kelas, dan mata pelajaran di Kelas & pengajaran → Penugasan guru.</div>
        @else
            <section class="panel" style="max-width:850px">
                <form class="stack" method="post" action="{{ route('teacher.grades.store') }}">@csrf
                    <label>Kelas & mata pelajaran yang Anda ajar<select name="assignment_id" required><option value="">Pilih penugasan</option>@foreach($assignments as $assignment)<option value="{{ $assignment->id }}" @selected(old('assignment_id') == $assignment->id)>{{ $assignment->schoolClass->name }} · {{ $assignment->subject->name }} · {{ $assignment->schoolClass->academicYear?->name ?? 'Tahun ajaran belum diatur' }}</option>@endforeach</select></label>
                <label>Semester<select name="semester_id" required><option value="">Pilih semester aktif</option>@foreach($semesters->where('is_active', true) as $semester)<option value="{{ $semester->id }}" @selected(old('semester_id') == $semester->id)>{{ $semester->name }} · {{ $semester->academicYear?->name }}</option>@endforeach</select><span class="field-help">Penilaian baru mengikuti semester aktif di Master Data.</span></label>
                    <label>Judul penilaian<input name="title" value="{{ old('title') }}" maxlength="150" placeholder="Contoh: Ulangan Bahasa Inggris — Simple Past Tense" required></label>
                    <div class="form-grid"><label>Jenis<select name="kind" required>@foreach(\App\Models\GradeAssessment::KINDS as $key => $label)<option value="{{ $key }}" @selected(old('kind') === $key)>{{ $label }}</option>@endforeach</select></label><label>Tanggal penilaian<input type="date" name="assessed_on" value="{{ old('assessed_on', today()->toDateString()) }}" required></label></div>
                    <p class="muted">Sesudah penilaian dibuat, isi nilai setiap siswa pada halaman berikutnya. Nilai akademik dicatat terpisah dari poin karakter/XP.</p>
                    <button class="btn primary" type="submit">Buat & lanjut isi nilai</button>
                </form>
            </section>
        @endif
    @else
        <section class="panel">
            <form class="form-grid" method="get" action="{{ route('teacher.grades.index') }}" style="margin-bottom:20px">
                <label>Cari penilaian<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Judul penilaian"></label>
                <label>Semester<select name="semester_id"><option value="">Semua semester</option>@foreach($semesters as $semester)<option value="{{ $semester->id }}" @selected(($filters['semester_id'] ?? '') == $semester->id)>{{ $semester->name }} · {{ $semester->academicYear?->name }}</option>@endforeach</select></label>
                <div class="actions"><button class="btn" type="submit">Terapkan</button><a class="btn" href="{{ route('teacher.grades.index') }}">Reset</a></div>
            </form>
            <div class="table-scroll"><table><thead><tr><th>Penilaian</th><th>Kelas / mapel</th><th>Semester</th><th>Guru</th><th>Terisi</th><th></th></tr></thead><tbody>
                @forelse($assessments as $assessment)<tr>
                    <td><strong>{{ $assessment->title }}</strong><br><span class="muted">{{ \App\Models\GradeAssessment::KINDS[$assessment->kind] }} · {{ $assessment->assessed_on->format('d M Y') }}</span></td>
                    <td>{{ $assessment->schoolClass->name }}<br>{{ $assessment->subject->name }}</td><td>{{ $assessment->semester->name }}<br>{{ $assessment->semester->academicYear?->name }}</td>
                    <td>{{ $assessment->teacher->name }}</td><td>{{ $assessment->grades_count }} siswa</td><td><a class="btn small" href="{{ route('teacher.grades.show', $assessment) }}">Buka nilai</a></td>
                </tr>@empty<tr><td colspan="6">Belum ada penilaian yang sesuai. Buat penilaian dari kelas dan mata pelajaran yang Anda ajar.</td></tr>@endforelse
            </tbody></table></div>
            <x-table-pagination :paginator="$assessments" label="Penilaian mata pelajaran" />
        </section>
    @endif
</x-layouts.app>

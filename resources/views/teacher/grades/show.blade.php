<x-layouts.app :title="$assessment->title">
    <div class="page-heading"><div><div class="breadcrumb"><a href="{{ route('teacher.grades.index') }}">Nilai mata pelajaran</a> / Isi nilai</div><h1>{{ $assessment->title }}</h1><p class="muted">{{ $assessment->schoolClass->name }} · {{ $assessment->subject->name }} · {{ $assessment->semester->name }} {{ $assessment->semester->academicYear?->name }}<br>{{ $assessment->teacher->name }} · {{ $assessment->assessed_on->format('d M Y') }}</p></div><a class="btn" href="{{ route('teacher.grades.index') }}">Kembali</a></div>
    <section class="panel">
        <form class="actions" method="get" action="{{ route('teacher.grades.show', $assessment) }}" style="margin-bottom:18px"><label>Cari siswa<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama atau NIS"></label><button class="btn" type="submit">Cari</button><a href="{{ route('teacher.grades.show', $assessment) }}">Semua siswa</a></form>
        @if($canEdit)<p class="muted">Isi nilai 0–100. Simpan halaman ini sebelum pindah halaman atau melakukan pencarian. Kolom kosong tidak menghapus nilai yang sudah tersimpan.</p>@else<div class="alert">Mode baca. Hanya guru pemilik penilaian yang masih ditugaskan mengajar kelas dan mapel ini dapat menyimpan nilai.</div>@endif
        <form id="grade-form" method="post" action="{{ route('teacher.grades.save', $assessment) }}">@csrf
            <div class="table-scroll"><table><thead><tr><th>Siswa</th><th>NIS</th><th>Nilai</th><th>Catatan (opsional)</th></tr></thead><tbody>
                @forelse($students as $student)
                    @php
                        $grade = $grades->get($student->id);
                        $editable = $canEdit && $student->is_active && $student->role === 'student' && $student->studentProfile?->school_class_id === $assessment->school_class_id;
                    @endphp
                    <tr><td><strong>{{ $student->name }}</strong>@unless($editable || !$canEdit)<br><span class="muted">Arsip — siswa sudah pindah / nonaktif</span>@endunless</td><td>{{ $student->studentProfile?->nis ?? '-' }}</td>
                        @if($editable)
                            <td><input type="hidden" name="grades[{{ $student->id }}][student_user_id]" value="{{ $student->id }}"><input aria-label="Nilai {{ $student->name }}" type="number" min="0" max="100" step="0.01" name="grades[{{ $student->id }}][score]" value="{{ old('grades.'.$student->id.'.score', $grade?->score) }}" style="min-width:90px"></td>
                            <td><input aria-label="Catatan {{ $student->name }}" name="grades[{{ $student->id }}][notes]" maxlength="1000" value="{{ old('grades.'.$student->id.'.notes', $grade?->notes) }}"></td>
                        @else<td>{{ $grade?->score ?? 'Belum diisi' }}</td><td>{{ $grade?->notes ?? '-' }}</td>@endif
                    </tr>
                @empty<tr><td colspan="4">Tidak ada siswa yang sesuai.</td></tr>@endforelse
            </tbody></table></div>
            @if($canEdit && $students->isNotEmpty())<button class="btn primary" style="margin-top:16px" type="submit">Simpan nilai halaman ini</button>@endif
        </form>
        <x-table-pagination :paginator="$students" label="Daftar siswa" />
    </section>
    <script>
        (() => {
            const form = document.getElementById('grade-form');
            let dirty = false;
            form.addEventListener('input', () => { dirty = true; });
            form.addEventListener('submit', () => { dirty = false; });
            window.addEventListener('beforeunload', (event) => {
                if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
        })();
    </script>
</x-layouts.app>

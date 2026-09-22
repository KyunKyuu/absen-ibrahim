@php($isAttitude = $section === 'attitude')
<x-layouts.app :title="$isAttitude ? 'Penilaian Sikap' : 'Prestasi & Pelanggaran'">
    <style>
        .assessment-layout{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(270px,.6fr);gap:18px;align-items:start}.step{display:flex;align-items:center;gap:10px;margin-bottom:12px}.step-number{display:grid;place-items:center;width:28px;height:28px;border-radius:50%;color:#fff;background:var(--accent);font-size:12px;font-weight:850}.class-picker{display:flex;gap:8px;flex-wrap:wrap}.class-picker a{padding:9px 13px;border:1px solid var(--line);border-radius:9px;background:#fff;font-weight:750}.class-picker a.active{border-color:var(--accent);color:#fff;background:var(--accent)}.student-search{position:relative;margin-bottom:10px}.student-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;max-height:270px;overflow:auto}.student-option{display:flex;align-items:center;gap:10px;padding:11px;border:1px solid var(--line);border-radius:9px;cursor:pointer}.student-option:hover,.student-option.selected{border-color:#6da58f;background:var(--accent-soft)}.student-option input{position:absolute;opacity:0;pointer-events:none}.student-avatar{display:grid;place-items:center;width:34px;height:34px;flex:0 0 auto;border-radius:50%;color:var(--accent);background:#dceee6;font-weight:850}.student-option strong,.student-option small{display:block}.student-option small{margin-top:2px;color:var(--muted)}.score-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px}.score-card{display:grid;gap:3px;padding:11px 7px;border:1px solid var(--line);border-radius:9px;text-align:center;cursor:pointer}.score-card input{position:absolute;opacity:0}.score-card:has(input:checked){border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}.score-card strong{font-size:18px}.score-card small{font-size:10px}.game-card{position:sticky;top:24px;overflow:hidden;background:linear-gradient(145deg,#173c30,#0f2921);color:#fff}.game-card .muted{color:#a9beb6}.point-orb{display:grid;place-items:center;width:92px;height:92px;margin:20px auto;border:7px solid rgba(185,227,204,.18);border-radius:50%;background:#b9e3cc;color:#173c30}.point-orb strong{font-size:27px}.level-track{height:8px;overflow:hidden;border-radius:99px;background:rgba(255,255,255,.13)}.level-track span{display:block;height:100%;width:var(--progress,0%);border-radius:inherit;background:#b9e3cc}.level-list{display:grid;gap:8px;margin-top:18px}.level-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.09);font-size:12px}.selected-student{padding:12px;border-radius:10px;background:rgba(255,255,255,.08)}
        @media(max-width:950px){.assessment-layout{grid-template-columns:1fr}.game-card{position:static}}@media(max-width:600px){.student-grid{grid-template-columns:1fr}.score-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    </style>
    <div class="page-heading"><div class="page-heading-copy"><div class="breadcrumb"><span>Penilaian</span><span>/</span><span>{{ $isAttitude ? 'Sikap' : 'Prestasi & pelanggaran' }}</span></div><h1>{{ $isAttitude ? 'Penilaian sikap' : 'Prestasi & pelanggaran' }}</h1><p class="muted">Pilih kelas dan siswa, lalu poin perkembangan akan dihitung otomatis.</p></div></div>
    <nav class="section-tabs"><a class="{{ $isAttitude ? 'active' : '' }}" href="{{ route('teacher.assessments', ['class' => $selectedClassId]) }}">Penilaian sikap</a><a class="{{ ! $isAttitude ? 'active' : '' }}" href="{{ route('teacher.assessments.achievements', ['class' => $selectedClassId]) }}">Prestasi & pelanggaran</a></nav>

    @if($classes->isEmpty())
        <div class="empty-state">Anda belum ditugaskan sebagai wali kelas atau guru mata pelajaran. Hubungi administrator sekolah.</div>
    @else
        <section class="panel" style="margin-bottom:18px">
            <div class="step"><span class="step-number">1</span><div><h2 style="margin:0">Pilih kelas</h2><span class="muted">Hanya kelas yang Anda ampu yang ditampilkan.</span></div></div>
            <div class="class-picker">@foreach($classes as $class)<a class="{{ $selectedClassId === $class->id ? 'active' : '' }}" href="{{ $isAttitude ? route('teacher.assessments', ['class' => $class->id]) : route('teacher.assessments.achievements', ['class' => $class->id]) }}">{{ $class->name }}</a>@endforeach</div>
        </section>

        <div class="assessment-layout">
            <section class="panel">
                <form class="stack" method="post" action="{{ $isAttitude ? route('teacher.assessments.attitude') : route('teacher.assessments.achievement') }}">@csrf
                    <div>
                        <div class="step"><span class="step-number">2</span><div><h2 style="margin:0">Pilih siswa</h2><span class="muted">{{ $students->count() }} siswa di kelas ini</span></div></div>
                        <div class="student-search"><input id="student-search" type="search" placeholder="Cari nama atau NIS..." autocomplete="off"></div>
                        <div class="student-grid" id="student-grid">
                            @forelse($students as $student)
                                <label class="student-option" data-search="{{ strtolower($student->name.' '.$student->studentProfile?->nis) }}" data-name="{{ $student->name }}" data-points="{{ $student->pointSummary?->general_points ?? 0 }}" data-level="{{ $student->pointSummary?->label ?? 'Perlu Dipantau' }}">
                                    <input type="radio" name="student_user_id" value="{{ $student->id }}" required>
                                    <span class="student-avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</span><span><strong>{{ $student->name }}</strong><small>NIS {{ $student->studentProfile?->nis ?: '-' }} · {{ $student->pointSummary?->general_points ?? 0 }} poin</small></span>
                                </label>
                            @empty<div class="empty-state" style="grid-column:1/-1">Belum ada siswa di kelas ini.</div>@endforelse
                        </div>
                        <div class="empty-state" id="student-empty" hidden>Tidak ada siswa yang cocok.</div>
                    </div>

                    <div style="border-top:1px solid var(--line);padding-top:18px">
                        <div class="step"><span class="step-number">3</span><h2 style="margin:0">Beri penilaian</h2></div>
                        @if($subjects->isNotEmpty())<label style="margin-bottom:15px">Mata pelajaran<select name="subject_id"><option value="">Penilaian umum</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></label>@endif
                        @if($isAttitude)
                            <label style="margin-bottom:15px">Aspek<select name="aspect" required><option>Disiplin</option><option>Tanggung Jawab</option><option>Kerja Sama</option><option>Kejujuran</option><option>Sopan Santun</option><option>Kepemimpinan</option></select></label>
                            <div class="score-grid" style="margin-bottom:15px">
                                @foreach([5 => ['Sangat Baik', 5], 4 => ['Baik', 3], 3 => ['Cukup', 1], 2 => ['Kurang', -3], 1 => ['Buruk', -5]] as $score => [$label, $points])
                                    <label class="score-card"><input type="radio" name="score" value="{{ $score }}" data-points="{{ $points }}" @checked($score === 5) required><strong>{{ $score }}</strong><small>{{ $label }}</small><small>{{ $points > 0 ? '+' : '' }}{{ $points }} poin</small></label>
                                @endforeach
                            </div>
                        @else
                            <label style="margin-bottom:15px">Judul<input name="title" placeholder="Contoh: Juara lomba pidato" required></label>
                            <div class="form-grid" style="margin-bottom:15px"><label>Kategori<select name="category" id="achievement-category" required><option value="achievement">Prestasi / pencapaian</option><option value="violation">Pelanggaran</option></select></label><label>Poin<input id="achievement-points" name="points" type="number" min="-100" max="100" value="10" required></label></div>
                        @endif
                        <label>Catatan<textarea name="notes" placeholder="Tuliskan pengamatan yang konkret"></textarea></label>
                    </div>
                    <button class="btn primary" type="submit" @disabled($students->isEmpty())>{{ $isAttitude ? 'Simpan penilaian & poin' : 'Simpan prestasi / pelanggaran' }}</button>
                </form>
            </section>

            <aside class="panel game-card">
                <span class="eyebrow" style="color:#b9e3cc">Dampak penilaian</span><h2 style="font-size:23px;margin-top:8px">Perjalanan karakter</h2>
                <div class="selected-student"><span class="muted">Siswa terpilih</span><strong id="selected-student-name" style="display:block;margin-top:4px">Belum dipilih</strong><small id="selected-student-level" class="muted">Pilih siswa dari daftar</small></div>
                <div class="point-orb"><strong id="point-impact">{{ $isAttitude ? '+5' : '+10' }}</strong><small>poin</small></div>
                <div><div style="display:flex;justify-content:space-between;margin-bottom:7px"><span id="current-level">Level saat ini</span><span id="current-points">0 poin</span></div><div class="level-track"><span id="level-progress"></span></div></div>
                <div class="level-list"><div class="level-row"><span>Teladan</span><strong>100+ poin</strong></div><div class="level-row"><span>Berkembang Baik</span><strong>50–99</strong></div><div class="level-row"><span>Perlu Dipantau</span><strong>0–49</strong></div><div class="level-row"><span>Perlu Pembinaan</span><strong>-49–-1</strong></div></div>
            </aside>
        </div>
    @endif

    <script>
        (() => {
            const search = document.getElementById('student-search');
            const options = Array.from(document.querySelectorAll('.student-option'));
            const empty = document.getElementById('student-empty');
            const syncSearch = () => {
                const query = (search?.value || '').toLowerCase().trim();
                let visible = 0;
                options.forEach((option) => { const show = option.dataset.search.includes(query); option.hidden = !show; if (show) visible++; });
                if (empty) empty.hidden = visible > 0;
            };
            search?.addEventListener('input', syncSearch);
            options.forEach((option) => option.querySelector('input')?.addEventListener('change', () => {
                options.forEach((item) => item.classList.toggle('selected', item === option));
                const points = Number(option.dataset.points || 0);
                document.getElementById('selected-student-name').textContent = option.dataset.name;
                document.getElementById('selected-student-level').textContent = option.dataset.level;
                document.getElementById('current-level').textContent = option.dataset.level;
                document.getElementById('current-points').textContent = points + ' poin';
                document.getElementById('level-progress').style.setProperty('--progress', Math.max(0, Math.min(100, points)) + '%');
            }));
            document.querySelectorAll('[name="score"]').forEach((input) => input.addEventListener('change', () => {
                const points = Number(input.dataset.points); document.getElementById('point-impact').textContent = (points > 0 ? '+' : '') + points;
            }));
            const achievementPoints = document.getElementById('achievement-points');
            achievementPoints?.addEventListener('input', () => { const points = Number(achievementPoints.value || 0); document.getElementById('point-impact').textContent = (points > 0 ? '+' : '') + points; });
            document.getElementById('achievement-category')?.addEventListener('change', (event) => { achievementPoints.value = event.target.value === 'violation' ? -10 : 10; achievementPoints.dispatchEvent(new Event('input')); });
        })();
    </script>
</x-layouts.app>

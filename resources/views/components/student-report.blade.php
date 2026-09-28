@props(['student', 'grades'])

<section class="panel student-report">
    <div class="topbar" style="margin-bottom:10px">
        <div>
            <span class="eyebrow">Raport akademik</span>
            <h2 style="margin:4px 0 0">Nilai setiap mata pelajaran</h2>
        </div>
        <span class="badge">{{ $grades->count() }} mapel</span>
    </div>

    @if($grades->isEmpty())
        <div class="empty-state">Belum ada nilai untuk semester yang dipilih.</div>
    @else
        <div class="student-report-list">
            @foreach($grades as $subjectName => $assessments)
                @php
                    $scores = $assessments->flatMap(fn ($assessment) => $assessment->grades->pluck('score'))->filter(fn ($score) => $score !== null);
                    $average = $scores->isNotEmpty() ? round($scores->avg(), 2) : null;
                @endphp
                <details class="student-report-subject" @if($loop->first) open @endif>
                    <summary>
                        <strong>{{ $subjectName }}</strong>
                        <span class="report-average">{{ $average !== null ? number_format($average, 2, ',', '.') : '-' }}</span>
                    </summary>
                    <div class="student-report-items">
                        @foreach($assessments as $assessment)
                            @php($grade = $assessment->grades->first())
                            <div class="student-report-row">
                                <span><strong>{{ $assessment->title }}</strong><small>{{ \App\Models\GradeAssessment::KINDS[$assessment->kind] ?? ucfirst($assessment->kind) }} · {{ $assessment->assessed_on?->format('d M Y') }}</small></span>
                                <strong>{{ $grade?->score !== null ? number_format((float) $grade->score, 2, ',', '.') : '-' }}</strong>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>
    @endif
</section>

@props(['student', 'progress', 'compact' => false])

@php($summary = $student->pointSummary)
<section class="panel student-progress {{ $compact ? 'compact' : '' }}">
    <div class="student-heading">
        <div>
            <span class="eyebrow">Profil perkembangan</span>
            <h2>{{ $student->name }}</h2>
            <p class="muted">{{ $student->studentProfile?->schoolClass?->name ?? 'Belum ada kelas' }} · NIS {{ $student->studentProfile?->nis ?? '-' }}</p>
        </div>
        <span class="level-badge {{ ($summary?->general_points ?? 0) < 0 ? 'priority' : '' }}">{{ $progress['level']['current'] }}</span>
    </div>

    <div class="point-grid" aria-label="Poin kumulatif">
        <div><span>General</span><strong>{{ $summary?->general_points ?? 0 }}</strong></div>
        <div><span>Sikap</span><strong>{{ $summary?->attitude_points ?? 0 }}</strong></div>
        <div><span>Kehadiran</span><strong>{{ $summary?->attendance_points ?? 0 }}</strong></div>
        <div><span>Prestasi</span><strong>{{ $summary?->achievement_points ?? 0 }}</strong></div>
    </div>

    <div class="level-progress">
        <div class="progress-copy"><span>Progres ke {{ $progress['level']['next'] }}</span><span>{{ $progress['level']['percentage'] }}%</span></div>
        <div class="progress-track"><span style="width:{{ $progress['level']['percentage'] }}%"></span></div>
        @if($progress['level']['points_to_next'] > 0)<small class="muted">Butuh {{ $progress['level']['points_to_next'] }} poin lagi.</small>@endif
    </div>

    <div class="attendance-grid">
        <div><strong>{{ $progress['attendance']['total'] }}</strong><span>Total check-in</span></div>
        <div><strong>{{ $progress['attendance']['ontime'] }}</strong><span>Tepat waktu</span></div>
        <div><strong>{{ $progress['attendance']['late'] }}</strong><span>Terlambat</span></div>
        <div><strong>{{ $progress['attendance']['ontime_rate'] }}%</strong><span>Ketepatan waktu</span></div>
    </div>

    @unless($compact)
        <div class="progress-details">
            <div>
                <h2>Aktivitas Terbaru</h2>
                <div class="timeline">
                    @forelse($progress['timeline'] as $activity)
                        <div class="timeline-item">
                            <span class="timeline-dot"></span>
                            <div><strong>{{ $activity['title'] }}</strong><small>{{ $activity['date']->format('d M Y') }} · {{ $activity['detail'] }}</small></div>
                            <span class="point-change {{ $activity['points'] < 0 ? 'negative' : '' }}">{{ $activity['points'] > 0 ? '+' : '' }}{{ $activity['points'] }}</span>
                        </div>
                    @empty
                        <p class="muted">Belum ada aktivitas pada filter ini.</p>
                    @endforelse
                </div>
            </div>
            <div>
                <h2>Riwayat Kelas</h2>
                <div class="class-history">
                    @forelse($progress['class_history'] as $history)
                        <div><strong>{{ $history->schoolClass?->name }}</strong><span>{{ $history->academicYear?->name ?? '-' }} · {{ $history->started_on?->format('d M Y') }}–{{ $history->ended_on?->format('d M Y') ?? 'sekarang' }}</span></div>
                    @empty
                        <div><strong>{{ $student->studentProfile?->schoolClass?->name ?? 'Belum ada kelas' }}</strong><span class="muted">Kelas saat ini</span></div>
                    @endforelse
                </div>
            </div>
        </div>
    @endunless
</section>

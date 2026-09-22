<?php

namespace App\Services;

use App\Models\AchievementAssessment;
use App\Models\Attendance;
use App\Models\AttitudeAssessment;
use App\Models\PointTransaction;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\User;

class StudentProgressService
{
    public function summarize(User $student, ?Semester $semester = null, ?int $schoolClassId = null): array
    {
        $student->loadMissing(['studentProfile.schoolClass', 'pointSummary']);

        $attendances = Attendance::query()
            ->with(['schoolClass', 'semester'])
            ->where('student_user_id', $student->id)
            ->when($semester, fn ($query) => $query->where('semester_id', $semester->id))
            ->when($schoolClassId, fn ($query) => $query->where('school_class_id', $schoolClassId))
            ->latest('attendance_date')
            ->get();

        $pointTransactions = PointTransaction::query()
            ->where('student_user_id', $student->id)
            ->where('type', '!=', 'attendance')
            ->when($semester, fn ($query) => $query
                ->whereDate('created_at', '>=', $semester->starts_on)
                ->whereDate('created_at', '<=', $semester->ends_on))
            ->when($schoolClassId, fn ($query) => $this->scopePointsForClass($query, $schoolClassId))
            ->latest()
            ->limit(30)
            ->get();

        $timeline = $attendances->take(30)->map(fn (Attendance $attendance) => [
            'date' => $attendance->attendance_date,
            'title' => $attendance->is_ontime ? 'Hadir tepat waktu' : 'Hadir terlambat',
            'detail' => trim(($attendance->schoolClass?->name ?? 'Tanpa kelas').' · '.ucfirst($attendance->source)),
            'points' => $attendance->is_ontime ? 5 : 1,
            'type' => 'attendance',
        ])->concat($pointTransactions->map(fn (PointTransaction $transaction) => [
            'date' => $transaction->created_at,
            'title' => $transaction->description,
            'detail' => match ($transaction->type) {
                'attitude' => 'Sikap',
                'achievement' => 'Prestasi / pelanggaran',
                default => 'Poin',
            },
            'points' => $transaction->points,
            'type' => $transaction->type,
        ]))->sortByDesc('date')->take(20)->values();

        $generalPoints = (int) ($student->pointSummary?->general_points ?? 0);

        return [
            'student' => $student,
            'attendance' => [
                'total' => $attendances->count(),
                'ontime' => $attendances->where('is_ontime', true)->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'ontime_rate' => $attendances->isEmpty()
                    ? 0
                    : (int) round($attendances->where('is_ontime', true)->count() / $attendances->count() * 100),
            ],
            'level' => $this->levelProgress($generalPoints),
            'timeline' => $timeline,
            'class_history' => StudentClassHistory::query()
                ->with(['schoolClass', 'academicYear'])
                ->where('student_user_id', $student->id)
                ->when($schoolClassId, fn ($query) => $query->where('school_class_id', $schoolClassId))
                ->latest('started_on')
                ->get(),
        ];
    }

    private function scopePointsForClass($query, int $schoolClassId)
    {
        return $query->where(function ($query) use ($schoolClassId) {
            $sources = [
                Attendance::class => Attendance::query()->where('school_class_id', $schoolClassId)->select('id'),
                AttitudeAssessment::class => AttitudeAssessment::query()->where('school_class_id', $schoolClassId)->select('id'),
                AchievementAssessment::class => AchievementAssessment::query()->where('school_class_id', $schoolClassId)->select('id'),
            ];

            foreach ($sources as $sourceType => $sourceIds) {
                $query->orWhere(fn ($sourceQuery) => $sourceQuery
                    ->where('source_type', $sourceType)
                    ->whereIn('source_id', $sourceIds));
            }
        });
    }

    private function levelProgress(int $points): array
    {
        [$floor, $target, $next] = match (true) {
            $points < 0 => [-50, 0, 'Pemula'],
            $points < 50 => [0, 50, 'Berkembang'],
            $points < 100 => [50, 100, 'Teladan'],
            default => [100, 100, 'Level maksimum'],
        };

        $percentage = $target === $floor
            ? 100
            : (int) round(max(0, min(1, ($points - $floor) / ($target - $floor))) * 100);

        return [
            'current' => match (true) {
                $points >= 100 => 'Teladan',
                $points >= 50 => 'Berkembang',
                $points >= 0 => 'Pemula',
                default => 'Mulai Bertumbuh',
            },
            'next' => $next,
            'percentage' => $percentage,
            'points_to_next' => max(0, $target - $points),
        ];
    }
}

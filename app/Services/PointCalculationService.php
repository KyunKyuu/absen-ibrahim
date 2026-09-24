<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\StudentClassHistory;
use App\Models\StudentPointSummary;
use App\Models\StudentProfile;
use App\Models\User;

class PointCalculationService
{
    public function attitudePoints(int $score): int
    {
        return match ($score) {
            5 => 5,
            4 => 3,
            3 => 1,
            2 => -3,
            default => -5,
        };
    }

    public function attendancePoints(string $status, bool $isOntime): int
    {
        return match ($status) {
            'present' => $isOntime ? 5 : 1,
            'excused', 'sick' => 0,
            'late' => 1,
            default => -5,
        };
    }

    public function record(User $student, string $type, int $points, string $description, ?User $actor = null, ?object $source = null): PointTransaction
    {
        $transaction = PointTransaction::query()->create([
            'student_user_id' => $student->id,
            'actor_user_id' => $actor?->id,
            'type' => $type,
            'points' => $points,
            'source_type' => $source ? $source::class : null,
            'source_id' => $source->id ?? null,
            'description' => $description,
        ]);

        $this->refreshSummary($student);

        return $transaction;
    }

    public function refreshSummary(User $student): StudentPointSummary
    {
        $periodStart = $this->currentClassPeriodStart($student);
        $totals = PointTransaction::query()
            ->where('student_user_id', $student->id)
            ->where('id', '>', PointTransaction::query()->where('student_user_id', $student->id)->where('type', 'reset')->max('id') ?? 0)
            ->when($periodStart, fn ($query) => $query->where('created_at', '>=', $periodStart))
            ->selectRaw("sum(case when type = 'attitude' then points else 0 end) as attitude_points")
            ->selectRaw("sum(case when type = 'attendance' then points else 0 end) as attendance_points")
            ->selectRaw("sum(case when type = 'achievement' then points else 0 end) as achievement_points")
            ->first();

        $attitude = (int) ($totals->attitude_points ?? 0);
        $attendance = (int) ($totals->attendance_points ?? 0);
        $achievement = (int) ($totals->achievement_points ?? 0);
        $general = $attitude + $attendance + $achievement;

        return StudentPointSummary::query()->updateOrCreate(
            ['student_user_id' => $student->id],
            [
                'general_points' => $general,
                'attitude_points' => $attitude,
                'attendance_points' => $attendance,
                'achievement_points' => $achievement,
                'label' => $this->labelFor($general),
            ]
        );
    }

    private function currentClassPeriodStart(User $student): ?string
    {
        $lastReset = PointTransaction::query()->where('student_user_id', $student->id)
            ->where('type', 'reset')->latest('id')->first();
        if ($lastReset) {
            return $lastReset->created_at->toDateTimeString();
        }

        $history = StudentClassHistory::query()
            ->where('student_user_id', $student->id)
            ->whereNull('ended_on')
            ->latest('started_on')
            ->latest('id')
            ->first();

        if ($history) {
            return $history->started_on?->startOfDay()->toDateTimeString();
        }

        $profile = StudentProfile::query()->where('user_id', $student->id)->first();
        $classStart = $profile?->school_class_id
            ? $profile->schoolClass?->academicYear?->starts_on
            : null;

        return $classStart?->startOfDay()->toDateTimeString();
    }

    public function labelFor(int $generalPoints): string
    {
        return match (true) {
            $generalPoints >= 100 => 'Teladan',
            $generalPoints >= 50 => 'Berkembang Baik',
            $generalPoints >= 0 => 'Perlu Dipantau',
            $generalPoints > -50 => 'Perlu Pembinaan',
            default => 'Prioritas Perhatian Guru',
        };
    }
}

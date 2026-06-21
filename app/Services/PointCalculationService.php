<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\StudentPointSummary;
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
        $totals = PointTransaction::query()
            ->where('student_user_id', $student->id)
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

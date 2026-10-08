<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\StudentClassHistory;
use App\Models\StudentPointSummary;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Attendance;

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

    /** Reconcile per-absence points and a repeated-absence sanction for each group of three. */
    public function syncRepeatedAbsencePenalty(User $student): void
    {
        $periodStart = $this->currentClassPeriodStart($student);
        $absences = Attendance::query()
            ->where('student_user_id', $student->id)
            ->where('status', 'absent')
            ->when($periodStart, fn ($query) => $query->whereDate('attendance_date', '>=', substr($periodStart, 0, 10)))
            ->orderBy('attendance_date')->orderBy('id')->get();

        $periodAttendanceIds = Attendance::query()
            ->where('student_user_id', $student->id)
            ->when($periodStart, fn ($query) => $query->whereDate('attendance_date', '>=', substr($periodStart, 0, 10)))
            ->pluck('id');
        $existing = PointTransaction::query()
            ->where('student_user_id', $student->id)
            ->where('type', 'attendance')
            ->where('source_type', Attendance::class)
            ->whereIn('source_id', $periodAttendanceIds)
            ->get();

        $expected = [];
        foreach ($absences->values() as $index => $absence) {
            $expected[] = [$absence->id, 'Alfa tanpa keterangan', -5];
            if (($index + 1) % 3 === 0) {
                $expected[] = [$absence->id, 'Sanksi alfa berulang (setiap 3 kali)', -25];
            }
        }

        $remaining = collect($expected)->keyBy(fn ($item) => $item[0].'|'.$item[1]);
        foreach ($existing as $transaction) {
            $isAbsencePoint = $transaction->description === 'Alfa tanpa keterangan';
            $isGroupPenalty = $transaction->description === 'Sanksi alfa berulang (setiap 3 kali)';
            if ($isAbsencePoint || $isGroupPenalty) {
                $key = $transaction->source_id.'|'.$transaction->description;
                $item = $remaining->get($key);
                if ($item && $transaction->points === $item[2]) {
                    $remaining->forget($key);
                } else {
                    $transaction->delete();
                }
            }
        }

        foreach ($remaining as [$attendanceId, $description, $value]) {
            PointTransaction::query()->create([
                'student_user_id' => $student->id,
                'type' => 'attendance',
                'points' => $value,
                'source_type' => Attendance::class,
                'source_id' => $attendanceId,
                'description' => $description,
            ]);
        }

        $this->refreshSummary($student);
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

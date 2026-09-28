<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendancePermission extends Model
{
    protected $fillable = [
        'student_user_id',
        'submitted_by_user_id',
        'school_class_id',
        'academic_year_id',
        'semester_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment_path',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'sick' => 'Sakit',
            'excused' => 'Izin',
            default => 'Lainnya / Dispensasi',
        };
    }

    public function typeEmoji(): string
    {
        return match ($this->type) {
            'sick' => '🤒',
            'excused' => '📝',
            default => '📌',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function durationDays(): int
    {
        if (! $this->start_date || ! $this->end_date) {
            return 1;
        }

        return (int) $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function includesDate(Carbon|string $date): bool
    {
        $d = Carbon::parse($date)->toDateString();
        $start = $this->start_date?->toDateString();
        $end = $this->end_date?->toDateString();

        return $start && $end && $d >= $start && $d <= $end;
    }
}

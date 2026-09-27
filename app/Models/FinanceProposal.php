<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceProposal extends Model
{
    protected $fillable = [
        'proposed_by_user_id', 'reviewed_by_user_id', 'school_class_id', 'academic_year_id',
        'title', 'description', 'billing_mode', 'amount', 'due_date', 'status',
        'review_notes', 'reviewed_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'due_date' => 'date',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposed_by_user_id');
    }

    public function bills()
    {
        return $this->hasMany(StudentBill::class, 'finance_proposal_id');
    }

    public function getTotalCollectedAmountAttribute(): int
    {
        return (int) $this->bills()->sum('paid_amount');
    }

    public function getTotalBilledAmountAttribute(): int
    {
        return (int) $this->bills()->sum('amount');
    }

    public function getPaidBillsCountAttribute(): int
    {
        return $this->bills()->where('status', 'paid')->count();
    }

    public function getTotalBillsCountAttribute(): int
    {
        return $this->bills()->count();
    }

    public function proposerRoleLabel(): string
    {
        if (! $this->proposer) {
            return '-';
        }

        if ($this->schoolClass && $this->schoolClass->homeroom_teacher_id === $this->proposer->id) {
            return 'Wali Kelas';
        }

        if ($this->schoolClass && $this->schoolClass->class_leader_user_id === $this->proposer->id) {
            return 'Ketua Kelas';
        }

        if ($this->proposer->hasRole('teacher')) {
            return 'Wali Kelas';
        }

        if ($this->proposer->hasRole('student')) {
            return 'Ketua Kelas';
        }

        return 'Pengusul';
    }
}

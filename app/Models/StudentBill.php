<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBill extends Model
{
    protected $fillable = [
        'student_user_id', 'school_class_id', 'academic_year_id', 'school_fee_type_id',
        'finance_proposal_id', 'created_by_user_id', 'title', 'billing_period', 'amount',
        'paid_amount', 'due_date', 'status', 'is_arrears',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_amount' => 'integer',
            'due_date' => 'date',
            'is_arrears' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function payments()
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function getOutstandingAmountAttribute(): int
    {
        return max(0, $this->amount - $this->paid_amount);
    }
}

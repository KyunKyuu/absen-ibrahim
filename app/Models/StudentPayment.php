<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    protected $fillable = [
        'student_bill_id', 'submitted_by_user_id', 'received_by_user_id', 'reviewed_by_user_id',
        'amount', 'status', 'paid_on', 'payment_method', 'reference', 'proof_path',
        'notes', 'review_notes', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'paid_on' => 'date', 'reviewed_at' => 'datetime'];
    }

    public function bill()
    {
        return $this->belongsTo(StudentBill::class, 'student_bill_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}

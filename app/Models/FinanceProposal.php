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
}

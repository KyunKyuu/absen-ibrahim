<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttitudeAssessment extends Model
{
    protected $fillable = [
        'teacher_user_id',
        'student_user_id',
        'school_class_id',
        'subject_id',
        'semester_id',
        'aspect',
        'score',
        'credit_cost',
        'points',
        'notes',
        'assessed_on',
    ];

    protected function casts(): array
    {
        return ['assessed_on' => 'date'];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}

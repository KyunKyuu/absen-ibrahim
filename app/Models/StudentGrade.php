<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    protected $fillable = ['grade_assessment_id', 'student_user_id', 'score', 'notes'];

    protected function casts(): array
    {
        return ['score' => 'decimal:2'];
    }

    public function assessment()
    {
        return $this->belongsTo(GradeAssessment::class, 'grade_assessment_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}

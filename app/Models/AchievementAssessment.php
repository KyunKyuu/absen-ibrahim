<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchievementAssessment extends Model
{
    protected $fillable = [
        'teacher_user_id',
        'student_user_id',
        'school_class_id',
        'subject_id',
        'title',
        'category',
        'points',
        'notes',
        'awarded_on',
    ];

    protected function casts(): array
    {
        return ['awarded_on' => 'date'];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

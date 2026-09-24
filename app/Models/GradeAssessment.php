<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeAssessment extends Model
{
    public const KINDS = ['assignment' => 'Tugas', 'quiz' => 'Ulangan harian', 'midterm' => 'UTS', 'final' => 'UAS'];

    protected $fillable = ['teacher_user_id', 'school_class_id', 'subject_id', 'semester_id', 'kind', 'title', 'assessed_on'];

    protected function casts(): array
    {
        return ['assessed_on' => 'date'];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function grades()
    {
        return $this->hasMany(StudentGrade::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassHistory extends Model
{
    protected $fillable = ['student_user_id', 'school_class_id', 'academic_year_id', 'started_on', 'ended_on'];

    protected function casts(): array
    {
        return ['started_on' => 'date', 'ended_on' => 'date'];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}

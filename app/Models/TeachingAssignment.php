<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingAssignment extends Model
{
    protected $fillable = ['teacher_user_id', 'school_class_id', 'subject_id'];

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPointSummary extends Model
{
    protected $fillable = [
        'student_user_id',
        'general_points',
        'attitude_points',
        'attendance_points',
        'achievement_points',
        'label',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}

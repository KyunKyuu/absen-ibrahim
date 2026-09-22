<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_user_id',
        'school_class_id',
        'academic_year_id',
        'semester_id',
        'created_by_user_id',
        'attendance_date',
        'checked_in_at',
        'status',
        'source',
        'latitude',
        'longitude',
        'distance_meters',
        'location_accuracy_meters',
        'is_within_radius',
        'is_ontime',
        'device_identifier',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'is_within_radius' => 'boolean',
            'is_ontime' => 'boolean',
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

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}

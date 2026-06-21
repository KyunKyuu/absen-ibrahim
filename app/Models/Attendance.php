<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_user_id',
        'created_by_user_id',
        'attendance_date',
        'checked_in_at',
        'status',
        'source',
        'latitude',
        'longitude',
        'distance_meters',
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
}

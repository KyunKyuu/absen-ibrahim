<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name',
        'latitude',
        'longitude',
        'attendance_radius_meters',
        'max_location_accuracy_meters',
        'attendance_open_time',
        'start_time',
        'late_after',
        'attendance_close_time',
        'point_rules',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'point_rules' => 'array',
        ];
    }

    public static function active(): self
    {
        return static::query()->firstOrCreate([], [
            'school_name' => 'Sekolah',
            'attendance_radius_meters' => 100,
            'max_location_accuracy_meters' => 100,
            'attendance_open_time' => '05:00:00',
            'start_time' => '07:00:00',
            'late_after' => '07:00:00',
            'attendance_close_time' => '10:00:00',
            'point_rules' => [],
        ]);
    }
}

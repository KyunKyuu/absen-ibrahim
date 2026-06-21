<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IotAttendanceLog extends Model
{
    protected $fillable = [
        'iot_device_id',
        'device_identifier',
        'fingerprint_user_id',
        'student_user_id',
        'scanned_at',
        'status',
        'payload',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'payload' => 'array',
        ];
    }
}

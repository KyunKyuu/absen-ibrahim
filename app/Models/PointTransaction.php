<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'student_user_id',
        'actor_user_id',
        'type',
        'points',
        'source_type',
        'source_id',
        'description',
    ];
}

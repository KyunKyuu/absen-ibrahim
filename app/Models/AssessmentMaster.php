<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentMaster extends Model
{
    protected $fillable = ['kind', 'group_name', 'name', 'points', 'sanction', 'score', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
}

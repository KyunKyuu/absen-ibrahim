<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['name', 'code', 'description', 'criteria'];

    protected function casts(): array
    {
        return ['criteria' => 'array'];
    }
}

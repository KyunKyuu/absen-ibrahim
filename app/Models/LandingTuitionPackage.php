<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingTuitionPackage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}

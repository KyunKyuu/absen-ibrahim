<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolFeeType extends Model
{
    protected $fillable = ['name', 'code', 'billing_cycle', 'default_amount', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'default_amount' => 'integer'];
    }
}

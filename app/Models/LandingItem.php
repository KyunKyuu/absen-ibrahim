<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LandingItem extends Model
{
    public const KINDS = ['program', 'activity', 'testimonial', 'statistic'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'date',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $query) => $query
                ->whereNull('published_at')
                ->orWhereDate('published_at', '<=', today()));
    }
}

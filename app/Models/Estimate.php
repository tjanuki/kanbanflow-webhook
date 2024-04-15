<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    public function setEstimatedHoursAttribute(float $value): void
    {
        $this->attributes['estimated_seconds'] = $value * 3600;
    }

    public function getEstimatedHoursAttribute(): float|int
    {
        return $this->estimated_seconds / 3600;
    }
}

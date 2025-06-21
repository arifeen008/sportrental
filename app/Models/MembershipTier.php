<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tier_name',
        'price',
        'included_hours',
        'validity_days',
        'applicable_days',
        'normal_hours_start',
        'normal_hours_end',
        'overtime_hour_multiplier',
        'special_perks',
        'conditions',
    ];
}
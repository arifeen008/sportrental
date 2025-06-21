<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_type',
        'package_name',
        'base_price',
        'base_start_time',
        'base_end_time',
        'overtime_price_per_hour_per_field',
        'overtime_max_end_time',
    ];
}
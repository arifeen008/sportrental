<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'size',
        'capacity',
        'status',
    ];

    /**
     * Get the hourly rates for the field type.
     */
    public function hourlyRates()
    {
        return $this->hasMany(HourlyRate::class);
    }

    /**
     * Get the bookings for the field type.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
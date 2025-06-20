<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'price',
        'type_category',
        'field_type_applicable',
        'day_type_applicable',
        'start_time_applicable',
        'end_time_applicable',
        'num_fields_applicable',
        'is_membership',
        'membership_duration_hours',
        'membership_offpeak_multiplier',
        'notes_conditions',
    ];

    protected $casts = [
        'start_time_applicable' => 'datetime', // Cast to Carbon instance for easier time comparisons
        'end_time_applicable' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

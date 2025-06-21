<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
        'price_calculation_details' => 'array',
        'password' => 'hashed',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the field type for the booking.
     */
    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }

    /**
     * Get the user membership associated with the booking (if any).
     * หมายเหตุ: ความสัมพันธ์นี้จะใช้ได้เมื่อมีการสร้างตารางและ Model 'UserMembership' ในอนาคต
     */
    // public function userMembership()
    // {
    //     return $this->belongsTo(UserMembership::class);
    // }
}
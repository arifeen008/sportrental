<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'booking_type_id',
        'field_id', // Note: This is nullable in the migration
        'start_time',
        'end_time',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'num_participants',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function bookingType()
    {
        return $this->belongsTo(BookingType::class);
    }
}

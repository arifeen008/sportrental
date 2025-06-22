<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * การตั้งค่านี้สำคัญมาก มันอนุญาตให้ทุกคอลัมน์ (รวมถึง user_id) ถูกบันทึกได้
     */
    protected $guarded = ['id'];

    protected $casts = [
        'booking_date'              => 'date',
        'price_calculation_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }
}

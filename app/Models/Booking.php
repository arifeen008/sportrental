<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     * การตั้งค่านี้หมายถึง "อนุญาตให้กรอก/อัปเดตข้อมูลได้ทุกคอลัมน์ ยกเว้น id"
     * ซึ่งจะแก้ปัญหาของเราได้ทันที และป้องกันปัญหาแบบเดียวกันในอนาคต
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date'              => 'date',
        'price_calculation_details' => 'array',
        'expires_at'                => 'datetime',
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

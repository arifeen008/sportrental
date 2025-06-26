<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded = ['id']; // อนุญาตให้ Mass Assign ได้ทุกฟิลด์

    // กำหนดให้ cast วันที่เป็น Carbon object
    protected $casts = ['published_at' => 'datetime'];

    // สร้างความสัมพันธ์ว่า Post นี้เป็นของ User คนไหน
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }
}

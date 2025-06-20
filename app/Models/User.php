<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_card',      // ตรวจสอบให้แน่ใจว่ามีบรรทัดนี้
        'phone_number', // ตรวจสอบให้แน่ใจว่ามีบรรทัดนี้
        'role',         // ตรวจสอบให้แน่ใจว่ามีบรรทัดนี้
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Check if the user is an admin.
     * This method is crucial for 'isAdmin()' to work in LoginController and Gates.
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin'; // ตรวจจาก field ที่ใช้จริง
    }

    /**
     * Check if the user is a regular user.
     * This method is crucial for 'isUser()' to work in LoginController and Gates.
     * @return bool
     */
    public function isUser()
    {
        return $this->role === 'user'; // ตรวจจาก field ที่ใช้จริง
    }

}

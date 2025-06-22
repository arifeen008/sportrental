<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลการจองทั้งหมดที่ user_id ตรงกับ user ที่ login อยู่
        // โดยเรียงจากวันที่จองล่าสุดไปเก่าสุด
        $bookings = Booking::where('user_id', Auth::id())
                            ->with('fieldType') // โหลดข้อมูลประเภทสนามมาด้วยเพื่อลดการ query
                            ->latest('booking_date')
                            ->get();

        // ส่งข้อมูล bookings ไปที่ view 'dashboard'
        return view('user.dashboard', compact('bookings'));
    }
}

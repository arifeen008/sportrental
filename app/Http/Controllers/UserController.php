<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\UserMembership;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // public function index()
    // {
    //     $bookings         = Booking::where('user_id', Auth::id())->with('fieldType')->latest('booking_date')->paginate(15);
    //     $activeMembership = UserMembership::where('user_id', Auth::id())
    //         ->where('status', 'active')
    //         ->where('expires_at', '>', now())
    //         ->with('membershipTier') // โหลดข้อมูล Tier มาด้วย
    //         ->first();
    //     return view('user.dashboard', compact('bookings', 'activeMembership'));
    // }

    public function index()
    {
        $userId = Auth::id(); // ดึง ID ของผู้ใช้ที่ล็อกอินอยู่

        // 1. ดึงข้อมูลการจอง "ทั้งหมด" ของผู้ใช้ที่ Login อยู่
        $myBookings = Booking::where('user_id', Auth::id())
            ->with('fieldType')    // โหลดข้อมูลสนามมาด้วย
            ->latest('created_at') // เรียงตามการจองล่าสุด
            ->paginate(10);        // แบ่งหน้าแสดงผลทีละ 10 รายการ

        // 2. ดึงข้อมูลบัตรสมาชิกที่ยัง Active อยู่ของผู้ใช้
        $activeMembership = UserMembership::where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('remaining_hours', '>', 0)
            ->with('membershipTier')
            ->first();

        // 3. ดึงข้อมูลการจองที่ "ยืนยันแล้ว" ทั้งหมดในอนาคต (สำหรับแสดงเป็นตารางรวม)
        $confirmedBookings = Booking::where('payment_status', 'paid')
            ->where('booking_date', '>=', today())
            ->with('fieldType')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(20) // แสดงแค่ 20 รายการล่าสุดเพื่อไม่ให้ตารางยาวเกินไป
            ->get();

        // 4. ส่งตัวแปรทั้งหมดไปที่ view
        return view('user.dashboard', [
            'myBookings'        => $myBookings,
            'activeMembership'  => $activeMembership,
            'confirmedBookings' => $confirmedBookings,
        ]);
    }
}

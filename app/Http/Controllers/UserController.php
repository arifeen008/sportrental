<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\UserMembership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

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

    /**
     * แสดงหน้าฟอร์มสำหรับแก้ไขโปรไฟล์
     */
    public function edit(Request $request)
    {
        return view('user.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * อัปเดตข้อมูลทั่วไป (ชื่อ, อีเมล)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->fill($validated);

        // ถ้ามีการเปลี่ยนอีเมล ให้สถานะการยืนยันอีเมลกลับเป็น null
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('user.profile.edit')->with('success', 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }

    /**
     * อัปเดตรหัสผ่านใหม่
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profile.edit')->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
    }
}

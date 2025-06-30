<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. ดึงข้อมูลการจอง "ทั้งหมด" ของผู้ใช้ที่ Login อยู่เท่านั้น
        $myBookings = Booking::where('user_id', $userId)
            ->with('fieldType')
            ->latest('created_at') // เรียงตามการจองล่าสุด
            ->paginate(10);        // แบ่งหน้าแสดงผล

        // 2. ดึงข้อมูลบัตรสมาชิกที่ยัง Active อยู่ของผู้ใช้
        $activeMembership = UserMembership::where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('remaining_hours', '>', 0)
            ->with('membershipTier')
            ->first();

        $confirmedBookings = Booking::where('status', 'paid')
            ->where('booking_date', '>=', today())
            ->with('fieldType')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(10) // แสดงแค่ 10 รายการล่าสุด
            ->get();

        // 3. ส่งแค่ 3 ตัวแปรนี้ไปที่ view
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
        try {
            // 1. ตรวจสอบข้อมูลพร้อมกำหนดข้อความ Error ภาษาไทย
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password'         => ['required', Password::defaults(), 'confirmed'],
            ], [
                // ข้อความสำหรับ current_password
                'current_password.required'         => 'กรุณากรอกรหัสผ่านปัจจุบันของคุณ',
                'current_password.current_password' => 'รหัสผ่านปัจจุบันที่คุณกรอกไม่ถูกต้อง',

                // ข้อความสำหรับ password
                'password.required'                 => 'กรุณากรอกรหัสผ่านใหม่',
                'password.confirmed'                => 'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
                'password.min'                      => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            ]);

            // 2. ถ้าข้อมูลถูกต้องทั้งหมด ให้อัปเดตรหัสผ่าน
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            // 3. ส่งกลับไปพร้อมข้อความ "สำเร็จ"
            return redirect()->route('profile.edit')->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
        } catch (ValidationException $e) {
            // 4. ถ้าข้อมูลไม่ถูกต้อง (เกิด ValidationException)

            // ดึงข้อความ error "แรกสุด" ที่เจอ
            $firstError = $e->validator->errors()->first();

            // Redirect กลับไปหน้าเดิมพร้อมกับข้อความ error นั้นสำหรับ SweetAlert
            return redirect()->back()->with('error', $firstError);
        }
    }
}

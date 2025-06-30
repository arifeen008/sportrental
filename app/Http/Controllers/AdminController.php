<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\UserMembership;
use Illuminate\Http\Request; // <-- อย่าลืมเพิ่ม use statement นี้
use Illuminate\Support\Facades\DB;
// <-- เพิ่ม use statement นี้

class AdminController extends Controller
{
    // ใน AdminDashboardController.php
    public function index()
    {
        // ดึงข้อมูลสำหรับ Widgets
        $bookingsTodayCount       = Booking::whereDate('booking_date', today())->count();
        $pendingVerificationCount = Booking::where('status', 'verifying')->count();
        $monthlyRevenue           = Booking::where('status', 'paid')->whereMonth('created_at', now()->month)->sum('total_price');

        // ดึงข้อมูลการจองที่ต้องจัดการ
        $actionRequiredBookings = Booking::where('status', 'verifying')->with(['user', 'fieldType'])->latest()->get();

        // เตรียมข้อมูลสำหรับ Chart.js (7 วันล่าสุด)
        $chartData = Booking::where('booking_date', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(booking_date) as date, COUNT(*) as count')
            ->groupBy('date')->orderBy('date', 'asc')->get();

        // สร้าง Label และ Value สำหรับ Chart ให้ครบ 7 วัน
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date          = today()->subDays($i);
            $chartLabels[] = thaidate('D j', $date);
            $bookingData   = $chartData->firstWhere('date', $date->format('Y-m-d'));
            $chartValues[] = $bookingData ? $bookingData->count : 0;
        }

        return view('admin.dashboard', [
            'bookingsTodayCount'       => $bookingsTodayCount,
            'pendingVerificationCount' => $pendingVerificationCount,
            'monthlyRevenue'           => $monthlyRevenue,
            'actionRequiredBookings'   => $actionRequiredBookings,
            'chartLabels'              => $chartLabels,
            'chartValues'              => $chartValues,
        ]);
    }

    // รับ $booking ที่ถูกหาเจอโดยอัตโนมัติ
    public function approve(Request $request, Booking $booking)
    {
        $booking->status = 'paid';
        $booking->status = 'confirmed';
        $booking->save();

        return redirect()->route('admin.dashboard')->with('success', 'อนุมัติการจอง #' . $booking->booking_code . ' เรียบร้อยแล้ว');
    }

    // รับ $booking ที่ถูกหาเจอโดยอัตโนมัติ
    public function reject(Request $request, Booking $booking)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $booking->status           = 'rejected';
        $booking->status           = 'cancelled';
        $booking->rejection_reason = $request->input('rejection_reason');
        $booking->save();

        return redirect()->route('admin.dashboard')->with('success', 'ปฏิเสธการจอง #' . $booking->booking_code . ' เรียบร้อยแล้ว');
    }

    /**
     * แสดงหน้าประวัติการจองทั้งหมด พร้อมการแบ่งหน้า
     */
    public function listAllBookings()
    {
        $bookings = Booking::with(['user', 'fieldType'])->latest()->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }

/**
 * แสดงหน้ารายละเอียดของการจองที่ระบุ
 */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'fieldType']);
        return view('admin.bookings.show', compact('booking'));
    }

    // ใน AdminBookingController.php

/**
 * อัปเดตวันและเวลาของการจองโดย Admin
 */
    public function rescheduleBooking(Request $request, Booking $booking)
    {
        // 1. ตรวจสอบข้อมูลที่ส่งมาจากฟอร์ม
        $validated = $request->validate([
            'new_booking_date' => 'required|date',
            'new_start_time'   => 'required|date_format:H:i',
            'new_end_time'     => 'required|date_format:H:i|after:new_start_time',
        ]);

                                                             // 2. ตรวจสอบว่าเวลาใหม่ว่างหรือไม่ (ป้องกัน Admin จองซ้อนเอง)
        $isBooked = Booking::where('id', '!=', $booking->id) // ไม่ต้องเช็คกับตัวเอง
            ->where('field_type_id', $booking->field_type_id)
            ->where('booking_date', $validated['new_booking_date'])
            ->where('status', 'paid')
            ->where('start_time', '<', $validated['new_end_time'])
            ->where('end_time', '>', $validated['new_start_time'])
            ->exists();

        if ($isBooked) {
            // ถ้าเวลาใหม่ไม่ว่าง ให้ส่ง Error กลับไป
            return redirect()->back()->with('error', 'ไม่สามารถเลื่อนได้: ช่วงเวลาใหม่ที่เลือกมีผู้จองแล้ว');
        }

        // 3. อัปเดตข้อมูลการจอง
        $booking->update([
            'booking_date'      => $validated['new_booking_date'],
            'start_time'        => $validated['new_start_time'],
            'end_time'          => $validated['new_end_time'],
            'reschedule_status' => 'rescheduled', // บันทึกสถานะว่าถูกเลื่อนโดยแอดมิน
        ]);

        // อาจจะเพิ่มการแจ้งเตือนกลับไปหาลูกค้าทาง Email/LINE

        return redirect()->route('admin.booking.show', $booking)->with('success', 'แก้ไขวัน/เวลาการจองเรียบร้อยแล้ว');
    }

    /**
     * ยกเลิกการจองโดย Admin และคืนชั่วโมงให้สมาชิก (ถ้ามี)
     */
    public function cancelBooking(Booking $booking)
    {
        try {
            DB::transaction(function () use ($booking) {

                // 1. ตรวจสอบว่าเป็นการจองด้วยบัตรสมาชิกและมีชั่วโมงให้คืนหรือไม่
                if ($booking->booking_type === 'membership' && $booking->hours_deducted > 0) {

                    // ค้นหาบัตรสมาชิกที่ใช้ในการจองครั้งนี้
                    $membership = UserMembership::find($booking->user_membership_id);

                    if ($membership) {
                        // คืนชั่วโมงกลับเข้าบัตร
                        $membership->remaining_hours += $booking->hours_deducted;

                        // ถ้าบัตรเคยสถานะ 'used_up' ให้เปลี่ยนกลับเป็น 'active'
                        if ($membership->status === 'used_up') {
                            $membership->status = 'active';
                        }

                        $membership->save();
                    }
                }

                // 2. อัปเดตสถานะการจองเป็น 'cancelled'
                $booking->update([
                    'status'           => 'cancelled',
                    'rejection_reason' => 'ยกเลิกโดยแอดมิน', // เพิ่มหมายเหตุ
                ]);
            });

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการยกเลิกการจอง: ' . $e->getMessage());
        }

        return redirect()->route('admin.booking.index')->with('success', 'ยกเลิกการจอง #' . $booking->booking_code . ' เรียบร้อยแล้ว');
    }

}

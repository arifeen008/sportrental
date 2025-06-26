<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

// <-- อย่าลืม import Model Booking
// ใช้ Auth เพื่อเข้าถึงข้อมูลผู้ใช้

class AdminController extends Controller
{
    // ใน AdminDashboardController.php
    public function index()
    {
        // ดึงข้อมูลสำหรับ Widgets
        $bookingsTodayCount       = Booking::whereDate('booking_date', today())->count();
        $pendingVerificationCount = Booking::where('payment_status', 'verifying')->count();
        $monthlyRevenue           = Booking::where('payment_status', 'paid')->whereMonth('created_at', now()->month)->sum('total_price');

        // ดึงข้อมูลการจองที่ต้องจัดการ
        $actionRequiredBookings = Booking::where('payment_status', 'verifying')->with(['user', 'fieldType'])->latest()->get();

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
        $booking->payment_status = 'paid';
        $booking->status         = 'confirmed';
        $booking->save();

        return redirect()->route('admin.dashboard')->with('success', 'อนุมัติการจอง #' . $booking->booking_code . ' เรียบร้อยแล้ว');
    }

    // รับ $booking ที่ถูกหาเจอโดยอัตโนมัติ
    public function reject(Request $request, Booking $booking)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $booking->payment_status   = 'rejected';
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

}

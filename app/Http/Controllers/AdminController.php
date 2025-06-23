<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

// <-- อย่าลืม import Model Booking
// ใช้ Auth เพื่อเข้าถึงข้อมูลผู้ใช้

class AdminController extends Controller
{
    /**
     * แสดงหน้า Admin Dashboard
     */
    public function index()
    {

        $bookings = Booking::with(['user', 'fieldType'])
            ->orderByRaw("
                CASE
                    WHEN payment_status = 'verifying' THEN 1
                    WHEN payment_status = 'unpaid' THEN 2
                    WHEN payment_status = 'paid' THEN 3
                    ELSE 4
                END
            ")

            ->latest('booking_date')
            ->paginate(15);

        return view('admin.dashboard', compact('bookings'));
    }

    // รับ $booking ที่ถูกหาเจอโดยอัตโนมัติ
    public function approve(Request $request, Booking $booking)
    {
        $booking->payment_status = 'paid';
        $booking->status         = 'confirmed';
        $booking->save();

        return redirect()->route('admin.dashboard')->with('success', 'อนุมัติการจอง #' . $booking->booking_code  . ' เรียบร้อยแล้ว');
    }

    // รับ $booking ที่ถูกหาเจอโดยอัตโนมัติ
    public function reject(Request $request, Booking $booking)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $booking->payment_status   = 'rejected';
        $booking->status           = 'cancelled';
        $booking->rejection_reason = $request->input('rejection_reason');
        $booking->save();

        return redirect()->route('admin.dashboard')->with('success', 'ปฏิเสธการจอง #' . $booking->booking_code  . ' เรียบร้อยแล้ว');
    }

}

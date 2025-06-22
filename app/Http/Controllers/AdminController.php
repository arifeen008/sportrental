<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking; // <-- อย่าลืม import Model Booking
// ใช้ Auth เพื่อเข้าถึงข้อมูลผู้ใช้

class AdminController extends Controller
{
    /**
     * แสดงหน้า Admin Dashboard
     */
    public function index()
    {
        // 1. ดึงข้อมูลการจองทั้งหมดจากฐานข้อมูล
        $bookings = Booking::with(['user', 'fieldType']) // โหลดข้อมูล User และ FieldType มาพร้อมกันเพื่อประสิทธิภาพ
            
            // 2. จัดเรียงข้อมูลให้รายการที่ "ต้องจัดการ" ขึ้นมาอยู่บนสุด
            ->orderByRaw("
                CASE 
                    WHEN payment_status = 'verifying' THEN 1
                    WHEN payment_status = 'unpaid' THEN 2
                    WHEN payment_status = 'paid' THEN 3
                    ELSE 4 
                END
            ")
            
            // 3. จากนั้นเรียงตามวันที่จองล่าสุด
            ->latest('booking_date')
            
            // 4. แบ่งหน้าแสดงผล (แสดงทีละ 15 รายการ)
            ->paginate(15);

        // 5. ส่งตัวแปร $bookings ไปยัง View
        return view('admin.dashboard', compact('bookings'));
    }

    

}

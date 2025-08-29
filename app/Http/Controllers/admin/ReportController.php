<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * แสดงหน้าสรุปรายงานการจอง
     */
    public function summary(Request $request)
    {
        // 1. กำหนดช่วงเวลาเริ่มต้นและสิ้นสุด
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()));
        $endDate   = Carbon::parse($request->input('end_date', now()->endOfMonth()));

        // 2. ดึงข้อมูลการจองที่ "ยืนยันแล้ว" หรือ "จ่ายแล้ว" ในช่วงเวลาที่เลือก
        $bookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'paid'])
            ->get();

        // 3. คำนวณสรุปรายได้และชั่วโมงรวม
        $totalRevenue     = $bookings->sum('total_price');
        $depositRevenue   = $bookings->where('booking_type', 'daily_package')->sum('deposit_amount');
        $totalHoursBooked = $bookings->sum('duration_in_hours');

        // 4. สรุปรายงานตามประเภทการจอง
        $bookingsByType = $bookings->groupBy('booking_type')->map(function ($group) {
            return [
                'count'   => $group->count(),
                'revenue' => $group->sum('total_price'),
            ];
        });

        // 5. สรุปรายงานตามสนาม
        $bookingsByField = $bookings->groupBy('field_type_id')->map(function ($group) {
            // เช็คว่าเป็นการจองแบบเหมา 2 สนามหรือไม่ (field_type_id จะเป็น null)
            $fieldName = 'เหมา 2 สนาม';
            if ($group->first()->field_type_id !== null) {
                $fieldName = optional($group->first()->fieldType)->name ?? 'ไม่ระบุสนาม';
            }

            return [
                'field_name' => $fieldName,
                'count'      => $group->count(),
                'revenue'    => $group->sum('total_price'),
            ];
        })->sortByDesc('revenue'); // เรียงลำดับจากรายได้มากไปน้อย

        return view('admin.reports.summary', [
            'totalRevenue'     => $totalRevenue,
            'depositRevenue'   => $depositRevenue,
            'totalHoursBooked' => $totalHoursBooked,
            'bookingsByType'   => $bookingsByType,
            'bookingsByField'  => $bookingsByField,
            'startDate'        => $startDate->format('Y-m-d'),
            'endDate'          => $endDate->format('Y-m-d'),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * แสดงหน้าสรุปรายงานการจอง
     */
    public function summary(Request $request)
    {
        // 1. กำหนดช่วงเวลาเริ่มต้นและสิ้นสุด
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()));

        // 2. ดึงข้อมูลการจองที่ "ยืนยันแล้ว" หรือ "จ่ายแล้ว" ในช่วงเวลาที่เลือก
        $bookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
                            ->whereIn('status', ['confirmed', 'paid'])
                            ->get();

        // 3. คำนวณสรุปรายได้
        $totalRevenue = $bookings->sum('total_price');
        $depositRevenue = $bookings->where('booking_type', 'daily_package')->sum('deposit_amount');
        $hourlyRevenue = $bookings->where('booking_type', 'hourly')->sum('total_price');
        $membershipUsed = $bookings->where('booking_type', 'membership')->count();

        // 4. สรุปรายงานตามประเภทการจอง (ใช้ Collection GroupBy)
        $bookingsByType = $bookings->groupBy('booking_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum('total_price'),
            ];
        });

        // 5. สรุปรายงานตามสนาม
        $bookingsByField = $bookings->groupBy('field_type_id')->map(function ($group) {
            return [
                'field_name' => optional($group->first()->fieldType)->name ?? 'เหมา 2 สนาม',
                'count' => $group->count(),
                'revenue' => $group->sum('total_price'),
            ];
        });

        return view('admin.reports.summary', [
            'totalRevenue' => $totalRevenue,
            'depositRevenue' => $depositRevenue,
            'hourlyRevenue' => $hourlyRevenue,
            'membershipUsed' => $membershipUsed,
            'bookingsByType' => $bookingsByType,
            'bookingsByField' => $bookingsByField,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }
}
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HourlyRate;
use App\Models\MembershipTier;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $latestPosts = Post::where('status', 'published')
            ->latest('published_at')
            ->take(3)
            ->get();
        $upcomingBookings = Booking::where('status', 'paid')
            ->whereBetween('booking_date', [today(), today()->addDays(7)])
            ->with('fieldType')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('welcome', compact('latestPosts', 'upcomingBookings'));
    }

    public function pricing()
    {
        // 1. ดึงข้อมูลเรทราคารายชั่วโมง
        $hourlyRates = HourlyRate::orderBy('start_time')->get()->groupBy(['day_group', 'start_time']);

        // 2. ดึงข้อมูลราคาเหมาวัน
        // $packageRates = PackageRate::orderBy('price')->get()->groupBy('rental_type');
        $packageRates = 0;
        // 3. ดึงข้อมูลบัตรสมาชิก
        $membershipTiers = MembershipTier::orderBy('price')->get();

        // 4. ส่งข้อมูลทั้งหมดไปยัง View
        return view('pricing', compact('hourlyRates', 'packageRates', 'membershipTiers'));
    }
}

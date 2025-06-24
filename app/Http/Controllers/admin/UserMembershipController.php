<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\User;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserMembershipController extends Controller
{

    // ใน UserMembershipController.php

    public function index(Request $request)
    {
        // เริ่มต้น Query ที่ Model User
        $query = User::query()->where('role', 'user');

        // ตรวจสอบว่ามีการค้นหาเข้ามาหรือไม่
        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // ดึงข้อมูล User พร้อมกับข้อมูลบัตรสมาชิกทั้งหมด (Eager Loading)
        // และจัดเรียงตามชื่อ แล้วแบ่งหน้า (Paginate)
        $users = $query->with(['userMemberships', 'userMemberships.membershipTier'])
            ->orderBy('name')
            ->paginate(15); // แสดงผลหน้าละ 15 คน

        return view('admin.memberships.index', compact('users'));
    }
    /**
     * แสดงฟอร์มสำหรับสร้างบัตรสมาชิกใหม่
     */
    public function create()
    {
                                                     // ดึงข้อมูลทั้งหมดที่จำเป็นสำหรับฟอร์ม
        $users = User::where('role', 'user')->get(); // ดึงเฉพาะ user ทั่วไป
        $tiers = MembershipTier::all();

        return view('admin.memberships.create', compact('users', 'tiers'));
    }

    /**
     * จัดเก็บบัตรสมาชิกที่สร้างใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'            => 'required|exists:users,id',
            'membership_tier_id' => 'required|exists:membership_tiers,id',
        ]);

        // 1. ดึงข้อมูลประเภทบัตร (tier) เพื่อเอาจำนวนชั่วโมงและวันหมดอายุ
        $tier = MembershipTier::findOrFail($request->membership_tier_id);

        // 2. สร้างบัตรสมาชิกใหม่
        UserMembership::create([
            'user_id'            => $request->user_id,
            'membership_tier_id' => $request->membership_tier_id,
            'card_number'        => 'MEM-' . strtoupper(Str::random(8)), // สร้างเลขบัตรแบบสุ่ม
            'initial_hours'      => $tier->included_hours,
            'remaining_hours'    => $tier->included_hours,                // ชั่วโมงเริ่มต้นและคงเหลือเท่ากัน
            'activated_at'       => now(),                                // เริ่มใช้งานทันที
            'expires_at'         => now()->addDays($tier->validity_days), // คำนวณวันหมดอายุ
            'status'             => 'active',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'ออกบัตรสมาชิกเรียบร้อยแล้ว');
    }
}

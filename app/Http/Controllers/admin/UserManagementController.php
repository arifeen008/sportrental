<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * แสดงหน้ารายชื่อสมาชิกทั้งหมด พร้อมค้นหาและแบ่งหน้า
     */
    public function index(Request $request)
    {
        $query = User::query()->where('role', 'user');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(fn($q) => $q->where('name', 'like', "%{$searchTerm}%")->orWhere('email', 'like', "%{$searchTerm}%"));
        }

        $users = $query->withCount(['bookings', 'userMemberships']) // นับจำนวนการจองและบัตร
                       ->orderBy('name')
                       ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * แสดงหน้ารายละเอียดของสมาชิกคนเดียว พร้อมประวัติทั้งหมด
     */
    public function show(User $user)
    {
        // โหลดข้อมูลที่เกี่ยวข้องทั้งหมดมาพร้อมกัน (Eager Loading)
        $user->load([
            'bookings' => fn($query) => $query->with('fieldType')->latest(),
            'userMemberships' => fn($query) => $query->with('membershipTier')->latest()
        ]);

        return view('admin.users.show', compact('user'));
    }
}
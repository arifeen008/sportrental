<?php
namespace App\Http\Controllers;

// ใช้ Auth เพื่อเข้าถึงข้อมูลผู้ใช้

class AdminController extends Controller
{
    /**
     * แสดงหน้า Admin Dashboard
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    public function rentSchedule()
    {
        return view('admin.rent-schedule');
    }

    public function members()
    {
        return view('admin.members');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function rentRequests()
    {
        return view('admin.rent-requests');
    }

}

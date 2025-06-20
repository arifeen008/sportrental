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

    

}

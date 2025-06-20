<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create()
    {
        
        return view('user.booking.create');
    }

    public function confirm(Request $request)
    {
        return view(('user.booking.confirm'));
    }
    public function store(Request $request)
    {
        dd($request->all());
        return view('user.dashboard');
    }
}

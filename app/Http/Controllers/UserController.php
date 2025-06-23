<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())->with('fieldType')->latest('booking_date')->paginate(15);
        return view('user.dashboard', compact('bookings'));
    }
}

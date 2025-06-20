<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create()
    {
        $fields = Field::where('is_active', true)->get();
        return view('user.booking.create', compact('fields'));
    }

    public function store(Request $request)
    {
        dd($request->all());
        
    }
}

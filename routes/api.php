<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API สำหรับเช็คว่าช่วงเวลาที่เลือก ว่างหรือไม่
Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('api.booking.checkAvailability');
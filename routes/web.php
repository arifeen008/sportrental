<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\admin\UserMembershipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Routes สำหรับการล็อกอิน
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes สำหรับการลงทะเบียน (ตัวเลือกเสริม)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::post('/booking/{booking}/approve', [AdminController::class, 'approve'])->name('booking.approve');
    Route::post('/booking/{booking}/reject', [AdminController::class, 'reject'])->name('booking.reject');

    Route::get('/memberships', [UserMembershipController::class, 'index'])->name('memberships.index');
    Route::get('/memberships/create', [UserMembershipController::class, 'create'])->name('memberships.create');
    Route::post('/memberships', [UserMembershipController::class, 'store'])->name('memberships.store');
});

// user routes
Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    // Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');

    Route::get('/hourly', [BookingController::class, 'createHourly'])->name('create.hourly');
    Route::get('/package', [BookingController::class, 'createPackage'])->name('create.package');
    Route::get('/membership', [BookingController::class, 'createMembership'])->name('create.membership');
    Route::post('/booking/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/bookings/{booking}/upload-slip', [BookingController::class, 'uploadSlip'])->name('booking.uploadSlip');

    Route::get('/membership/booking', [BookingController::class, 'createMembershipBooking'])->name('membership.booking.create');

});

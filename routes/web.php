<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FieldController;
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

    Route::get('/rent-schedule', [AdminController::class, 'rentSchedule'])->name('rent-schedule');
    Route::get('/members', [AdminController::class, 'members'])->name('members');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    Route::get('/index', [FieldController::class, 'index'])->name('field.index');
    Route::get('/add-field', [FieldController::class, 'create'])->name('add-field');
    Route::post('/add-field', [FieldController::class, 'store'])->name('field.store');
    Route::get('/edit/{id}', [FieldController::class, 'edit'])->name('edit-field');
    Route::put('/update', [FieldController::class, 'update'])->name('field.update');
    Route::delete('/field/{id}', [FieldController::class, 'destroy'])->name('delete-field');

    Route::get('/rent-requests', [AdminController::class, 'rentRequests'])->name('rent-requests');
});

// user routes
Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    Route::get('/booking', [BookingController::class, 'create'])->name('booking.form');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/rent-field', [UserController::class, 'rentField'])->name('rent-field');
});

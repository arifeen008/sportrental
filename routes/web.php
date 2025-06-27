<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\MembershipPurchaseController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\UserMembershipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $latestPosts = Post::where('status', 'published')
        ->latest('published_at')
        ->take(3)
        ->get();
    return view('welcome', compact('latestPosts'));
});
// Routes สำหรับการล็อกอิน
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'storeNewPassword'])->name('password.store');
});

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

    Route::get('/bookings', [AdminController::class, 'listAllBookings'])->name('booking.index');
    Route::get('/bookings/{booking}', [AdminController::class, 'show'])->name('booking.show');
    Route::resource('posts', PostController::class);

    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [MembershipPurchaseController::class, 'index'])->name('index');
        Route::post('/{purchase}/approve', [MembershipPurchaseController::class, 'approve'])->name('approve');
        Route::post('/{purchase}/reject', [MembershipPurchaseController::class, 'reject'])->name('reject');
    });

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
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
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/membership/booking', [BookingController::class, 'createMembershipBooking'])->name('membership.booking.create');

    Route::get('/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('password.update');

    // Route สำหรับแสดงหน้าเลือกซื้อบัตร
    Route::get('/purchase', [MembershipController::class, 'index'])->name('purchase.index');

    // Route สำหรับดำเนินการ "ซื้อ" (สร้างบัตร)
    Route::post('/purchase', [MembershipController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{purchase}', [MembershipController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{purchase}/upload-slip', [MembershipController::class, 'uploadSlip'])->name('purchase.uploadSlip');

});

<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * แสดงฟอร์มลงทะเบียน
     */
    public function showRegisterForm()
    {
        return view('register');
    }

    /**
     * ดำเนินการลงทะเบียน
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'phone_number'          => ['required', 'string', 'max:10'],
            'id_card'               => ['required', 'string', 'max:13'],
        ]);

        User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => 'user',
            'phone_number' => $request->phone_number,
            'id_card'      => $request->id_card,
        ]);

        return redirect('/login')->with('success', 'Registration successful! Please login.');
    }

    /**
     * แสดงหน้าฟอร์มสำหรับขอลิงก์รีเซ็ตรหัสผ่าน
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * ส่งอีเมลพร้อมลิงก์รีเซ็ตรหัสผ่าน
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));

        return $status == Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
    }

    /**
     * แสดงหน้าฟอร์มสำหรับตั้งรหัสผ่านใหม่
     */
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * จัดการการตั้งรหัสผ่านใหม่
     */
    public function storeNewPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        return $status == Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
    }
}

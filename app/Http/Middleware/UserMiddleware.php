<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'user') {
            return $next($request);
        }

        return redirect('/login')->withErrors(['access' => 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้']);
    }
}

<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Make sure App\Models\User is imported

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        Gate::define('admin', function (User $user) {
            return $user->isAdmin(); // ใช้เมธอด isAdmin()
        });

        Gate::define('user', function (User $user) {
            return $user->isUser(); // ใช้เมธอด isUser()
        });
    }
}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <title>Admin Panel - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 260px;
            background-color: #212529;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: #495057;
        }

        .content {
            flex-grow: 1;
            padding: 2rem;
        }

        .card.border-left-primary {
            border-left: .25rem solid var(--bs-primary) !important;
        }

        .card.border-left-success {
            border-left: .25rem solid var(--bs-success) !important;
        }

        .card.border-left-warning {
            border-left: .25rem solid var(--bs-warning) !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="sidebar p-3 d-flex flex-column flex-shrink-0">
        <a href="{{ route('admin.dashboard') }}"
            class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Admin Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> แดชบอร์ด
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('admin.booking.index') }}" class="nav-link text-white">
                    <i class="fas fa-history fa-fw me-2"></i> ประวัติการจอง
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('admin.memberships.index') }}" class="nav-link text-white {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}">
                    <i class="fas fa-users fa-fw me-2"></i> ข้อมูลสมาชิก
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                data-bs-toggle="dropdown">
                <strong>{{ Auth::user()->name }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                <li><a class="dropdown-item" href="/" target="_blank">ดูหน้าเว็บ</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">@csrf<a class="dropdown-item" href="#" onclick="event.preventDefault();this.closest('form').submit();">ออกจากระบบ</a></form>
                </li>
            </ul>
        </div>
    </div>

    <main class="content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('layouts.partials.sweetalert')
    @stack('scripts')
</body>

</html>

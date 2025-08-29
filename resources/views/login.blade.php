@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .login-card {
            border: none;
            border-radius: 1rem;
        }
    </style>
@endpush

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg login-card">
                <div class="card-header text-center bg-primary text-white p-4">
                    <h3 class="mb-0">
                        <i class="fas fa-futbol me-2"></i>
                        เข้าสู่ระบบ
                    </h3>
                    <p class="mb-0 mt-1 small">ยินดีต้อนรับสู่ SKF STADIUM</p>
                </div>
                <div class="card-body p-5">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        {{-- Email Address --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">อีเมล</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror" required autofocus
                                       autocomplete="email" placeholder="you@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label for="password" class="form-label">รหัสผ่าน</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required
                                       autocomplete="current-password" placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Remember Me & Forgot Password --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                <label class="form-check-label" for="remember_me">
                                    จดจำฉันไว้ในระบบ
                                </label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="text-decoration-none">
                                    ลืมรหัสผ่าน?
                                </a>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">เข้าสู่ระบบ</button>
                        </div>

                        {{-- Register Link --}}
                        <div class="mt-4 text-center">
                            <p class="text-muted">ยังไม่มีบัญชี? <a href="{{ route('register') }}" class="fw-bold text-decoration-none">สมัครสมาชิกที่นี่</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .register-card {
            border: none;
            border-radius: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg register-card">
                    <div class="card-header text-center bg-success text-white p-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            สร้างบัญชีผู้ใช้ใหม่
                        </h3>
                        <p class="mb-0 mt-1 small">สมัครสมาชิกเพื่อเริ่มใช้งานระบบจองสนาม</p>
                    </div>
                    <div class="card-body p-5">

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row g-3">
                                {{-- Name --}}
                                <div class="col-md-12">
                                    <label for="name" class="form-label">ชื่อ-สกุล</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                                            class="form-control @error('name') is-invalid @enderror" required autofocus
                                            autocomplete="name" placeholder="กรอกชื่อและนามสกุล">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-12">
                                    <label for="email" class="form-label">อีเมล</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror" required
                                            autocomplete="email" placeholder="you@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ID Card Number --}}
                                <div class="col-md-6">
                                    <label for="id_card" class="form-label">เลขบัตรประชาชน</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" id="id_card" name="id_card" value="{{ old('id_card') }}"
                                            class="form-control @error('id_card') is-invalid @enderror" required
                                            autocomplete="off" maxlength="13" pattern="\d{13}"
                                            title="กรุณากรอกเลขบัตรประชาชน 13 หลัก">
                                        @error('id_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Phone Number --}}
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">เบอร์โทรศัพท์</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" id="phone_number" name="phone_number"
                                            value="{{ old('phone_number') }}"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            autocomplete="tel" maxlength="10" pattern="\d{10}"
                                            title="กรุณากรอกเบอร์โทรศัพท์ 10 หลัก">
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="col-md-6">
                                    <label for="password" class="form-label">รหัสผ่าน</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" required
                                            autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="form-control" required autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg">สมัครสมาชิก</button>
                            </div>

                            {{-- Login Link --}}
                            <div class="mt-4 text-center">
                                <p class="text-muted">เป็นสมาชิกอยู่แล้ว? <a href="{{ route('login') }}"
                                        class="fw-bold text-decoration-none">เข้าสู่ระบบที่นี่</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

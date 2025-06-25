@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>ลืมรหัสผ่าน</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4">
                            ไม่มีปัญหา! เพียงกรอกอีเมลของคุณที่ลงทะเบียนไว้ แล้วเราจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ไปให้
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">อีเมล</label>
                                <input id="email" type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    required autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    ส่งลิงก์รีเซ็ตรหัสผ่าน
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

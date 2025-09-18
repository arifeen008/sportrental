@extends('layouts.app')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4>ข้อมูลส่วนตัว</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('user.profile.update') }}">
                            @csrf
                            @method('patch')
                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่อ</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>เปลี่ยนรหัสผ่าน</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('user.password.update') }}">
                            @csrf
                            @method('put')
                            <div class="mb-3">
                                <label for="current_password" class="form-label">รหัสผ่านปัจจุบัน</label>
                                <input type="password" name="current_password" id="current_password" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">รหัสผ่านใหม่</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
                        </form>
                    </div>
                </div>

                {{-- ปุ่มย้อนกลับอยู่นอกฟอร์ม --}}
                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">ย้อนกลับ</a>
                </div>
            </div>
        </div>
    </div>
@endsection


@include('layouts.partials.sweetalert')

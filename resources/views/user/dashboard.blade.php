@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    {{-- ชื่อผู้ใช้งานจะถูกดึงมาจาก Auth::user()->name ในระบบจริง --}}
                    <h2 class="card-title text-primary mb-3">สวัสดี, [ชื่อผู้ใช้งาน]!</h2>
                    <p class="card-text lead">ยินดีต้อนรับสู่แดชบอร์ดส่วนตัวของคุณ</p>
                    <hr>
                    <div class="d-grid gap-2 col-md-6 mx-auto">
                        {{-- ลิงก์ไปยังหน้าค้นหา/จองสนามจริง --}}
                        <a href="{{route('user.booking.form')}}" class="btn btn-success btn-lg">
                            <i class="fas fa-futbol me-2"></i> จองสนามฟุตบอลทันที!
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i> การจองที่กำลังจะมาถึง
                    </h5>
                </div>
                <div class="card-body">
                    {{-- ส่วนนี้จะแสดงเมื่อไม่มีการจอง --}}
                    <div class="alert alert-info" role="alert">
                        คุณยังไม่มีการจองที่กำลังจะมาถึง
                        <a href="#" class="alert-link">จองสนามตอนนี้เลย!</a>
                    </div>

                    {{-- ตัวอย่างการแสดงการจอง (ข้อมูลจำลอง) --}}
                    <ul class="list-group list-group-flush" style="display: none;"> {{-- ซ่อนไว้ก่อนเพื่อแสดง alert ด้านบน --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>สนาม A (ขนาด 7 คน)</strong>
                                <small class="d-block text-muted">123 ถนนสนามฟุตบอล, กรุงเทพฯ</small>
                                <small class="d-block">
                                    วันที่: 25 มิ.ย. 2568
                                    เวลา: 18:00 - 19:00 น.
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark">รอการยืนยัน</span>
                                <br>
                                <a href="#" class="btn btn-sm btn-info mt-2">รายละเอียด</a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>สนาม B (หญ้าเทียม)</strong>
                                <small class="d-block text-muted">456 ซอยฟุตบอล, นนทบุรี</small>
                                <small class="d-block">
                                    วันที่: 01 ก.ค. 2568
                                    เวลา: 20:00 - 21:30 น.
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-success">ยืนยันแล้ว</span>
                                <br>
                                <a href="#" class="btn btn-sm btn-info mt-2">รายละเอียด</a>
                            </div>
                        </li>
                        {{-- สามารถเพิ่มรายการจำลองได้อีก --}}
                    </ul>
                    <div class="mt-3 text-center" style="display: none;"> {{-- ซ่อนไว้ก่อน --}}
                        <a href="#" class="btn btn-outline-primary">ดูการจองทั้งหมด</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> กิจกรรมล่าสุด
                    </h5>
                </div>
                <div class="card-body">
                    {{-- ส่วนนี้จะแสดงเมื่อไม่มีกิจกรรม --}}
                    <div class="alert alert-secondary" role="alert">
                        ยังไม่มีกิจกรรมล่าสุด
                    </div>

                    {{-- ตัวอย่างกิจกรรมล่าสุด (ข้อมูลจำลอง) --}}
                    <ul class="list-group list-group-flush" style="display: none;"> {{-- ซ่อนไว้ก่อน --}}
                        <li class="list-group-item">
                            การจองสนาม <strong>สนาม A</strong> ได้รับการยืนยันแล้ว
                            <br>
                            <small class="text-muted">1 ชั่วโมงที่แล้ว</small>
                        </li>
                        <li class="list-group-item">
                            คุณได้อัปเดตข้อมูลโปรไฟล์ของคุณ
                            <br>
                            <small class="text-muted">เมื่อวานนี้</small>
                        </li>
                        {{-- สามารถเพิ่มรายการจำลองได้อีก --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- อาจจะเพิ่มส่วนสำหรับแนะนำสนามยอดนิยม หรือโปรโมชั่น --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i> สนามแนะนำสำหรับคุณ</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        ค้นหาสนามที่ใช่สำหรับคุณ! ลองดูสนามยอดนิยมหรือสนามใหม่ๆ ที่น่าสนใจ
                    </p>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-search me-2"></i> สำรวจสนามทั้งหมด
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
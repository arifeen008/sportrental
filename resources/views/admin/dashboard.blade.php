@extends('layouts.app') {{-- หรือ Layout ของ Admin ที่คุณมี --}}

@section('styles')
    {{-- เพิ่ม Font Awesome CDN สำหรับไอคอนสวยๆ --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endsection

@section('content')
    <div class="container py-4">
        {{-- ส่วนต้อนรับ Admin --}}
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title text-primary mb-1">Admin Dashboard</h2>
                                <p class="card-text text-muted">ยินดีต้อนรับ, {{ Auth::user()->name }}</p>
                            </div>
                            <a href="{{ route('user.booking.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i> สร้างการจองใหม่
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- การ์ดสำหรับแสดงรายการที่ต้องจัดการ --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-exclamation-triangle me-2"></i> การจองที่ต้องดำเนินการ (รอตรวจสอบสลิป)
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- ส่วนนี้จะวนลูปเฉพาะการจองที่ 'รอตรวจสอบ' (verifying) --}}
                        @if ($bookings->where('payment_status', 'verifying')->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach ($bookings->where('payment_status', 'verifying') as $booking)
                                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                                        <div>
                                            <strong>#{{ $booking->id }} - {{ $booking->user->name }}</strong>
                                            <small class="d-block text-muted">
                                                วันที่ใช้บริการ: {{ $booking->booking_date->format('d/m/Y') }} | ยอดเงิน:
                                                {{ number_format($booking->total_price, 2) }} บาท
                                            </small>
                                        </div>
                                        <div class="mt-2 mt-md-0">
                                            @if ($booking->payment_status === 'verifying')
                                                <div class="d-flex justify-content-center gap-2">
                                                    {{-- ปุ่มสำหรับเปิด Modal ดูสลิป --}}
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewSlipModal-{{ $booking->id }}">
                                                        <i class="fas fa-receipt me-1"></i> ดูสลิป
                                                    </button>

                                                    {{-- ฟอร์มสำหรับส่งคำสั่ง 'อนุมัติ' (เหมือนเดิม) --}}
                                                    <form action="{{ route('admin.booking.approve', $booking->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('ยืนยันการอนุมัติการจอง #{{ $booking->id }}?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check-circle me-1"></i> อนุมัติ
                                                        </button>
                                                    </form>

                                                    {{-- อาจจะมีปุ่มปฏิเสธ --}}
                                                    <button class="btn btn-sm btn-danger">ปฏิเสธ</button>
                                                </div>
                                            @elseif($booking->payment_status === 'paid')
                                                <span class="text-success fw-bold">การจองสมบูรณ์</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-success text-center" role="alert">
                                ไม่มีรายการที่ต้องดำเนินการในขณะนี้
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ตารางแสดงประวัติการจองทั้งหมด --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> ประวัติการจองทั้งหมด</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ผู้จอง</th>
                                        <th>วันที่ใช้บริการ</th>
                                        <th>รายการ</th>
                                        <th class="text-end">ยอดชำระ</th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                            <td>
                                                @if ($booking->booking_type === 'daily_package')
                                                    {{-- กรณีเป็นการจองแบบเหมาวัน --}}
                                                    {{-- ดึงชื่อแพ็กเกจจากข้อมูลที่เก็บเป็น JSON --}}
                                                    <strong>{{ $booking->price_calculation_details['package_name'] ?? 'แพ็กเกจเหมาวัน' }}</strong>

                                                    {{-- แสดงประเภทงาน (การกุศล/แข่งขัน) ถ้ามี --}}
                                                    @if (isset($booking->price_calculation_details['rental_type']))
                                                        <small
                                                            class="d-block text-muted">{{ $booking->price_calculation_details['rental_type'] }}</small>
                                                    @endif
                                                @else
                                                    {{-- กรณีเป็นการจองรายชั่วโมง หรือใช้บัตรสมาชิก --}}

                                                    {{-- แสดงชื่อสนาม (เช็คก่อนว่ามีข้อมูลหรือไม่) --}}
                                                    <strong>{{ $booking->fieldType->name ?? 'ไม่ระบุสนาม' }}</strong>

                                                    {{-- แสดงเวลาที่จอง โดยจัดรูปแบบให้สวยงาม --}}
                                                    <small class="d-block text-muted">
                                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} น.
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($booking->total_price, 2) }}</td>
                                            <td class="text-center">
                                                @if ($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">ชำระเงินแล้ว</span>
                                                @elseif($booking->payment_status == 'unpaid')
                                                    <span class="badge bg-secondary">ยังไม่ชำระเงิน</span>
                                                @elseif($booking->payment_status == 'verifying')
                                                    <span class="badge bg-warning text-dark">รอตรวจสอบ</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $booking->payment_status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">ยังไม่มีประวัติการจอง
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{-- แสดงตัวแบ่งหน้า --}}
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- ================= ส่วน Modal สำหรับดูสลิป ================= --}}
    {{-- จะวนลูปสร้าง Modal ตามจำนวน booking ที่ 'รอตรวจสอบ' --}}
    @foreach ($bookings->where('payment_status', 'verifying') as $booking)
        <div class="modal fade" id="viewSlipModal-{{ $booking->id }}" tabindex="-1"
            aria-labelledby="slipModalLabel-{{ $booking->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="slipModalLabel-{{ $booking->id }}">สลิปการโอนเงินสำหรับการจอง
                            #{{ $booking->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        @if ($booking->slip_image_path)
                            {{-- แสดงรูปภาพสลิป --}}
                            <img src="{{ Storage::url($booking->slip_image_path) }}" class="img-fluid" alt="Payment Slip">
                        @else
                            <p class="text-danger">ไม่พบไฟล์สลิป</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

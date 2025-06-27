@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .summary-dl dt {
            font-weight: 500;
        }

        .summary-dl dd {
            color: #555;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">รายละเอียดการจอง: {{ $booking->booking_code }}</h1>
        <a href="{{ route('admin.booking.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> กลับสู่ประวัติการจอง
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">ข้อมูลการจอง</h5>
                </div>
                <div class="card-body">
                    <dl class="row summary-dl mb-0">
                        <dt class="col-sm-4">รหัสการจอง</dt>
                        <dd class="col-sm-8">{{ $booking->booking_code }}</dd>

                        <dt class="col-sm-4">วันที่ใช้บริการ</dt>
                        <dd class="col-sm-8">{{ thaidate('lที่ j F Y', $booking->booking_date) }}</dd>

                        <dt class="col-sm-4">เวลา</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} น.</dd>

                        <dt class="col-sm-4">ระยะเวลา</dt>
                        <dd class="col-sm-8">{{ $booking->duration_in_hours }} ชั่วโมง</dd>

                        <dt class="col-sm-4">ประเภทการจอง</dt>
                        <dd class="col-sm-8">
                            @switch($booking->booking_type)
                                @case('hourly')
                                    รายชั่วโมง
                                @break

                                @case('daily_package')
                                    เหมาวัน
                                @break

                                @case('membership')
                                    ใช้บัตรสมาชิก
                                @break

                                @default
                                    {{ $booking->booking_type }}
                            @endswitch
                        </dd>

                        <dt class="col-sm-4">สนาม / แพ็กเกจ</dt>
                        <dd class="col-sm-8">
                            @if ($booking->booking_type === 'daily_package')
                                {{ $booking->price_calculation_details['package_name'] ?? '-' }}
                                ({{ $booking->price_calculation_details['rental_type'] ?? '' }})
                            @else
                                {{ optional($booking->fieldType)->name ?? 'ไม่ระบุ' }}
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">ข้อมูลผู้จอง</h5>
                </div>
                <div class="card-body">
                    <dl class="row summary-dl mb-0">
                        <dt class="col-sm-4">ชื่อ-สกุล</dt>
                        <dd class="col-sm-8">{{ $booking->user->name }}</dd>
                        <dt class="col-sm-4">อีเมล</dt>
                        <dd class="col-sm-8">{{ $booking->user->email }}</dd>
                        <dt class="col-sm-4">เบอร์โทรศัพท์</dt>
                        <dd class="col-sm-8">{{ $booking->user->phone_number ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">สรุปค่าใช้จ่าย</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-6">ราคารวม</dt>
                        <dd class="col-6 text-end">{{ number_format($booking->base_price, 2) }}</dd>
                        <dt class="col-6">ค่าล่วงเวลา</dt>
                        <dd class="col-6 text-end">{{ number_format($booking->overtime_charges, 2) }}</dd>
                        <dt class="col-6 text-success">ส่วนลด</dt>
                        <dd class="col-6 text-end text-success">-{{ number_format($booking->discount, 2) }}</dd>
                        <hr class="my-2">
                        <dt class="col-6 fs-5">ยอดสุทธิ (บาท)</dt>
                        <dd class="col-6 fs-5 text-end fw-bold text-danger">{{ number_format($booking->total_price, 2) }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">สลิปการโอนเงิน</h5>
                </div>
                <div class="card-body text-center">
                    @if ($booking->slip_image_path)
                        <a href="{{ Storage::url($booking->slip_image_path) }}" data-bs-toggle="modal"
                            data-bs-target="#slipModal">
                            <img src="{{ Storage::url($booking->slip_image_path) }}" class="img-fluid rounded"
                                alt="Payment Slip">
                        </a>
                    @else
                        <p class="text-muted mt-3">ยังไม่มีการอัปโหลดสลิป</p>
                    @endif
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">แก้ไขวัน/เวลาจอง</h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted small">ใช้ฟังก์ชันนี้ในกรณีที่ลูกค้าติดต่อขอเลื่อนวันหรือเวลา</p>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                        <i class="fas fa-edit me-2"></i>แก้ไขการจอง
                    </button>
                </div>
            </div>
            @if ($booking->payment_status === 'rejected' && $booking->rejection_reason)
                <div class="alert alert-danger mt-4">
                    <strong>เหตุผลที่ถูกปฏิเสธ:</strong><br>
                    {{ $booking->rejection_reason }}
                </div>
            @endif
        </div>
    </div>

    @if ($booking->slip_image_path)
        <div class="modal fade" id="slipModal" tabindex="-1" aria-labelledby="slipModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <img src="{{ Storage::url($booking->slip_image_path) }}" class="img-fluid"
                            alt="Payment Slip Preview">
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.booking.reschedule', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH') {{-- ใช้ PATCH สำหรับการอัปเดต --}}
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขการจอง: {{ $booking->booking_code }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">การจองเดิม: {{ thaidate('j M Y', $booking->booking_date) }} เวลา
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</p>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label"><strong>วันที่ใหม่</strong></label>
                            <input type="date" name="new_booking_date" class="form-control"
                                value="{{ $booking->booking_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label"><strong>เวลาเริ่มใหม่</strong></label>
                                <select class="form-select" name="new_start_time" required>
                                    @for ($i = 9; $i <= 21; $i++)
                                        @php $time = sprintf('%02d', $i) . ':00'; @endphp
                                        <option value="{{ $time }}"
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') == $time ? 'selected' : '' }}>
                                            {{ $time }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label"><strong>เวลาสิ้นสุดใหม่</strong></label>
                                <select class="form-select" name="new_end_time" required>
                                    @for ($i = 10; $i <= 22; $i++)
                                        @php $time = sprintf('%02d', $i) . ':00'; @endphp
                                        <option value="{{ $time }}"
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') == $time ? 'selected' : '' }}>
                                            {{ $time }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

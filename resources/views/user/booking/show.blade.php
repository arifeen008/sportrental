@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endpush

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">รายละเอียดการจอง: {{ $booking->booking_code }}</h4>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-light btn-sm">&laquo; กลับสู่แดชบอร์ด</a>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <h5>ข้อมูลการจอง</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>สถานะการชำระเงิน:</span>
                                    <strong>
                                        @if($booking->status == 'paid') <span class="badge bg-success">ชำระเงินแล้ว</span>
                                        @elseif($booking->status == 'unpaid') <span class="badge bg-warning text-dark">รอชำระเงิน</span>
                                        @elseif($booking->status == 'verifying') <span class="badge bg-info">รอตรวจสอบ</span>
                                        @elseif($booking->status == 'rejected') <span class="badge bg-danger">ถูกปฏิเสธ</span>
                                        @else <span class="badge bg-secondary">{{ $booking->status }}</span>
                                        @endif
                                    </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>วันที่ใช้บริการ:</span>
                                    <strong>{{ thaidate('lที่ j F Y', $booking->booking_date) }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>เวลา:</span>
                                    <strong>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} น.</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>ระยะเวลา:</span>
                                    <strong>{{ $booking->duration_in_hours }} ชั่วโมง</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>ประเภทการจอง:</span>
                                    <strong>
                                        @if($booking->booking_type === 'hourly') รายชั่วโมง
                                        @elseif($booking->booking_type === 'daily_package') เหมาวัน
                                        @elseif($booking->booking_type === 'membership') ใช้บัตรสมาชิก
                                        @endif
                                    </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>สนาม/แพ็กเกจ:</span>
                                    <strong>{{ optional($booking->fieldType)->name ?? ($booking->price_calculation_details['package_name'] ?? '-') }}</strong>
                                </li>
                            </ul>
                            @if($booking->notes)
                            <h5 class="mt-4">หมายเหตุเพิ่มเติม</h5>
                            <p class="text-muted fst-italic">{{ $booking->notes }}</p>
                            @endif
                        </div>

                        <div class="col-md-5">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">สรุปค่าใช้จ่าย</h5>
                                    <dl class="row">
                                        <dt class="col-6">ราคารวม</dt><dd class="col-6 text-end">{{ number_format($booking->base_price, 2) }}</dd>
                                        <dt class="col-6">ส่วนลด</dt><dd class="col-6 text-end text-success">-{{ number_format($booking->discount, 2) }}</dd>
                                        <hr class="my-2">
                                        <dt class="col-6 fs-5">ยอดสุทธิ</dt><dd class="col-6 fs-5 text-end fw-bold">{{ number_format($booking->total_price, 2) }}</dd>
                                    </dl>
                                </div>
                            </div>
                            @if($booking->slip_image_path)
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">สลิปการโอนเงิน</h6>
                                        <img src="{{ Storage::url($booking->slip_image_path) }}" class="img-fluid rounded" alt="Payment Slip">
                                    </div>
                                </div>
                            @endif
                            @if($booking->status === 'rejected' && $booking->rejection_reason)
                                <div class="alert alert-danger mt-3">
                                    <strong>เหตุผลที่ถูกปฏิเสธ:</strong> {{ $booking->rejection_reason }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .summary-list .list-group-item {
            border: none;
            padding: 0.75rem 0;
        }
        .summary-total {
            border-top: 2px solid var(--bs-primary);
            padding-top: 1rem !important;
        }
        .summary-total .total-price {
            font-size: 1.75rem;
            font-weight: bold;
            color: var(--bs-danger);
        }
    </style>
@endpush


@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i>โปรดตรวจสอบและยืนยันการจอง</h4>
                </div>
                <div class="card-body p-4">

                    <div class="row">
                        <div class="col-md-6">
                            <h5>รายละเอียดการจอง</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-calendar-day fa-fw me-2 text-muted"></i>
                                    <strong>วันที่:</strong> {{ thaidate('lที่ j F Y', $summary['booking_date']) }}
                                </li>
                                <li>
                                    <i class="fas fa-clock fa-fw me-2 text-muted"></i>
                                    <strong>เวลา:</strong> {{ $summary['time_range'] }}
                                </li>
                                <li>
                                    <i class="fas fa-futbol fa-fw me-2 text-muted"></i>
                                    <strong>รายการ:</strong>
                                    @if ($summary['booking_inputs']['booking_type'] === 'daily_package')
                                        {{ $summary['package_name'] }} ({{ $summary['booking_inputs']['rental_type'] }})
                                    @else
                                        {{ $summary['field_name'] }}
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>ข้อมูลผู้จอง</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-user fa-fw me-2 text-muted"></i>
                                    <strong>ชื่อ:</strong> {{ Auth::user()->name }}
                                </li>
                                <li>
                                    <i class="fas fa-envelope fa-fw me-2 text-muted"></i>
                                    <strong>อีเมล:</strong> {{ Auth::user()->email }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mt-4">สรุปค่าใช้จ่าย / การใช้สิทธิ์</h5>
                    <div class="list-group list-group-flush summary-list">

                        {{-- ================= LOGIC การแสดงผลตามประเภทการจอง (แก้ไขแล้ว) ================= --}}

                        @if ($summary['booking_inputs']['booking_type'] === 'membership')
                            {{-- สำหรับบัตรสมาชิก --}}
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>ชั่วโมงที่จะถูกหัก</span>
                                <span class="fs-5 fw-bold text-primary">{{ $summary['hours_to_deduct'] }} ชั่วโมง</span>
                            </div>
                        @else
                            {{-- สำหรับรายชั่วโมง และ เหมาวัน --}}
                            @if (isset($summary['base_price']))
                                {{-- เหมาวัน --}}
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>ราคาเหมา</span>
                                    <span>{{ number_format($summary['base_price'], 2) }} บาท</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>ค่าบริการล่วงเวลา</span>
                                    <span>{{ number_format($summary['overtime_cost'], 2) }} บาท</span>
                                </div>
                            @else
                                {{-- รายชั่วโมง --}}
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>ราคารวม (ก่อนหักส่วนลด)</span>
                                    <span>{{ number_format($summary['subtotal_price'] ?? 0, 2) }} บาท</span>
                                </div>
                            @endif

                            {{-- ส่วนลด (แสดงตลอดเวลา) --}}
                            <div class="list-group-item d-flex justify-content-between align-items-center text-success">
                                <span><i class="fas fa-tags me-1"></i> {{ $summary['discount_reason'] ?? 'ส่วนลด' }}</span>
                                <span>-{{ number_format($summary['discount_amount'] ?? 0, 2) }} บาท</span>
                            </div>
                            
                            {{-- ยอดรวมสุดท้าย --}}
                            <div class="list-group-item d-flex justify-content-between align-items-center summary-total">
                                <span class="fw-bold">ยอดชำระสุทธิ</span>
                                <span class="total-price">{{ number_format($summary['total_price'], 2) }} บาท</span>
                            </div>
                        @endif

                        {{-- สิทธิพิเศษ (แสดงตลอดเวลา) --}}
                        <div class="list-group-item d-flex justify-content-between align-items-center text-info">
                            <span><i class="fas fa-gift me-2"></i>สิทธิพิเศษ</span>
                            <strong>{{ $summary['special_perks'] ?? 'ไม่มี' }}</strong>
                        </div>
                    </div>

                    {{-- ส่วนแสดงเงื่อนไขการมัดจำ (จะแสดงเมื่อเป็นเหมาวันเท่านั้น) --}}
                   @if ($summary['booking_inputs']['booking_type'] === 'daily_package' && isset($summary['deposit_amount']))
                        <div class="alert alert-warning mt-4">
                            <h5 class="alert-heading">เงื่อนไขการชำระเงิน</h5>
                            <p class="mb-2">กรุณาชำระเงินมัดจำ 50% เพื่อยืนยันการจองของท่าน</p>
                            <ul class="list-unstyled mb-0">
                                <li>- ยอดมัดจำที่ต้องชำระวันนี้: <strong class="fs-5">{{ number_format($summary['deposit_amount'], 2) }} บาท</strong></li>
                                <li>- เงินประกันสนาม: <strong>{{ number_format($summary['security_deposit'], 2) }} บาท</strong> (ชำระพร้อมยอดคงเหลือ)</li>
                            </ul>
                            <hr>
                            <p class="mb-0"><small>หมายเหตุ: ยอดคงเหลือและเงินประกันสนาม ต้องชำระล่วงหน้า 5 วันก่อนวันใช้งาน</small></p>
                        </div>
                    @endif

                    <form action="{{ route('user.booking.store') }}" method="POST" class="mt-4">
                        @csrf

                        {{-- Hidden Inputs ที่แก้ไขแล้วทั้งหมด --}}
                        @foreach ($summary['booking_inputs'] as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        
                        <input type="hidden" name="base_price" value="{{ $summary['subtotal_price'] ?? ($summary['base_price'] ?? 0) }}">
                        <input type="hidden" name="overtime_charges" value="{{ $summary['overtime_cost'] ?? 0 }}">
                        <input type="hidden" name="discount" value="{{ $summary['discount_amount'] ?? 0 }}">
                        <input type="hidden" name="total_price" value="{{ $summary['total_price'] }}">
                        <input type="hidden" name="duration_in_hours" value="{{ $summary['duration_in_hours'] ?? 0 }}">
                        <input type="hidden" name="hours_deducted" value="{{ $summary['hours_to_deduct'] ?? null }}">
                        <input type="hidden" name="user_membership_id" value="{{ $summary['user_membership_id'] ?? null }}">

                        {{-- ปุ่มยืนยันและปุ่มกลับ --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">&laquo; กลับไปแก้ไข</a>
                            <button type="submit" class="btn btn-success">ยืนยันการจอง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">กรุณาตรวจสอบและยืนยันการจอง</h4>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title">{{ $summary['title'] }}</h5>
                        <hr>
                        <dl class="row summary-table gy-2">
                            @if (
                                    $summary['booking_inputs']['booking_type'] === 'hourly' ||
                                    $summary['booking_inputs']['booking_type'] === 'membership'
                                )
                                <dt class="col-5">สนาม</dt>
                                <dd class="col-7">{{ $summary['field_name'] }}</dd>
                            @else
                                <dt class="col-5">แพ็กเกจ</dt>
                                <dd class="col-7">{{ $summary['package_name'] }}</dd>
                            @endif

                            <dt class="col-5">วันที่</dt>
                            <dd class="col-7">{{ $summary['booking_date_formatted'] }}</dd>

                            <dt class="col-5">เวลา</dt>
                            <dd class="col-7">{{ $summary['time_range'] }}</dd>

                            <hr class="my-2">

                            @if ($summary['booking_inputs']['booking_type'] === 'membership')
                                <dt class="col-5">ชั่วโมงที่จะถูกหัก</dt>
                                <dd class="col-7">{{ $summary['hours_to_deduct'] }} ชั่วโมง</dd>
                            @else
                                @if (isset($summary['base_price']))
                                    {{-- ส่วนของเหมาวัน (เหมือนเดิม) --}}
                                    <dt class="col-5">ราคาเหมา</dt>
                                    <dd class="col-7">{{ number_format($summary['base_price'], 2) }} บาท</dd>
                                    <dt class="col-5">ค่าบริการล่วงเวลา</dt>
                                    <dd class="col-7">{{ number_format($summary['overtime_cost'], 2) }} บาท</dd>
                                @else
                                    {{-- ส่วนของรายชั่วโมง (ที่แก้ไขใหม่) --}}
                                    <dt class="col-12">รายละเอียดราคา ({{ $summary['duration_in_hours'] }} ชั่วโมง)</dt>
                                    @foreach ($summary['price_breakdown_details'] as $detail)
                                        <dt class="col-6 ps-4"><small>{{ $detail['time'] }}</small></dt>
                                        <dd class="col-6"><small>{{ number_format($detail['price']) }} บาท</small></dd>
                                    @endforeach
                                @endif
                                <dt class="col-5 fw-bold">ราคารวม</dt>
                                <dd class="col-7 total-price">{{ number_format($summary['total_price'], 2) }} บาท</dd>
                            @endif

                            @if (!empty($summary['special_perks']))
                                <hr class="my-2">
                                <dt class="col-5 text-success">สิทธิพิเศษ</dt>
                                <dd class="col-7 text-success">{{ $summary['special_perks'] }}</dd>
                            @endif
                        </dl>
                        @if (isset($summary['deposit_amount']))
                            <div class="alert alert-warning mt-4">
                                <h5 class="alert-heading">เงื่อนไขการชำระเงิน</h5>
                                <p class="mb-2">กรุณาชำระเงินมัดจำ 50% เพื่อยืนยันการจองของท่านภายในวันที่ทำการจอง</p>
                                <ul class="list-unstyled mb-0">
                                    <li>- ยอดมัดจำที่ต้องชำระวันนี้: <strong
                                            class="fs-5">{{ number_format($summary['deposit_amount'], 2) }} บาท</strong>
                                    </li>
                                    <li>- เงินประกันสนาม: <strong>{{ number_format($summary['security_deposit'], 2) }}
                                            บาท</strong></li>
                                </ul>
                                <hr>
                                <p class="mb-0"><small>หมายเหตุ: ยอดคงเหลือและเงินประกันสนาม ต้องชำระล่วงหน้า 5
                                        วันก่อนวันใช้งาน</small></p>
                            </div>
                        @endif
                        <form action="{{ route('user.booking.store') }}" method="POST" class="mt-4">
                            @csrf

                            @foreach ($summary['booking_inputs'] as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="hidden" name="total_price" value="{{ $summary['total_price'] }}">
                            @if (isset($summary['hours_to_deduct']))
                                <input type="hidden" name="hours_deducted" value="{{ $summary['hours_to_deducted'] }}">
                            @endif

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('user.booking.create') }}" class="btn btn-secondary">&laquo;
                                    แก้ไขข้อมูล</a>
                                <button type="submit" class="btn btn-success">ยืนยันการจอง</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
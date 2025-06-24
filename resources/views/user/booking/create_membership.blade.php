@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endsection

@section('content')
    <div class="container py-5">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-id-card me-2"></i>บัตรสมาชิกของคุณ</h4>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">ประเภทบัตร:</dt>
                            <dd class="col-sm-8">{{ $activeMembership->membershipTier->tier_name }}</dd>
                            <dt class="col-sm-4">เลขที่บัตร:</dt>
                            <dd class="col-sm-8">{{ $activeMembership->card_number }}</dd>
                            <dt class="col-sm-4">ชั่วโมงคงเหลือ:</dt>
                            <dd class="col-sm-8 fw-bold fs-5 text-primary">
                                {{ number_format($activeMembership->remaining_hours, 2) }} ชั่วโมง</dd>
                            <dt class="col-sm-4">วันหมดอายุ:</dt>
                            <dd class="col-sm-8">{{ thaidate('j F Y', $activeMembership->expires_at) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">กรอกข้อมูลเพื่อจองสนามโดยใช้สิทธิ์</h5>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <form action="{{ route('user.booking.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_type" value="membership">
                            <input type="hidden" name="user_membership_id" value="{{ $activeMembership->id }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="field_type_id" class="form-label">ประเภทสนาม</label>
                                    <select class="form-select" name="field_type_id" required>
                                        <option value="1">สนามกลางแจ้ง</option>
                                        <option value="2">สนามหลังคา</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="booking_date" class="form-label">ในวันที่</label>
                                    <input type="date" class="form-control" name="booking_date" min="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">เวลาเริ่ม</label>
                                    <select class="form-select" name="start_time" required>
                                        @for ($i = 9; $i <= 21; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}:00">{{ sprintf('%02d', $i) }}:00
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
                                    <select class="form-select" name="end_time" required>
                                        @for ($i = 10; $i <= 22; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}:00">{{ sprintf('%02d', $i) }}:00
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">หมายเหตุ (ถ้ามี)</label>
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i> กลับแดชบอร์ด
                                </a>

                                <button type="submit" class="btn btn-primary btn-lg">
                                    ตรวจสอบราคาและดำเนินการต่อ <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

             <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>ตารางการจองที่ยืนยันแล้ว</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>เวลา</th>
                                        <th>สนาม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($confirmedBookings as $booking)
                                        <tr>
                                            <td>
                                                {{ thaidate('วัน l', $booking->booking_date) }}<br>
                                                {{ thaidate('j F Y', $booking->booking_date) }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                                            <td>
                                                @if ($booking->booking_type === 'daily_package')
                                                    {{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}
                                                @else
                                                    {{ $booking->fieldType->name ?? 'ไม่ระบุ' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted p-4">
                                                ยังไม่มีการจองที่ยืนยันแล้วในช่วงนี้
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

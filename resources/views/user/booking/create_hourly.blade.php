@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endsection

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-clock me-2"></i>จองสนามรายชั่วโมง</h4>
                    </div>
                    <div class="card-body p-4">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <form action="{{ route('user.booking.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_type" value="hourly">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="field_type_id" class="form-label">ประเภทสนาม</label>
                                    <select class="form-select" name="field_type_id" required>
                                        <option value="1">สนามกลางแจ้ง</option>
                                        <option value="2">สนามหลังคา</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="booking_date" class="form-label">ในวันที่</label>
                                    <input type="date" class="form-control" name="booking_date" value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}"
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
                                    <textarea class="form-control" name="notes" rows="3" placeholder="เช่น ขออุปกรณ์เพิ่มเติม, คำขอพิเศษอื่นๆ"></textarea>
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

            <div class="col-lg-5">
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

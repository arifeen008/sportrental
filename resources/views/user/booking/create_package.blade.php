@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .overtime-wrapper {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row g-4 justify-content-center">

            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-calendar-day me-2"></i>จองสนามแบบเหมาวัน</h4>
                    </div>
                    <div class="card-body p-4">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('user.booking.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_type" value="daily_package">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">ประเภทการจัดงาน</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rental_type"
                                                id="typeTournament" value="รายการแข่งขัน" checked>
                                            <label class="form-check-label" for="typeTournament">รายการแข่งขัน</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rental_type"
                                                id="typeCharity" value="การกุศล">
                                            <label class="form-check-label" for="typeCharity">การกุศล</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="package_name" class="form-label">เลือกแพ็กเกจ</label>
                                    <select class="form-select" name="package_name" required>
                                        <option value="สนามกลางแจ้ง">เหมาสนามกลางแจ้ง</option>
                                        <option value="สนามหลังคา">เหมาสนามหลังคา</option>
                                        <option value="เหมา 2 สนาม">เหมา 2 สนาม</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="booking_date" class="form-label">ในวันที่ (ต้องจองล่วงหน้า 5 วัน)</label>
                                    <input type="date" class="form-control" name="booking_date" min="{{ now()->addDays(5)->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="wants_overtime" name="wants_overtime" value="1">
                                        <label class="form-check-label" for="wants_overtime">ต้องการช่วงเวลาเพิ่มเติม
                                            (นอกเวลา หลัง 18:00 น.)</label>
                                    </div>
                                </div>
                                <div id="overtime_end_time_wrapper" class="col-md-6 overtime-wrapper">
                                    <label for="overtime_end_time" class="form-label">ใช้บริการถึงเวลา</label>
                                    <select class="form-select" name="overtime_end_time">
                                        <option value="19:00">19:00 น.</option>
                                        <option value="20:00">20:00 น.</option>
                                        <option value="21:00">21:00 น.</option>
                                        <option value="22:00">22:00 น.</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">หมายเหตุ (ถ้ามี)</label>
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">&laquo; กลับแดชบอร์ด</a>
                                <button type="submit" class="btn btn-primary btn-lg">ตรวจสอบราคาและดำเนินการต่อ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>ตารางจองที่ยืนยันแล้ว</h5>
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
                                            <td>{{ thaidate('d M y', $booking->booking_date) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                                            <td>{{ optional($booking->fieldType)->name ?? 'เหมาวัน' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center p-3 text-muted">
                                                ยังไม่มีการจองที่ยืนยันแล้ว</td>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overtimeCheckbox = document.getElementById('wants_overtime');
            const timeWrapper = document.getElementById('overtime_end_time_wrapper');

            if (overtimeCheckbox && timeWrapper) {
                overtimeCheckbox.addEventListener('change', function() {
                    timeWrapper.style.display = this.checked ? 'block' : 'none';
                });
            }
        });
    </script>
@endpush

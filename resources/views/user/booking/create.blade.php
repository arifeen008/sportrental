@extends('layouts.app')

@push('styles')
    {{-- เราสามารถใส่ CSS ที่จำเป็นสำหรับหน้านี้ได้ (ถ้ามี) --}}
    {{-- Font Awesome สำหรับไอคอน --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        /* ซ่อนส่วนของฟอร์มที่ยังไม่ถูกเลือก */
        .form-section {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row g-4">

            <div class="col-lg-7">
                <div class="card shadow-sm" id="booking_form_card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>ฟอร์มจองสนาม</h4>
                    </div>
                    <div class="card-body p-4">

                        {{-- ส่วนสำหรับแสดงข้อความ Error ที่ถูกส่งกลับมาจาก Controller --}}
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('user.booking.confirm') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                {{-- ประเภทการจอง --}}
                                <div class="col-12">
                                    <label for="booking_type" class="form-label fw-bold">ประเภทการจอง</label>
                                    <select class="form-select" id="booking_type" name="booking_type" required>
                                        <option value="hourly" selected>เช่ารายชั่วโมง</option>
                                        <option value="daily_package">เช่าเหมาวัน</option>
                                        <option value="membership">ใช้บัตรสมาชิก</option>
                                    </select>
                                </div>

                                {{-- ส่วนสำหรับ "รายชั่วโมง" และ "บัตรสมาชิก" --}}
                                <div id="hourly_booking_section" class="form-section col-12">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="field_type_id" class="form-label">ประเภทสนาม</label>
                                            <select class="form-select" name="field_type_id">
                                                <option value="1">สนามกลางแจ้ง</option>
                                                <option value="2">สนามหลังคา</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="start_time" class="form-label">เวลาเริ่ม</label>
                                            <select class="form-select" name="start_time">
                                                @for ($i = 9; $i <= 21; $i++)
                                                    <option value="{{ sprintf('%02d', $i) }}:00">
                                                        {{ sprintf('%02d', $i) }}:00</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
                                            <select class="form-select" name="end_time">
                                                @for ($i = 10; $i <= 22; $i++)
                                                    <option value="{{ sprintf('%02d', $i) }}:00">
                                                        {{ sprintf('%02d', $i) }}:00</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- ส่วนสำหรับ "เช่าเหมาวัน" --}}
                                <div id="package_booking_section" class="form-section col-12">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold">ประเภทการจัดงาน</label>
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
                                        <div class="col-12">
                                            <label for="package_name" class="form-label">เลือกแพ็กเกจ</label>
                                            <select class="form-select" name="package_name">
                                                <option value="สนามกลางแจ้ง">เหมาสนามกลางแจ้ง</option>
                                                <option value="สนามหลังคา">เหมาสนามหลังคา</option>
                                                <option value="เหมา 2 สนาม">เหมา 2 สนาม</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">เลือกช่วงเวลา</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="wants_overtime"
                                                    name="wants_overtime" value="1">
                                                <label class="form-check-label" for="wants_overtime">
                                                    ต้องการช่วงเวลาเพิ่มเติม (นอกเวลา หลัง 18:00 น.)
                                                </label>
                                            </div>
                                        </div>
                                        <div id="overtime_end_time_wrapper" class="form-section col-md-6">
                                            <label for="overtime_end_time" class="form-label">ใช้บริการถึงเวลา</label>
                                            <select class="form-select" name="overtime_end_time">
                                                <option value="19:00">19:00 น.</option>
                                                <option value="20:00">20:00 น.</option>
                                                <option value="21:00">21:00 น.</option>
                                                <option value="22:00">22:00 น.</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- ฟิลด์ที่แสดงตลอด --}}
                                <div class="col-12">
                                    <label for="booking_date" class="form-label">ในวันที่</label>
                                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">หมายเหตุ (ถ้ามี)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="เช่น ขออุปกรณ์เพิ่มเติม, คำขอพิเศษอื่นๆ"></textarea>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">ตรวจสอบราคาและดำเนินการต่อ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            ตารางการจองที่ยืนยันแล้ว
                        </h5>
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


@section('scripts')
    <script>
        // ตรวจสอบว่ามี session 'success' ถูกส่งกลับมาหรือไม่
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                timer: 3000, // แสดงผล 3 วินาทีแล้วหายไป
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif

        // ตรวจสอบว่ามี session 'error' ถูกส่งกลับมาหรือไม่
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}'
                // สำหรับ error เราจะไม่ตั้งเวลาให้หายไป ให้ผู้ใช้กดยืนยันเอง
            });
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingTypeSelect = document.getElementById('booking_type');
            const hourlySection = document.getElementById('hourly_booking_section');
            const packageSection = document.getElementById('package_booking_section');
            const wantsOvertimeCheckbox = document.getElementById('wants_overtime');
            const overtimeEndTimeWrapper = document.getElementById('overtime_end_time_wrapper');

            function toggleSections() {
                const selectedType = bookingTypeSelect.value;
                hourlySection.style.display = 'none';
                packageSection.style.display = 'none';

                if (selectedType === 'hourly' || selectedType === 'membership') {
                    hourlySection.style.display = 'block';
                } else if (selectedType === 'daily_package') {
                    packageSection.style.display = 'block';
                    if (wantsOvertimeCheckbox) {
                        wantsOvertimeCheckbox.checked = false;
                    }
                    if (overtimeEndTimeWrapper) {
                        overtimeEndTimeWrapper.style.display = 'none';
                    }
                }
            }

            if (bookingTypeSelect) {
                bookingTypeSelect.addEventListener('change', toggleSections);
            }
            if (wantsOvertimeCheckbox) {
                wantsOvertimeCheckbox.addEventListener('change', function() {
                    if (overtimeEndTimeWrapper) {
                        overtimeEndTimeWrapper.style.display = this.checked ? 'block' : 'none';
                    }
                });
            }

            toggleSections();
        });
    </script>
@endsection

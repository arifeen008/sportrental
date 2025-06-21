@extends('layouts.app')
@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">ฟอร์มจองสนามฟุตบอล</h4>
                    </div>
                    <div class="card-body p-4">

                        <form action="{{ route('user.booking.confirm') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="booking_type" class="form-label fw-bold">ประเภทการจอง</label>
                                    <select class="form-select" id="booking_type" name="booking_type" required>
                                        <option value="" disabled selected>-- กรุณาเลือกประเภทการจอง --</option>
                                        <option value="hourly">เช่ารายชั่วโมง</option>
                                        <option value="daily_package">เช่าเหมาวัน</option>
                                        <option value="membership">ใช้บัตรสมาชิก</option>
                                    </select>
                                </div>

                                <div id="hourly_booking_section" class="form-section col-12">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="field_type_id" class="form-label">ประเภทสนาม</label>
                                            <select class="form-select" id="field_type_id" name="field_type_id">
                                                <option value="1">สนามกลางแจ้ง</option>
                                                <option value="2">สนามหลังคา</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="start_time" class="form-label">เวลาเริ่ม</label>
                                            <select class="form-select" id="start_time" name="start_time">
                                                @for ($i = 9; $i <= 21; $i++)
                                                    {{-- <--- แก้จาก 8 เป็น 9 --}}
                                                    <option value="{{ sprintf('%02d', $i) }}:00">
                                                        {{ sprintf('%02d', $i) }}:00</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
                                            <select class="form-select" id="end_time" name="end_time">
                                                @for ($i = 10; $i <= 22; $i++)
                                                    {{-- <--- แก้จาก 9 เป็น 10 --}}
                                                    <option value="{{ sprintf('%02d', $i) }}:00">
                                                        {{ sprintf('%02d', $i) }}:00</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="package_booking_section" class="form-section col-12">
     <div class="row g-3">
        
        <div class="col-12">
            <label class="form-label fw-bold">ประเภทการจัดงาน</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="rental_type" id="typeTournament" value="รายการแข่งขัน" checked>
                <label class="form-check-label" for="typeTournament">รายการแข่งขัน</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="rental_type" id="typeCharity" value="การกุศล">
                <label class="form-check-label" for="typeCharity">การกุศล</label>
            </div>
        </div>
        <div class="col-12">
            <label for="package_name" class="form-label">เลือกแพ็กเกจ</label>
            <select class="form-select" id="package_name" name="package_name">
                <option value="สนามกลางแจ้ง">เหมาสนามกลางแจ้ง</option>
                <option value="สนามหลังคา">เหมาสนามหลังคา</option>
                <option value="เหมา 2 สนาม">เหมา 2 สนาม</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">เลือกช่วงเวลา</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="wants_overtime" name="wants_overtime" value="1">
                <label class="form-check-label" for="wants_overtime">
                    ต้องการช่วงเวลาเพิ่มเติม (นอกเวลา หลัง 18:00 น.)
                </label>
            </div>
        </div>
        <div id="overtime_end_time_wrapper" class="form-section col-md-6">
            <label for="overtime_end_time" class="form-label">ใช้บริการถึงเวลา</label>
            <select class="form-select" id="overtime_end_time" name="overtime_end_time">
                <option value="19:00">19:00 น.</option>
                <option value="20:00">20:00 น.</option>
                <option value="21:00">21:00 น.</option>
                <option value="22:00">22:00 น.</option>
            </select>
        </div>
    </div>
</div>

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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. ดึง Element ที่ต้องใช้ทั้งหมด
            const bookingTypeSelect = document.getElementById('booking_type');
            const hourlySection = document.getElementById('hourly_booking_section');
            const packageSection = document.getElementById('package_booking_section');

            const wantsOvertimeCheckbox = document.getElementById('wants_overtime');
            const overtimeEndTimeWrapper = document.getElementById('overtime_end_time_wrapper');

            // 2. เพิ่ม Event Listener เมื่อมีการเปลี่ยน "ประเภทการจอง"
            bookingTypeSelect.addEventListener('change', function() {
                const selectedType = this.value;

                // ซ่อนทุกส่วนก่อน
                hourlySection.style.display = 'none';
                packageSection.style.display = 'none';

                if (selectedType === 'hourly' || selectedType === 'membership') {
                    // ถ้าเลือก 'รายชั่วโมง' หรือ 'บัตรสมาชิก' ให้แสดงส่วนของรายชั่วโมง
                    hourlySection.style.display = 'block';
                } else if (selectedType === 'daily_package') {
                    // ถ้าเลือก 'เหมาวัน' ให้แสดงส่วนของแพ็กเกจ
                    packageSection.style.display = 'block';
                }
            });

            // 3. เพิ่ม Event Listener สำหรับ Checkbox "ต้องการช่วงเวลาเพิ่มเติม"
            wantsOvertimeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // ถ้า Checkbox ถูกเลือก ให้แสดงตัวเลือกเวลาสิ้นสุดของ Overtime
                    overtimeEndTimeWrapper.style.display = 'block';
                } else {
                    // ถ้าไม่เลือก ให้ซ่อนไว้
                    overtimeEndTimeWrapper.style.display = 'none';
                }
            });
        });
    </script>
@endsection

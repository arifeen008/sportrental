@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .availability-status {
            min-height: 40px;
            transition: all 0.3s;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-clock me-2"></i>จองสนามรายชั่วโมง</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('user.booking.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_type" value="hourly">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="field_type_id_hourly" class="form-label">ประเภทสนาม</label>
                                    <select class="form-select" name="field_type_id" id="field_type_id_hourly" required>
                                        <option value="1">สนามกลางแจ้ง</option>
                                        <option value="2">สนามหลังคา</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="booking_date_hourly" class="form-label">ในวันที่</label>
                                    <input type="date" class="form-control" name="booking_date" id="booking_date_hourly"
                                        value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="start_time_hourly" class="form-label">เวลาเริ่ม</label>
                                    <select class="form-select" name="start_time" id="start_time_hourly" required>
                                        @for ($i = 9; $i <= 21; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}:00">{{ sprintf('%02d', $i) }}:00
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_time_hourly" class="form-label">เวลาสิ้นสุด</label>
                                    <select class="form-select" name="end_time" id="end_time_hourly" required>
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
                                <div class="col-12">
                                    <div id="availability-status" class="mt-2 p-2 rounded text-center fw-bold"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary btn-lg">&laquo;กลับแดชบอร์ด</a>
                                <button type="submit" id="submit-booking-btn" class="btn btn-primary btn-lg"disabled>ตรวจสอบราคาและยืนยันคำสั่งซื้อ</button>
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
                                            <td>{{ optional($booking->fieldType)->name ?? 'เหมา' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center p-3 text-muted">ยังไม่มีการจอง</td>
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
            // ดึง Elements ทั้งหมด
            const fieldTypeSelect = document.getElementById('field_type_id_hourly');
            const bookingDateInput = document.getElementById('booking_date_hourly');
            const startTimeSelect = document.getElementById('start_time_hourly');
            const endTimeSelect = document.getElementById('end_time_hourly');
            const statusDiv = document.getElementById('availability-status');
            const submitButton = document.getElementById('submit-booking-btn');

            let debounceTimer;

            // ฟังก์ชันสำหรับเช็คสถานะ
            function performAvailabilityCheck() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const data = {
                        field_type_id: fieldTypeSelect.value,
                        booking_date: bookingDateInput.value,
                        start_time: startTimeSelect.value,
                        end_time: endTimeSelect.value,
                    };

                    // ตรวจสอบข้อมูลเบื้องต้น
                    if (!data.field_type_id || !data.booking_date || !data.start_time || !data.end_time ||
                        data.start_time >= data.end_time) {
                        statusDiv.innerHTML = '';
                        submitButton.disabled = true;
                        statusDiv.className = 'mt-2 p-2 rounded text-center fw-bold';
                        return;
                    }

                    statusDiv.innerHTML = '<span class="text-muted">กำลังตรวจสอบ...</span>';
                    submitButton.disabled = true;
                    statusDiv.className = 'mt-2 p-2 rounded text-center fw-bold alert alert-secondary';

                    fetch('{{ route('api.booking.checkAvailability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.ok ? response.json() : Promise.reject(response))
                        .then(result => {
                            if (result.available) {
                                statusDiv.innerHTML = '✅ ช่วงเวลานี้ว่าง สามารถจองได้';
                                statusDiv.className =
                                    'mt-2 p-2 rounded text-center fw-bold alert alert-success';
                                submitButton.disabled = false;
                            } else {
                                statusDiv.innerHTML = '❌ ขออภัย ช่วงเวลานี้มีผู้จองแล้ว';
                                statusDiv.className =
                                    'mt-2 p-2 rounded text-center fw-bold alert alert-danger';
                                submitButton.disabled = true;
                            }
                        })
                        .catch(error => {
                            statusDiv.innerHTML = 'เกิดข้อผิดพลาดในการตรวจสอบ';
                            statusDiv.className =
                                'mt-2 p-2 rounded text-center fw-bold alert alert-danger';
                            submitButton.disabled = true;
                            console.error('Error:', error);
                        });
                }, 500);
            }
            [fieldTypeSelect, bookingDateInput, startTimeSelect, endTimeSelect].forEach(el => {
                el.addEventListener('change', performAvailabilityCheck);
            });
            performAvailabilityCheck();
        });
    </script>
@endpush
@section('scripts')
    <script>
        // ตรวจสอบว่ามี session 'success' หรือไม่
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                timer: 3000, // แสดงผล 3 วินาทีแล้วหายไป
                showConfirmButton: false
            });
        @endif

        // ตรวจสอบว่ามี session 'error' หรือไม่
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}'
            });
        @endif

        // ตรวจสอบว่ามี session 'warning' หรือไม่
        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'คำเตือน',
                text: '{{ session('warning') }}'
            });
        @endif
    </script>
@endsection
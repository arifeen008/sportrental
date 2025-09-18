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
                        <form action="{{ route('user.booking.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_type" value="membership">
                            <input type="hidden" name="user_membership_id" value="{{ $activeMembership->id }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="field_type_id_membership" class="form-label">ประเภทสนาม</label>
                                    <select class="form-select" name="field_type_id" id="field_type_id_membership" required>
                                        <option value="1">สนามกลางแจ้ง</option>
                                        <option value="2">สนามหลังคา</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="booking_date_membership" class="form-label">ในวันที่</label>
                                    <input type="date" class="form-control" name="booking_date"
                                        id="booking_date_membership" min="{{ now()->addDays(3)->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="start_time_membership" class="form-label">เวลาเริ่ม</label>
                                    <select class="form-select" name="start_time" id="start_time_membership" required>
                                        @for ($i = 9; $i <= 21; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}:00">{{ sprintf('%02d', $i) }}:00
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_time_membership" class="form-label">เวลาสิ้นสุด</label>
                                    <select class="form-select" name="end_time" id="end_time_membership" required>
                                        @for ($i = 10; $i <= 22; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}:00">{{ sprintf('%02d', $i) }}:00
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div id="availability-status-membership" class="mt-2 p-2 rounded text-center fw-bold">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">หมายเหตุ (ถ้ามี)</label>
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg">กลับแดชบอร์ด</a>
                                <button type="submit" id="submit-booking-btn-membership" class="btn btn-primary btn-lg"
                                    disabled>ตรวจสอบชั่วโมงและดำเนินการต่อ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
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
                                            <td>
                                                @if ($booking->booking_type === 'daily_package')
                                                    {{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}
                                                @else
                                                    {{ optional($booking->fieldType)->name ?? 'ไม่ระบุ' }}
                                                @endif
                                            </td>
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
            // ดึง Elements ของหน้านี้โดยเฉพาะ
            const fieldTypeSelect = document.getElementById('field_type_id_membership');
            const bookingDateInput = document.getElementById('booking_date_membership');
            const startTimeSelect = document.getElementById('start_time_membership');
            const endTimeSelect = document.getElementById('end_time_membership');
            const statusDiv = document.getElementById('availability-status-membership');
            const submitButton = document.getElementById('submit-booking-btn-membership');

            let debounceTimer;

            function performAvailabilityCheck() {
                // Logic การทำงานเหมือนกับของหน้า create_hourly ทุกประการ
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const data = {
                        field_type_id: fieldTypeSelect.value,
                        booking_date: bookingDateInput.value,
                        start_time: startTimeSelect.value,
                        end_time: endTimeSelect.value,
                    };

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
                                statusDiv.innerHTML = `✅ ${result.message}`;
                                statusDiv.className =
                                    'mt-2 p-2 rounded text-center fw-bold alert alert-success';
                                submitButton.disabled = false;
                            } else {
                                statusDiv.innerHTML = `❌ ${result.message}`;
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

            // ผูก event listener ให้กับฟิลด์ในหน้านี้
            [fieldTypeSelect, bookingDateInput, startTimeSelect, endTimeSelect].forEach(el => {
                el.addEventListener('change', performAvailabilityCheck);
            });

            // เรียกใช้ครั้งแรกเมื่อโหลดหน้า
            performAvailabilityCheck();
        });
    </script>
@endpush
@include('layouts.partials.sweetalert')

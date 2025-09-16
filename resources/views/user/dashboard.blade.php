@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .booking-card .status-badge {
            font-size: 0.9rem;
        }
    </style>
@endpush


@section('content')
    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h2 class="h4">สวัสดี, {{ Auth::user()->name }}!</h2>
                        <p class="text-muted mb-0">ยินดีต้อนรับสู่แดชบอร์ดของคุณ</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="{{ route('user.create.hourly') }}" class="btn btn-primary"><i
                                class="fas fa-plus-circle me-2"></i>จองสนามรายชั่วโมง</a>
                        <a href="{{ route('user.create.package') }}" class="btn btn-info"><i
                                class="fas fa-calendar-day me-2"></i>จองแบบเหมาวัน</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">


                @if ($activeMembership)
                    <div class="card shadow-sm mb-4 text-white"
                        style="background: linear-gradient(45deg, #2a2a2a, #4a4a4a);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">{{ $activeMembership->membershipTier->tier_name }}</h5>
                                    <p class="card-text small text-white-50">{{ $activeMembership->card_number }}</p>
                                </div>
                                <span class="badge bg-light text-dark">Active</span>
                            </div>

                            <div class="my-4">
                                <label for="remaining-hours" class="form-label">ชั่วโมงคงเหลือ</label>
                                <div class="progress" style="height: 20px;">
                                    @php
                                        $percentage =
                                            ($activeMembership->remaining_hours / $activeMembership->initial_hours) *
                                            100;
                                    @endphp
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;"
                                        aria-valuenow="{{ $activeMembership->remaining_hours }}" aria-valuemin="0"
                                        aria-valuemax="{{ $activeMembership->initial_hours }}">
                                        {{ number_format($activeMembership->remaining_hours) }} ชม.
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <p class="mb-0 small">เจ้าของบัตร</p>
                                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                </div>
                                <div>
                                    <p class="mb-0 small text-end">หมดอายุ</p>
                                    <h6 class="mb-0">{{ thaidate('j M Y', $activeMembership->expires_at) }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-dark">
                            <a href="{{ route('user.create.membership') }}" class="btn btn-success w-100">
                                <i class="fas fa-calendar-plus me-2"></i> จองสนามด้วยบัตรนี้
                            </a>
                        </div>
                    </div>
                @else
                    {{-- ================= ดีไซน์ใหม่สำหรับผู้ใช้ที่ยังไม่มีบัตร ================= --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-id-card fa-3x text-muted mb-3"></i>
                            <h5 class="card-title">คุณยังไม่มีบัตรสมาชิก</h5>
                            <p class="text-muted">สมัครสมาชิกเพื่อรับสิทธิพิเศษและส่วนลดมากมาย!</p>
                            <a href="{{ route('user.purchase.index') }}" class="btn btn-primary">
                                <i class="fas fa-credit-card me-2"></i> ดูและซื้อบัตรสมาชิก
                            </a>
                        </div>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัสการจอง</th>
                                        <th>วันที่ใช้บริการ</th>
                                        <th>รายละเอียด (สนามและเวลา)</th>
                                        <th class="text-end">ยอดชำระ (บาท)</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center" style="min-width: 150px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($myBookings as $booking)
                                        <tr>
                                            {{-- รหัสการจอง --}}
                                            <td><strong>{{ $booking->booking_code }}</strong></td>

                                            {{-- วันที่ใช้บริการ --}}
                                            <td>{{ thaidate('j M Y', $booking->booking_date) }}</td>

                                            {{-- รายละเอียด (สนามและเวลา) --}}
                                            <td>
                                                @if ($booking->booking_type === 'daily_package')
                                                    <strong>{{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}</strong>
                                                    <small
                                                        class="d-block text-muted">{{ $booking->price_calculation_details['rental_type'] ?? '' }}</small>
                                                @else
                                                    <strong>{{ optional($booking->fieldType)->name ?? 'ไม่ระบุ' }}</strong>
                                                    <small class="d-block text-muted">
                                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} น.
                                                    </small>
                                                @endif
                                            </td>

                                            {{-- ยอดชำระ --}}
                                            <td class="text-end">{{ number_format($booking->total_price, 2) }}</td>

                                            {{-- สถานะ --}}
                                            <td class="text-center">
                                                @if ($booking->status == 'paid' || $booking->status == 'confirmed')
                                                    <span class="badge bg-success">ชำระเงินแล้ว</span>
                                                @elseif($booking->status == 'unpaid' || $booking->status == 'pending_payment')
                                                    <span class="badge bg-warning text-dark">รอชำระเงิน</span>
                                                @elseif($booking->status == 'verifying')
                                                    <span class="badge bg-info">รอตรวจสอบ</span>
                                                @elseif($booking->status == 'rejected')
                                                    <span class="badge bg-danger">ถูกปฏิเสธ</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-secondary">ยกเลิกแล้ว</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $booking->status }}</span>
                                                @endif
                                            </td>

                                            {{-- ปุ่มจัดการ --}}
                                            <td class="text-center">
                                                {{-- ถ้ายังไม่จ่ายเงิน ให้แสดงปุ่มแจ้งชำระเงิน --}}
                                                @if ($booking->status == 'unpaid' || $booking->status == 'pending_payment')
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#uploadSlipModal-{{ $booking->id }}">
                                                        <i class="fas fa-upload me-1"></i> แจ้งชำระเงิน
                                                    </button>
                                                @else
                                                    {{-- ในกรณีอื่นๆ ทั้งหมด (จ่ายแล้ว, รอตรวจ, ถูกปฏิเสธ) ให้แสดงปุ่มดูรายละเอียด --}}
                                                    <a href="{{ route('user.booking.show', $booking) }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-eye me-1"></i> รายละเอียด
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <p class="mb-0">คุณยังไม่มีประวัติการจอง</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($myBookings->hasPages())
                        <div class="card-footer">
                            {{ $myBookings->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            ตารางจองที่ยืนยันแล้ว (7 วันข้างหน้า)
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($confirmedBookings->isEmpty())
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>ยังไม่มีการจองที่ยืนยันแล้วในช่วงนี้</p>
                            </div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach ($confirmedBookings as $booking)
                                    <li class="list-group-item px-0 py-3">
                                        <div class="d-flex w-100">
                                            <div class="text-center me-3" style="width: 65px;">
                                                <div class="bg-light rounded p-1 border">
                                                    <span
                                                        class="d-block small text-danger fw-bold">{{ thaidate('M', $booking->booking_date) }}</span>
                                                    <span class="d-block h4 mb-0">{{ $booking->booking_date->day }}</span>
                                                    <span
                                                        class="d-block small text-muted">{{ thaidate('D', $booking->booking_date) }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    @if ($booking->booking_type === 'daily_package')
                                                        <i class="fas fa-sun text-warning me-1"></i>
                                                        {{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}
                                                    @else
                                                        <i class="fas fa-futbol text-primary me-1"></i>
                                                        {{ optional($booking->fieldType)->name ?? 'ไม่ระบุ' }}
                                                    @endif
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} น.
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    @foreach ($myBookings->where('status', 'pending_payment') as $booking)
        <div class="modal fade" id="uploadSlipModal-{{ $booking->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('user.booking.uploadSlip', $booking) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i>แจ้งชำระเงินสำหรับ:
                                {{ $booking->booking_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <p class="mb-1">ยอดที่ต้องชำระ: <strong
                                        class="fs-5">{{ number_format($booking->total_price, 2) }} บาท</strong></p>
                                <p class="small mb-0">โอนเงินมาที่: ธ.กสิกรไทย 255-1-03447-2 (สหกรณ์อิสลามษะกอฟะฮ จำกัด)
                                </p>
                            </div>
                            <div class="text-center mb-3">
                                <img id="slip-preview-{{ $booking->id }}" src="#" alt="ตัวอย่างสลิป"
                                    class="img-fluid rounded" style="max-height: 200px; display: none;">
                            </div>
                            <div class="mb-3">
                                <label for="slipImage-{{ $booking->id }}" class="form-label">เลือกไฟล์รูปภาพสลิป</label>
                                <input class="form-control" type="file" name="slip_image"
                                    id="slipImage-{{ $booking->id }}" required onchange="previewSlip(event)">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">ยืนยันการแจ้งชำระเงิน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('scripts')
    <script>
        function previewSlip(event) {
            const bookingId = event.target.id.split('-')[1];
            const previewImage = document.getElementById('slip-preview-' + bookingId);
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
@endpush
@push('scripts')
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
@endpush

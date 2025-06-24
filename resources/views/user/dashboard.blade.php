@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .booking-card .status-badge {
            font-size: 0.9rem;
        }
    </style>
@endsection


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
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="far fa-id-card me-2"></i> บัตรสมาชิกของคุณ</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-2">
                                <dt class="col-sm-4">ประเภทบัตร:</dt>
                                <dd class="col-sm-8">{{ $activeMembership->membershipTier->tier_name }}</dd>
                                <dt class="col-sm-4">ชั่วโมงคงเหลือ:</dt>
                                <dd class="col-sm-8 fw-bold fs-5 text-primary">
                                    {{ number_format($activeMembership->remaining_hours, 2) }} ชม.</dd>
                                <dt class="col-sm-4">วันหมดอายุ:</dt>
                                <dd class="col-sm-8">{{ thaidate('j F Y', $activeMembership->expires_at) }}</dd>
                            </dl>
                            <a href="{{ route('user.create.membership') }}" class="btn btn-success w-100">
                                <i class="fas fa-calendar-plus me-2"></i> จองสนามด้วยบัตรนี้
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
                                        <th>รายละเอียด</th>
                                        <th class="text-end">ยอดชำระ (บาท)</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($myBookings as $booking)
                                        <tr>
                                            <td><strong>{{ $booking->booking_code }}</strong></td>
                                            <td>{{ thaidate('j M Y', $booking->booking_date) }}</td>
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
                                            <td class="text-end">{{ number_format($booking->total_price, 2) }}</td>
                                            <td class="text-center">
                                                @if ($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">ชำระเงินแล้ว</span>
                                                @elseif($booking->payment_status == 'unpaid')
                                                    <span class="badge bg-warning text-dark">รอชำระเงิน</span>
                                                @elseif($booking->payment_status == 'verifying')
                                                    <span class="badge bg-info">รอตรวจสอบ</span>
                                                @elseif($booking->payment_status == 'rejected')
                                                    <span class="badge bg-danger">ถูกปฏิเสธ</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $booking->payment_status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($booking->payment_status == 'unpaid')
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#uploadSlipModal-{{ $booking->id }}">
                                                        <i class="fas fa-upload me-1"></i> แจ้งชำระเงิน
                                                    </button>
                                                @else
                                                    <a href="#" class="btn btn-sm btn-outline-secondary disabled"
                                                        aria-disabled="true">รายละเอียด</a>
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


    @foreach ($myBookings->where('payment_status', 'unpaid') as $booking)
        <div class="modal fade" id="uploadSlipModal-{{ $booking->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('user.booking.uploadSlip', $booking) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i>แจ้งชำระเงินสำหรับ:
                                {{ $booking->booking_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <p class="mb-1">ยอดที่ต้องชำระ: <strong
                                        class="fs-5">{{ number_format($booking->total_price, 2) }} บาท</strong></p>
                                <p class="small mb-0">โอนเงินมาที่: ธ.กสิกรไทย 255-1-03447-2 (สหกรณ์อิสลามปะกาสัย)</p>
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

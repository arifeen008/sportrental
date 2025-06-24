@extends('layouts.admin')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">ภาพรวมระบบ</h1>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">การจองวันนี้</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $bookingsTodayCount }} รายการ</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar-day fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">รอตรวจสอบสลิป</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $pendingVerificationCount }} รายการ</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">รายได้เดือนนี้ (โดยประมาณ)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($monthlyRevenue, 2) }} บาท</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 fw-bold text-primary">รายการที่ต้องดำเนินการ</h6>
                </div>
                <div class="card-body">
                    {{-- ในการ์ด "การจองที่ต้องดำเนินการ" --}}

                    @forelse($actionRequiredBookings as $booking)
                        <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center p-3">

                            {{-- ส่วนแสดงรายละเอียดที่แก้ไขใหม่ --}}
                            <div>
                                <strong class="d-block">{{ $booking->user->name }} - (จอง
                                    #{{ $booking->booking_code }})</strong>
                                <div class="text-muted small mt-1">
                                    <span class="me-3">
                                        <i class="fas fa-calendar-alt fa-fw"></i>
                                        {{ thaidate('j F Y', $booking->booking_date) }}
                                    </span>
                                    <span class="me-3">
                                        <i class="fas fa-clock fa-fw"></i>
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} น.
                                    </span>
                                    <br>
                                    <span class="me-3">
                                        <i class="fas fa-futbol fa-fw"></i>
                                        @if ($booking->booking_type === 'daily_package')
                                            {{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}
                                        @else
                                            {{ optional($booking->fieldType)->name ?? 'ไม่ระบุ' }}
                                        @endif
                                    </span>
                                    <span class="me-3">
                                        <i class="fas fa-money-bill-wave fa-fw"></i>
                                        ราคา: <strong
                                            class="text-dark">{{ number_format($booking->total_price, 2) }}</strong> บาท
                                    </span>
                                </div>
                            </div>

                            {{-- ส่วนปุ่มจัดการ (เหมือนเดิม) --}}
                            <div class="mt-2 mt-md-0 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#viewSlipModal-{{ $booking->id }}" title="ดูสลิป">
                                    <i class="fas fa-receipt"></i> ดูสลิป
                                </button>
                                <form action="{{ route('admin.booking.approve', $booking) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('ยืนยันการอนุมัติการจอง #{{ $booking->booking_code }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="อนุมัติ">
                                        <i class="fas fa-check"></i> อนุมัติ
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal-{{ $booking->id }}" title="ปฏิเสธ">
                                    <i class="fas fa-times"></i> ปฏิเสธ
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-success text-center mb-0" role="alert">
                            ไม่มีรายการที่ต้องดำเนินการในขณะนี้
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">สถิติการจอง 7 วันล่าสุด</h6>
                </div>
                <div class="card-body"><canvas id="bookingChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- Modals สำหรับ Action ต่างๆ --}}
    @foreach ($actionRequiredBookings as $booking)
        <div class="modal fade" id="viewSlipModal-{{ $booking->id }}" tabindex="-1"
            aria-labelledby="slipModalLabel-{{ $booking->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="slipModalLabel-{{ $booking->id }}">สลิปการโอนเงิน: การจอง
                            {{ $booking->booking_code }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        @if ($booking->slip_image_path)
                            <img src="{{ Storage::url($booking->slip_image_path) }}" class="img-fluid"
                                alt="Payment Slip for Booking {{ $booking->booking_code }}">
                        @else
                            <p class="text-danger">ไม่พบไฟล์สลิป</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rejectModal-{{ $booking->id }}" tabindex="-1"
            aria-labelledby="rejectModalLabel-{{ $booking->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.booking.reject', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel-{{ $booking->id }}">ปฏิเสธการจอง
                                {{ $booking->booking_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="rejection_reason-{{ $booking->id }}"
                                    class="form-label">กรุณาระบุเหตุผลที่ปฏิเสธ:</label>
                                <textarea class="form-control" name="rejection_reason" id="rejection_reason-{{ $booking->id }}" rows="3"
                                    required placeholder="เช่น สลิปไม่ถูกต้อง, ยอดเงินไม่ตรง"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-danger">ยืนยันการปฏิเสธ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bookingChart').getContext('2d');
            const bookingChart = new Chart(ctx, {
                type: 'bar', // เปลี่ยนเป็น Bar Chart อาจจะดูง่ายกว่า
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'จำนวนการจอง',
                        data: @json($chartValues),
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush

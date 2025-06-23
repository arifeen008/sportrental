@extends('layouts.app')

@section('styles')
    {{-- เพิ่ม Font Awesome CDN สำหรับไอคอนสวยๆ --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endsection

@section('content')
    <div class="container py-4">
        {{-- ส่วนต้อนรับ Admin --}}
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title text-primary mb-1">Admin Dashboard</h2>
                                <p class="card-text text-muted">ยินดีต้อนรับ, {{ Auth::user()->name }}</p>
                            </div>
                            <a href="{{ route('user.booking.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i> สร้างการจองใหม่
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- การ์ดสำหรับแสดงรายการที่ต้องจัดการ --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-exclamation-triangle me-2"></i> การจองที่ต้องดำเนินการ
                            ({{ $bookings->where('payment_status', 'verifying')->count() }} รายการ)
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($bookings->where('payment_status', 'verifying') as $booking)
                            <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center p-3">
                                <div>
                                    <strong>#{{ $booking->booking_code  }} - {{ $booking->user->name }}</strong>
                                    <small class="d-block text-muted">
                                        วันที่ใช้บริการ: {{ $booking->booking_date->format('d/m/Y') }} | ยอดเงิน:
                                        {{ number_format($booking->total_price, 2) }} บาท
                                    </small>
                                </div>
                                <div class="mt-2 mt-md-0 d-flex justify-content-end gap-2">
                                    {{-- ปุ่มสำหรับเปิด Modal ดูสลิป --}}
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#viewSlipModal-{{ $booking->booking_code  }}" title="ดูสลิป">
                                        <i class="fas fa-receipt"></i> ดูสลิป
                                    </button>
                                    {{-- ฟอร์มสำหรับส่งคำสั่ง 'อนุมัติ' --}}
                                    <form action="{{ route('admin.booking.approve', $booking) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('ยืนยันการอนุมัติการจอง #{{ $booking->booking_code  }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="อนุมัติ">
                                            <i class="fas fa-check"></i> อนุมัติ
                                        </button>
                                    </form>
                                    {{-- เพิ่ม: ปุ่มสำหรับเปิด Modal ปฏิเสธการจอง --}}
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal-{{ $booking->booking_code  }}" title="ปฏิเสธ">
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
        </div>

        {{-- ตารางแสดงประวัติการจองทั้งหมด --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> ประวัติการจองทั้งหมด</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ผู้จอง</th>
                                        <th>วันที่ใช้บริการ</th>
                                        <th>รายการ</th> {{-- แก้ไข: เพิ่มข้อมูลส่วนนี้ --}}
                                        <th class="text-end">ยอดชำระ</th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->booking_code  }}</td>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                            <td>
                                                {{-- แก้ไข: เพิ่มการแสดงผลรายละเอียด --}}
                                                @if ($booking->booking_type === 'daily_package')
                                                    <strong>{{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}</strong>
                                                    <small
                                                        class="d-block text-muted">{{ $booking->price_calculation_details['rental_type'] ?? '' }}</small>
                                                @else
                                                    <strong>{{ $booking->fieldType->name ?? 'ไม่ระบุสนาม' }}</strong>
                                                    <small
                                                        class="d-block text-muted">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                                        - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                        น.</small>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($booking->total_price, 2) }}</td>
                                            <td class="text-center">
                                                @if ($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">ชำระเงินแล้ว</span>
                                                @elseif($booking->payment_status == 'unpaid')
                                                    <span class="badge bg-secondary">ยังไม่ชำระเงิน</span>
                                                @elseif($booking->payment_status == 'verifying')
                                                    <span class="badge bg-warning text-dark">รอตรวจสอบ</span>
                                                @elseif($booking->payment_status == 'rejected')
                                                    <span class="badge bg-danger">ถูกปฏิเสธ</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $booking->payment_status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">ยังไม่มีประวัติการจอง
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= ส่วน Modals (แสดงสลิป และ ปฏิเสธ) ================= --}}
    @foreach ($bookings->where('payment_status', 'verifying') as $booking)
        {{-- Modal สำหรับดูสลิป --}}
        <div class="modal fade" id="viewSlipModal-{{ $booking->booking_code  }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">สลิปสำหรับการจอง #{{ $booking->booking_code  }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        @if ($booking->slip_image_path)
                            <img src="{{ Storage::url($booking->slip_image_path) }}" class="img-fluid" alt="Payment Slip">
                        @else
                            <p class="text-danger">ไม่พบไฟล์สลิป</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal สำหรับปฏิเสธการจอง --}}
        <div class="modal fade" id="rejectModal-{{ $booking->booking_code  }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.booking.reject', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">ปฏิเสธการจอง #{{ $booking->booking_code  }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="rejection_reason-{{ $booking->booking_code  }}"
                                    class="form-label">กรุณาระบุเหตุผลที่ปฏิเสธ:</label>
                                <textarea class="form-control" name="rejection_reason" id="rejection_reason-{{ $booking->booking_code  }}" rows="3"
                                    required></textarea>
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

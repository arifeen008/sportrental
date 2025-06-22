@extends('layouts.app')

{{-- เพิ่ม Font Awesome CDN สำหรับไอคอนสวยๆ --}}
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endsection


@section('content')
    <div class="container py-4">
        {{-- ส่วนต้อนรับและปุ่มจอง --}}
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h2 class="card-title text-primary mb-3">สวัสดี, {{ Auth::user()->name }}!</h2>
                        <p class="card-text lead">ยินดีต้อนรับสู่แดชบอร์ดส่วนตัวของคุณ</p>
                        <hr>
                        <div class="d-grid gap-2 col-md-6 mx-auto">
                            <a href="{{ route('user.booking.create') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-plus-circle me-2"></i> ทำการจองใหม่
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ส่วนตารางประวัติการจอง --}}
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i> ประวัติการจองทั้งหมด
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัสจอง</th>
                                        <th>วันที่ใช้บริการ</th>
                                        <th>รายการ</th>
                                        <th class="text-end">ยอดชำระ</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- ตรวจสอบว่ามีข้อมูลการจองหรือไม่ --}}
                                    @forelse ($bookings as $booking)
                                        <tr>
                                            {{-- รหัสการจอง --}}
                                            <td>#{{ $booking->id }}</td>

                                            {{-- วันที่ใช้บริการ --}}
                                            <td>{{ $booking->booking_date->format('d M Y') }}</td>

                                            {{-- รายละเอียดการจอง --}}
                                            <td>
                                                @if ($booking->booking_type === 'daily_package')
                                                    {{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}
                                                    <small
                                                        class="d-block text-muted">{{ $booking->price_calculation_details['rental_type'] ?? '' }}</small>
                                                @else
                                                    {{ $booking->fieldType->name ?? 'N/A' }}
                                                    <small class="d-block text-muted">
                                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} น.
                                                    </small>
                                                @endif
                                            </td>

                                            {{-- ยอดชำระ --}}
                                            <td class="text-end">{{ number_format($booking->total_price, 2) }}</td>

                                            {{-- สถานะ (แสดงด้วย Badge สีต่างๆ) --}}
                                            <td class="text-center">
                                                @if ($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">ชำระเงินแล้ว</span>
                                                @elseif($booking->payment_status == 'unpaid')
                                                    <span class="badge bg-warning text-dark">รอชำระเงิน</span>
                                                @elseif($booking->payment_status == 'verifying')
                                                    <span class="badge bg-info">รอตรวจสอบ</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $booking->payment_status }}</span>
                                                @endif
                                            </td>

                                            {{-- ปุ่มจัดการ (แสดงตามสถานะ) --}}
                                            <td class="text-center">
                                                @if ($booking->payment_status == 'unpaid')
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#uploadSlipModal-{{ $booking->id }}">
                                                        <i class="fas fa-upload me-1"></i> อัปโหลดสลิป
                                                    </button>
                                                @else
                                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- กรณีไม่พบข้อมูลการจองเลย --}}
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">คุณยังไม่มีประวัติการจอง
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


    {{-- ================= ส่วน Modal สำหรับอัปโหลดสลิป ================= --}}
    {{-- จะวนลูปสร้าง Modal ตามจำนวน booking ที่ยังไม่จ่ายเงิน --}}
    @foreach ($bookings->where('payment_status', 'unpaid') as $booking)
        <div class="modal fade" id="uploadSlipModal-{{ $booking->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- อย่าลืมเพิ่ม route สำหรับอัปโหลดสลิป --}}
                    <form action="{{ route('user.booking.uploadSlip', $booking->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">อัปโหลดสลิปสำหรับ #{{ $booking->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>ยอดชำระ: <strong class="text-danger">{{ number_format($booking->total_price, 2) }}
                                    บาท</strong></p>
                            <div class="mb-3">
                                <label for="slipImage-{{ $booking->id }}" class="form-label">เลือกไฟล์รูปภาพสลิป</label>
                                <input class="form-control" type="file" name="slip_image"
                                    id="slipImage-{{ $booking->id }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">ยืนยันการอัปโหลด</button>
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

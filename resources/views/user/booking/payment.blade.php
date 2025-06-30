@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endsection

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-hourglass-half me-2"></i>ขั้นตอนสุดท้าย: ชำระเงิน</h4>
                    </div>
                    <div class="card-body p-4">

                        {{-- นาฬิกานับถอยหลัง --}}
                        <div id="countdown-timer" class="alert alert-warning text-center">
                            <h5 class="alert-heading">เหลือเวลาในการชำระเงิน</h5>
                            <p id="timer" class="display-4 fw-bold mb-0">15:00</p>
                        </div>

                        {{-- สรุปรายการ --}}
                        <h5 class="mt-4">สรุปการจอง</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>รหัสการจอง:</span>
                                <strong>{{ $booking->booking_code }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>วันที่:</span>
                                <strong>{{ thaidate('j F Y', $booking->booking_date) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>เวลา:</span>
                                <strong>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <span class="fw-bold">ยอดที่ต้องชำระ:</span>
                                <strong class="fs-5 text-danger">{{ number_format($booking->total_price, 2) }} บาท</strong>
                            </li>
                        </ul>

                        <hr class="my-4">

                        {{-- ฟอร์มอัปโหลดสลิป --}}
                        <div id="upload-section">
                            <h5 class="mb-3">แจ้งการชำระเงิน</h5>
                            <div class="alert alert-info small">
                                <strong>ช่องทางการโอนเงิน:</strong><br>
                                ธ.กสิกรไทย 255-1-03447-2 (สหกรณ์อิสลามษะกอฟะฮ จำกัด)
                            </div>
                            <form action="{{ route('user.booking.uploadSlip', $booking) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="slip_image" class="form-label">อัปโหลดสลิปการโอนเงิน</label>
                                    <input class="form-control" type="file" name="slip_image" id="slip_image" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">ยืนยันการชำระเงิน</button>
                                </div>
                            </form>
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
            const timerElement = document.getElementById('timer');
            const countdownWrapper = document.getElementById('countdown-timer');
            const uploadSection = document.getElementById('upload-section');

            // แปลงเวลาหมดอายุจาก PHP มาให้ JavaScript ใช้งาน
            const expiryTime = new Date('{{ $booking->expires_at->toIso8601String() }}').getTime();

            const countdownInterval = setInterval(function() {
                const now = new Date().getTime();
                const distance = expiryTime - now;

                // คำนวณนาทีและวินาทีที่เหลือ
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                if (distance < 0) {
                    // เมื่อหมดเวลา
                    clearInterval(countdownInterval);
                    timerElement.textContent = "หมดเวลา";
                    countdownWrapper.classList.remove('alert-warning');
                    countdownWrapper.classList.add('alert-danger');
                    // ปิดการใช้งานฟอร์มอัปโหลด
                    uploadSection.innerHTML =
                        '<p class="text-center text-danger">การจองนี้หมดเวลาในการชำระเงินแล้ว กรุณาทำการจองใหม่อีกครั้ง</p>';
                } else {
                    // แสดงผลนาฬิกา
                    timerElement.textContent = minutes.toString().padStart(2, '0') + ":" + seconds
                        .toString().padStart(2, '0');
                }
            }, 1000);
        });
    </script>
@endpush

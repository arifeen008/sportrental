@extends('layouts.app')
@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>ขั้นตอนสุดท้าย: ชำระเงิน</h4>
                    </div>
                    <div class="card-body">
                        <div id="countdown-timer" class="alert alert-warning text-center">
                            กรุณาชำระเงินและอัปโหลดสลิปภายใน: <strong id="timer">15:00</strong> นาที
                        </div>
                        {{-- ... โค้ดสรุปรายละเอียดการจอง และ ฟอร์มอัปโหลดสลิป ... --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // JavaScript สำหรับนับถอยหลัง
    </script>
@endpush
@push('styles')
    <style>
        /* CSS สำหรับปรับแต่งหน้าชำระเงิน */
        #countdown-timer {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
@endpush

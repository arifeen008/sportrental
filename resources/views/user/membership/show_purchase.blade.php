@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .slip-preview img {
            max-height: 250px;
            cursor: pointer;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
@endsection

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">รายละเอียดการสั่งซื้อบัตรสมาชิก</h4>
                </div>
                <div class="card-body p-4">
                    {{-- ส่วนแสดงรายละเอียด --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">รายการสั่งซื้อ</h5>
                            <p class="mb-1"><strong>รหัสสั่งซื้อ:</strong> {{ $purchase->purchase_code }}</p>
                            <p class="mb-1"><strong>ประเภทบัตร:</strong> {{ $purchase->membershipTier->tier_name }}</p>
                            <p class="mb-1"><strong>วันที่สั่งซื้อ:</strong> {{ thaidate('j F Y, H:i น.', $purchase->created_at) }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="mb-3">สถานะรายการ</h5>
                            @if($purchase->status == 'pending_payment')
                                <span class="badge bg-warning text-dark fs-6">รอชำระเงิน</span>
                            @elseif($purchase->status == 'verifying')
                                <span class="badge bg-info fs-6">รอการตรวจสอบ</span>
                            @elseif($purchase->status == 'completed')
                                <span class="badge bg-success fs-6">ดำเนินการสำเร็จ</span>
                            @elseif($purchase->status == 'rejected')
                                <span class="badge bg-danger fs-6">ถูกปฏิเสธ</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    {{-- ส่วนการชำระเงินและอัปโหลดสลิป --}}
                    <div class="mt-4">
                        {{-- ใช้ @if เพื่อแสดงผลตามสถานะ --}}
                        @if($purchase->status == 'pending_payment')
                            <h5 class="mb-3">ขั้นตอนต่อไป: ชำระเงินและแจ้งโอน</h5>
                            <div class="alert alert-secondary">
                                <p class="mb-1">กรุณาชำระเงินจำนวน <strong class="fs-4 text-danger">{{ number_format($purchase->price, 2) }}</strong> บาท</p>
                                <p class="small mb-0">มาที่บัญชี: ธ.กสิกรไทย 255-1-03447-2 (สหกรณ์อิสลามปะกาสัย)</p>
                            </div>
                            <form action="{{ route('user.purchase.uploadSlip', $purchase) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="slip_image" class="form-label">อัปโหลดสลิปการโอนเงินที่นี่</label>
                                    <input type="file" name="slip_image" id="slip_image" class="form-control" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">ยืนยันการแจ้งชำระเงิน</button>
                                </div>
                            </form>
                        @elseif($purchase->status == 'verifying')
                            <h5 class="mb-3">สลิปของคุณที่แนบมา</h5>
                            <p class="text-muted">เราได้รับข้อมูลการชำระเงินของคุณแล้ว และกำลังดำเนินการตรวจสอบ โปรดรอการยืนยันจากเจ้าหน้าที่</p>
                            <div class="text-center slip-preview">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#viewSlipModal">
                                    <img src="{{ Storage::url($purchase->slip_image_path) }}" class="img-fluid rounded" alt="Payment Slip">
                                </a>
                            </div>
                        @elseif($purchase->status == 'completed')
                             <div class="alert alert-success text-center">
                                <h5>ดำเนินการสำเร็จ!</h5>
                                <p class="mb-0">บัตรสมาชิกของคุณถูกเปิดใช้งานเรียบร้อยแล้ว สามารถตรวจสอบได้ที่หน้าแดชบอร์ด</p>
                            </div>
                        @elseif($purchase->status == 'rejected')
                            <div class="alert alert-danger">
                                <h5>รายการถูกปฏิเสธ</h5>
                                <p class="mb-0"><strong>เหตุผล:</strong> {{ $purchase->rejection_reason ?? 'กรุณาติดต่อเจ้าหน้าที่' }}</p>
                            </div>
                        @endif
                    </div>
                     <div class="mt-4 text-center">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">&laquo; กลับสู่แดชบอร์ด</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($purchase->slip_image_path)
<div class="modal fade" id="viewSlipModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="{{ Storage::url($purchase->slip_image_path) }}" class="img-fluid">
      </div>
    </div>
  </div>
</div>
@endif
@endsection
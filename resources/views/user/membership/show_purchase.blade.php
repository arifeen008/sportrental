@extends('layouts.app')
@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>ยืนยันการสั่งซื้อและแจ้งชำระเงิน</h4>
                    <p class="mb-0">รหัสสั่งซื้อ: {{ $purchase->purchase_code }}</p>
                </div>
                <div class="card-body">
                    <h5>รายการ: {{ $purchase->membershipTier->tier_name }}</h5>
                    <p>กรุณาชำระเงินจำนวน <strong class="fs-4 text-danger">{{ number_format($purchase->price, 2) }}</strong> บาท เพื่อเปิดใช้งานบัตร</p>
                    <div class="alert alert-info">
                        <strong>ช่องทางการโอนเงิน:</strong><br>
                        ธ.กสิกรไทย 255-1-03447-2 (สหกรณ์อิสลามปะกาสัย)
                    </div>
                    <hr>
                    <h5>อัปโหลดสลิปการโอนเงิน</h5>
                    <form action="{{ route('membership.purchase.uploadSlip', $purchase) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="slip_image" class="form-label">เลือกไฟล์สลิป</label>
                            <input type="file" name="slip_image" id="slip_image" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">ยืนยันการแจ้งชำระเงิน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
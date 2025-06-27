@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">จัดการรายการสั่งซื้อบัตรสมาชิก</h1>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        {{-- ฟอร์มสำหรับค้นหา (ถ้าต้องการในอนาคต) --}}
        <form action="{{ route('admin.purchases.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" 
                       placeholder="ค้นหาด้วยรหัสสั่งซื้อ หรือ ชื่อผู้ซื้อ..." 
                       value="{{ request('search') }}">
                <button class="btn btn-secondary" type="submit">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>รหัสสั่งซื้อ</th>
                        <th>ผู้ซื้อ</th>
                        <th>ประเภทบัตร</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th class="text-end">ราคา (บาท)</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td><strong>{{ $purchase->purchase_code }}</strong></td>
                            <td>{{ $purchase->user->name }}</td>
                            <td>{{ $purchase->membershipTier->tier_name }}</td>
                            <td>{{ thaidate('j M y', $purchase->created_at) }}</td>
                            <td class="text-end">{{ number_format($purchase->price, 2) }}</td>
                            <td class="text-center">
                                @if($purchase->status == 'completed')
                                    <span class="badge bg-success">สำเร็จ</span>
                                @elseif($purchase->status == 'pending_payment')
                                    <span class="badge bg-secondary">รอชำระเงิน</span>
                                @elseif($purchase->status == 'verifying')
                                    <span class="badge bg-warning text-dark">รอตรวจสอบ</span>
                                @elseif($purchase->status == 'rejected')
                                    <span class="badge bg-danger">ถูกปฏิเสธ</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($purchase->status === 'verifying')
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- ปุ่มดูสลิป --}}
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewSlipModal-{{ $purchase->id }}" title="ดูสลิป">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                        {{-- ปุ่มอนุมัติ --}}
                                        <form action="{{ route('admin.purchases.approve', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการอนุมัติรายการนี้?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="อนุมัติ"><i class="fas fa-check"></i></button>
                                        </form>
                                        {{-- ปุ่มปฏิเสธ --}}
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $purchase->id }}" title="ปฏิเสธ"><i class="fas fa-times"></i></button>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">ไม่พบรายการสั่งซื้อ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($purchases->hasPages())
    <div class="card-footer">
        {{ $purchases->appends(request()->input())->links() }}
    </div>
    @endif
</div>

@foreach($purchases->where('status', 'verifying') as $purchase)
    <div class="modal fade" id="viewSlipModal-{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">สลิปสำหรับ: {{ $purchase->purchase_code }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    @if($purchase->slip_image_path)
                        <img src="{{ Storage::url($purchase->slip_image_path) }}" class="img-fluid rounded" alt="Payment Slip">
                    @else
                        <p class="text-danger">ไม่พบไฟล์สลิป</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal-{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.purchases.reject', $purchase) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">ปฏิเสธรายการสั่งซื้อ {{ $purchase->purchase_code }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejection_reason-{{ $purchase->id }}" class="form-label">กรุณาระบุเหตุผลที่ปฏิเสธ:</label>
                            <textarea class="form-control" name="rejection_reason" id="rejection_reason-{{ $purchase->id }}" rows="3" required></textarea>
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
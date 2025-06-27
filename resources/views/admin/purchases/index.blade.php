@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-4">จัดการรายการสั่งซื้อบัตรสมาชิก</h1>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>รหัสสั่งซื้อ</th>
                        <th>ผู้ซื้อ</th>
                        <th>ประเภทบัตร</th>
                        <th class="text-end">ราคา</th>
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
                            <td class="text-end">{{ number_format($purchase->price, 2) }}</td>
                            <td class="text-center">
                                {{-- ... โค้ด Badge แสดงสถานะ ... --}}
                            </td>
                            <td class="text-center">
                                @if($purchase->status === 'verifying')
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ Storage::url($purchase->slip_image_path) }}" target="_blank" class="btn btn-sm btn-info" title="ดูสลิป"><i class="fas fa-receipt"></i></a>
                                        <form action="{{ route('admin.purchases.approve', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการอนุมัติรายการนี้?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="อนุมัติ"><i class="fas fa-check"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $purchase->id }}" title="ปฏิเสธ"><i class="fas fa-times"></i></button>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">ไม่พบรายการสั่งซื้อ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($purchases->hasPages())
        <div class="card-footer">{{ $purchases->links() }}</div>
    @endif
</div>

@foreach($purchases->where('status', 'verifying') as $purchase)
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
                    <label for="rejection_reason-{{ $purchase->id }}" class="form-label">กรุณาระบุเหตุผล:</label>
                    <textarea class="form-control" name="rejection_reason" id="rejection_reason-{{ $purchase->id }}" rows="3" required></textarea>
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
@extends('layouts.admin')

@section('content')
    <h1 class="h3 mb-4">ประวัติการจองทั้งหมด</h1>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>รหัสจอง</th>
                            <th>ผู้จอง</th>
                            <th>วันที่ใช้บริการ</th>
                            <th>รายการ</th>
                            <th class="text-end">ยอดชำระ</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">ดูรายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->booking_code }}</strong></td>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ thaidate('j M Y', $booking->booking_date) }}</td>
                                <td>
                                    @if ($booking->booking_type === 'daily_package')
                                        {{ $booking->price_calculation_details['package_name'] ?? 'เหมาวัน' }}
                                    @else
                                        {{ optional($booking->fieldType)->name ?? 'ไม่ระบุ' }}
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
                                <td class="text-center">
                                    <a href="{{ route('admin.booking.show', $booking) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">ไม่พบข้อมูลการจอง</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($bookings->hasPages())
            <div class="card-footer">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection

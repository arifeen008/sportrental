@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">ข้อมูลสมาชิก: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">&laquo; กลับไปที่รายชื่อ</a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">ข้อมูลส่วนตัว</h5>
                </div>
                <div class="card-body">
                    <p><strong>อีเมล:</strong> {{ $user->email }}</p>
                    <p><strong>เบอร์โทรศัพท์:</strong> {{ $user->phone_number ?? '-' }}</p>
                    <p><strong>วันที่สมัคร:</strong> {{ thaidate('j F Y', $user->created_at) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">ประวัติการจอง ({{ $user->bookings->count() }} รายการ)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <tbody>
                                @forelse($user->bookings as $booking)
                                    <tr>
                                        <td><a
                                                href="{{ route('admin.booking.show', $booking) }}">{{ $booking->booking_code }}</a>
                                        </td>
                                        <td>{{ thaidate('d M y', $booking->booking_date) }}</td>
                                        <td>{{ optional($booking->fieldType)->name ?? 'เหมาวัน' }}</td>
                                        <td class="text-center"><span
                                                class="badge bg-success">{{ $booking->payment_status }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center p-3">ไม่มีประวัติการจอง</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">ประวัติบัตรสมาชิก ({{ $user->userMemberships->count() }} ใบ)</h5>
                </div>
                <div class="card-body">
                    @forelse($user->userMemberships as $membership)
                        <div class="mb-2 pb-2 border-bottom">
                            <strong>{{ $membership->membershipTier->tier_name }}</strong>
                            <p class="small text-muted mb-0">
                                {{ $membership->card_number }} |
                                สถานะ: <span class="fw-bold">{{ $membership->status }}</span> |
                                หมดอายุ: {{ thaidate('j M y', $membership->expires_at) }}
                            </p>
                        </div>
                    @empty
                        <p class="text-center text-muted">ไม่มีประวัติบัตรสมาชิก</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">จัดการสมาชิก</h1>
        <a href="{{ route('admin.memberships.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>ออกบัตรสมาชิกใหม่
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <form action="{{ route('admin.memberships.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยชื่อ หรือ อีเมล..."
                        value="{{ request('search') }}">
                    <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อ-สกุล</th>
                            <th>อีเมล</th>
                            <th class="text-center">จำนวนบัตรทั้งหมด</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-center">{{ $user->user_memberships_count ?? count($user->userMemberships) }}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#historyModal-{{ $user->id }}">
                                        <i class="fas fa-history me-1"></i> ดูประวัติบัตร
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted p-4">ไม่พบข้อมูลผู้ใช้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($users->hasPages())
            <div class="card-footer">
                {{ $users->appends(request()->input())->links() }}
            </div>
        @endif
    </div>


    @foreach ($users as $user)
        <div class="modal fade" id="historyModal-{{ $user->id }}" tabindex="-1"
            aria-labelledby="historyModalLabel-{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="historyModalLabel-{{ $user->id }}">ประวัติบัตรสมาชิกของ:
                            {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($user->userMemberships->isEmpty())
                            <p class="text-center text-muted">ผู้ใช้นี้ยังไม่มีประวัติบัตรสมาชิก</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>เลขที่บัตร</th>
                                        <th>ประเภท</th>
                                        <th>ชั่วโมงคงเหลือ</th>
                                        <th>วันหมดอายุ</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->userMemberships as $membership)
                                        <tr>
                                            <td>{{ $membership->card_number }}</td>
                                            <td>{{ $membership->membershipTier->tier_name }}</td>
                                            <td>{{ $membership->remaining_hours }} / {{ $membership->initial_hours }}</td>
                                            <td>{{ thaidate('j M Y', $membership->expires_at) }}</td>
                                            <td>
                                                @if ($membership->status == 'active' && $membership->expires_at->isPast())
                                                    <span class="badge bg-danger">หมดอายุ</span>
                                                @elseif($membership->status == 'active')
                                                    <span class="badge bg-success">ใช้งาน</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $membership->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">ข้อมูลสมาชิก</h1>
</div>
<div class="card shadow-sm">
    <div class="card-header">
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" 
                       placeholder="ค้นหาสมาชิกด้วยชื่อ หรือ อีเมล..." 
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
                        <th>ชื่อ-สกุล</th>
                        <th>อีเมล</th>
                        <th class="text-center">จำนวนการจอง</th>
                        <th class="text-center">จำนวนบัตรสมาชิก</th>
                        <th class="text-center">ดูรายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">{{ $user->bookings_count }}</td>
                        <td class="text-center">{{ $user->user_memberships_count }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-search-plus me-1"></i> ดูข้อมูลทั้งหมด
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">ไม่พบข้อมูลผู้ใช้</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->appends(request()->input())->links() }}</div>
    @endif
</div>
@endsection
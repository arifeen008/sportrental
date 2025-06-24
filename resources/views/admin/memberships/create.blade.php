@extends('layouts.app') {{-- หรือ Layout ของ Admin --}}

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3><i class="fas fa-id-card me-2"></i> ออกบัตรสมาชิกใหม่</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.memberships.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label">เลือกผู้ใช้ (User)</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">-- กรุณาเลือกผู้ใช้ --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="membership_tier_id" class="form-label">เลือกประเภทบัตร</label>
                                <select name="membership_tier_id" id="membership_tier_id" class="form-select" required>
                                    <option value="">-- กรุณาเลือกประเภทบัตร --</option>
                                    @foreach ($tiers as $tier)
                                        <option value="{{ $tier->id }}">{{ $tier->tier_name }}
                                            ({{ $tier->included_hours }} ชม. / {{ $tier->validity_days }} วัน)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">ยืนยันการออกบัตร</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

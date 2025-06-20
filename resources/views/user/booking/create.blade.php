@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h2>แบบฟอร์มเช่าสนามฟุตบอล</h2>

        <form method="POST" action="{{ route('user.booking.confirm') }}">
            @csrf

            <!-- วันที่จอง -->
            <div class="mb-3">
                <label for="booking_date" class="form-label">วันที่จอง</label>
                <input type="date" class="form-control" id="booking_date" name="booking_date" required>
            </div>

            <!-- สนาม -->
            <div class="mb-3">
                <label for="field_id" class="form-label">เลือกสนาม</label>
                <select class="form-select" id="field_id" name="field_id" required>
                    <option value="">-- กรุณาเลือกสนาม --</option>
                    {{-- @foreach ($fields as $field)
                        <option value="{{ $field->id }}">{{ $field->name }}</option>
                    @endforeach --}}
                </select>
            </div>

            <!-- เวลาเริ่ม -->
            <div class="mb-3">
                <label for="start_time" class="form-label">เวลาเริ่ม</label>
                <input type="time" class="form-control" id="start_time" name="start_time" min="08:00" max="22:00"
                    step="3600" required>
            </div>

            <!-- เวลาสิ้นสุด -->
            <div class="mb-3">
                <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
                <input type="time" class="form-control" id="end_time" name="end_time" min="08:00" max="22:00"
                    step="3600" required>
            </div>

            <!-- จำนวนผู้เข้าร่วม -->
            <div class="mb-3">
                <label for="participants" class="form-label">จำนวนผู้เข้าร่วม</label>
                <input type="number" class="form-control" id="participants" name="participants" min="1" required>
            </div>

            <!-- หมายเหตุเพิ่มเติม -->
            <div class="mb-3">
                <label for="note" class="form-label">หมายเหตุเพิ่มเติม</label>
                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
            </div>

            <!-- ปุ่มส่ง -->
            <button type="submit" class="btn btn-primary">ยืนยันการจอง</button>
        </form>
    </div>
@endsection

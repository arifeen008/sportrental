@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">เพิ่มสนามกีฬาใหม่</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.add-field') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่อสนาม <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">ที่อยู่</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ old('address') }}">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียด</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="size" class="form-label">ขนาดสนาม (เช่น 7 คน, 11 คน)</label>
                                <input type="text" class="form-control" id="size" name="size"
                                    value="{{ old('size') }}">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="status" name="status" value="1"
                                    {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">สถานะเปิดใช้งาน</label>
                                <small class="form-text text-muted">ยกเลิกการเลือกเพื่อปิดใช้งานสนาม</small>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">รูปภาพสนาม</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">อัปโหลดรูปภาพสนาม (ไม่เกิน 2MB, รองรับ JPG, PNG,
                                    GIF)</small>
                            </div>

                            <button type="submit" class="btn btn-primary">บันทึกสนาม</button>
                            <a href="#" class="btn btn-secondary">ยกเลิก</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')
@section('content')
    <h1 class="h3 mb-4">เขียนข่าวใหม่</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">หัวข้อข่าว</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">เนื้อหา</label>
                    <textarea name="content" id="content" class="form-control" rows="10" required></textarea>
                </div>

                {{-- ส่วนอัปโหลดรูปภาพใหม่ --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cover_image" class="form-label"><b>รูปหน้าปก (Cover Image)</b></label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="other_images" class="form-label">รูปภาพประกอบอื่นๆ (เลือกได้หลายรูป)</label>
                        <input type="file" name="other_images[]" id="other_images" class="form-control" accept="image/*"
                            multiple>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">สถานะ</label>
                    <select name="status" id="status" class="form-select">
                        <option value="draft">บันทึกเป็นฉบับร่าง</option>
                        <option value="published">เผยแพร่ทันที</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">บันทึกข่าวสาร</button>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.admin')
@section('content')
    <h1 class="h3 mb-4">เขียนข่าวใหม่</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.posts.update',$post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label">หัวข้อข่าว</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{$post->title}}" required>
                </div>
                <div class="mb-3">
                    <label for="content_editor" class="form-label">เนื้อหา</label>
                    <textarea name="content" id="summernote" class="form-control" rows="15">{{ old('content', $post->content) }}</textarea>
                </div>

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
                        <option value="published">เผยแพร่ทันที</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">บันทึกข่าวสาร</button>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                tabsize: 2,
                height: 300
            });
        });
    </script>
@endpush
@push('styles')
    <script type="text/javascript" src="//code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" />
    <script type="text/javascript" src="cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">ข่าวสารและกิจกรรมทั้งหมด</h1>
            <p class="lead text-muted">ติดตามโปรโมชันและการอัปเดตล่าสุดจาก SKF STADIUM</p>
        </div>

        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm h-100">
                        @if ($post->cover_image_path)
                            <img src="{{ Storage::url($post->cover_image_path) }}" class="card-img-top"
                                alt="{{ $post->title }}" style="height: 220px; object-fit: cover;">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit($post->content, 120) }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">อ่านเพิ่มเติม</a>
                            </div>
                        </div>
                        <div class="card-footer text-muted small">
                            เผยแพร่เมื่อ: {{ thaidate('j M Y', $post->published_at) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">ยังไม่มีข่าวสารและกิจกรรมในขณะนี้</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $posts->links() }}
        </div>

    </div>
@endsection

@extends('layouts.app')

@push('styles')
    {{-- เพิ่ม CSS ของ Lightbox2 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" />
    <style>
        .post-content {
            line-height: 1.8;
        }

        .gallery-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 0.5rem;
            transition: transform 0.2s ease-in-out;
        }

        .gallery-image:hover {
            transform: scale(1.05);
        }

        .sidebar-post-list a {
            text-decoration: none;
            color: #333;
        }

        .sidebar-post-list a:hover .sidebar-post-title {
            color: var(--bs-primary);
        }

        .sidebar-post-date {
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
    <div class="container my-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <article>
                    @if ($post->cover_image_path)
                        <img src="{{ Storage::url($post->cover_image_path) }}" class="img-fluid rounded shadow-lg mb-4"
                            alt="{{ $post->title }}">
                    @endif
                    <h1 class="display-5 fw-bold mb-3">{{ $post->title }}</h1>
                    <p class="text-muted border-bottom pb-3 mb-4">
                        <i class="fas fa-calendar-alt me-2"></i>เผยแพร่เมื่อ:
                        {{ thaidate('l ที่ j F Y', $post->published_at) }}
                    </p>
                    <div class="post-content fs-5">
                        {!! $post->content !!}
                    </div>
                    @if ($post->images->isNotEmpty())
                        <hr class="my-5">
                        <h3 class="mb-4">แกลเลอรีรูปภาพ</h3>
                        <div class="row g-3">
                            @foreach ($post->images as $image)
                                <div class="col-lg-4 col-md-6">
                                    <a href="{{ Storage::url($image->path) }}" data-lightbox="post-gallery"
                                        data-title="{{ $post->title }}">
                                        <img src="{{ Storage::url($image->path) }}" class="gallery-image shadow-sm"
                                            alt="Gallery Image">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            </div>

            <div class="col-lg-4">
                <div class="position-sticky" style="top: 2rem;">
                    <div class="p-4 mb-3 bg-light rounded">
                        <h4 class="fst-italic">ข่าวสารอื่นๆ ที่น่าสนใจ</h4>
                        <div class="sidebar-post-list">
                            @forelse($otherPosts as $otherPost)
                                <div class="mb-3 border-bottom pb-3">
                                    <a href="{{ route('posts.show', $otherPost) }}">
                                        <h6 class="sidebar-post-title mb-1">{{ $otherPost->title }}</h6>
                                        <p class="sidebar-post-date text-muted mb-0">
                                            {{ thaidate('j M Y', $otherPost->published_at) }}
                                        </p>
                                    </a>
                                </div>
                            @empty
                                <p class="text-muted">ไม่มีข่าวสารอื่นๆ</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <a href="/" class="btn btn-primary">&laquo; กลับสู่หน้าหลัก</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
@endpush

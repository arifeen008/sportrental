@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                @if ($post->cover_image_path)
                    <img src="{{ Storage::url($post->cover_image_path) }}" class="img-fluid rounded shadow-lg mb-4"
                        alt="{{ $post->title }}">
                @endif

                <h1 class="display-5 fw-bold">{{ $post->title }}</h1>
                <p class="text-muted border-bottom pb-3 mb-4">
                    เผยแพร่เมื่อ: {{ thaidate('lที่ j F Y', $post->published_at) }}
                </p>

                <div class="post-content fs-5">
                    {!! nl2br(e($post->content)) !!}
                </div>

                @if ($post->images->isNotEmpty())
                    <hr class="my-5">
                    <h3 class="mb-4">แกลเลอรีรูปภาพ</h3>
                    <div class="row g-3">
                        @foreach ($post->images as $image)
                            <div class="col-lg-4 col-md-6">
                                <a href="{{ Storage::url($image->path) }}" data-bs-toggle="modal"
                                    data-bs-target="#imageModal-{{ $image->id }}">
                                    <img src="{{ Storage::url($image->path) }}" class="img-fluid rounded shadow-sm"
                                        alt="Gallery Image">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5">
                    <a href="/" class="btn btn-primary">&laquo; กลับสู่หน้าหลัก</a>
                </div>
            </div>
        </div>
    </div>

    @foreach ($post->images as $image)
        <div class="modal fade" id="imageModal-{{ $image->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-0"><img src="{{ Storage::url($image->path) }}" class="img-fluid"></div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

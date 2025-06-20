@extends('layouts.app')
@section('content')
    <div class="container mt-5">

        <div class="text-center mb-5">
            <h2 class="fw-bold">ระบบเช่าสนามฟุตบอล</h2>
            <p class="text-muted">เลือกสนามและจองช่วงเวลาที่ต้องการ</p>
        </div>

        <div class="row">
            {{-- สนามจำลองที่ 1 --}}
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="https://via.placeholder.com/400x200.png?text=สนาม+1" class="card-img-top" alt="สนาม A">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">สนาม A</h5>
                        <p class="card-text text-muted">สถานที่: ถนนวิภาวดี, กรุงเทพฯ</p>
                        <p class="text-success fw-bold">฿500 / ชั่วโมง</p>
                        <a href="{{ url('/booking/1') }}" class="btn btn-primary mt-auto">จองสนาม</a>
                    </div>
                </div>
            </div>

            {{-- สนามจำลองที่ 2 --}}
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="https://via.placeholder.com/400x200.png?text=สนาม+2" class="card-img-top" alt="สนาม B">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">สนาม B</h5>
                        <p class="card-text text-muted">สถานที่: รามคำแหง, กรุงเทพฯ</p>
                        <p class="text-success fw-bold">฿600 / ชั่วโมง</p>
                        <a href="{{ url('/booking/2') }}" class="btn btn-primary mt-auto">จองสนาม</a>
                    </div>
                </div>
            </div>

            {{-- สนามจำลองที่ 3 --}}
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="https://via.placeholder.com/400x200.png?text=สนาม+3" class="card-img-top" alt="สนาม C">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">สนาม C</h5>
                        <p class="card-text text-muted">สถานที่: เชียงใหม่</p>
                        <p class="text-success fw-bold">฿450 / ชั่วโมง</p>
                        <a href="{{ url('/booking/3') }}" class="btn btn-primary mt-auto">จองสนาม</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

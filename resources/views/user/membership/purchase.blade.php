@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .tier-card {
            transition: all 0.2s ease-in-out;
        }

        .tier-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">เลือกแพ็กเกจบัตรสมาชิก</h1>
            <p class="lead text-muted">เลือกบัตรที่ใช่สำหรับคุณ แล้วเริ่มจองสนามได้เลย!</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 text-center">
            @foreach ($tiers as $tier)
                <div class="col">
                    <div class="card shadow-sm h-100 tier-card">
                        {{-- ใช้สีที่แตกต่างกันสำหรับบัตร VIP --}}
                        <div class="card-header py-3 {{ $tier->tier_name == 'VIP 20 ชม.' ? 'bg-warning' : 'bg-light' }}">
                            <h4 class="my-0 fw-normal">{{ $tier->tier_name }}</h4>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title pricing-card-title">{{ number_format($tier->price) }}<small
                                    class="text-muted fw-light"> บาท</small></h1>
                            <ul class="list-unstyled mt-3 mb-4 flex-grow-1">
                                <li><i class="fas fa-check text-success"></i> ใช้งานได้ {{ $tier->included_hours }} ชั่วโมง
                                </li>
                                <li><i class="fas fa-check text-success"></i> มีอายุ {{ $tier->validity_days }} วัน</li>
                                <li><i class="fas fa-check text-success"></i> ใช้ได้กับทุกสนาม</li>
                                @if ($tier->special_perks)
                                    <li class="text-primary fw-bold mt-2">{{ $tier->special_perks }}</li>
                                @endif
                            </ul>
                            {{-- ฟอร์มสำหรับส่งข้อมูลการซื้อ --}}
                            <form action="{{ route('user.purchase.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="membership_tier_id" value="{{ $tier->id }}">
                                <button type="submit" class="w-100 btn btn-lg btn-primary">ซื้อบัตรนี้</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

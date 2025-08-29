@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .pricing-header {
            max-width: 700px;
        }

        .pricing-card {
            transition: all 0.3s ease-in-out;
        }

        .pricing-card:hover {
            transform: scale(1.02);
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
            <h1 class="display-4 fw-normal">อัตราค่าบริการและโปรโมชัน</h1>
            <p class="fs-5 text-muted">เลือกแพ็กเกจที่เหมาะสมกับการใช้งานของคุณที่สุด
                ไม่ว่าจะเป็นการเล่นกับเพื่อน,การจัดกิจกรรม, หรือการเป็นสมาชิกเพื่อรับสิทธิพิเศษ</p>
        </div>

        <h2 class="display-6 text-center mb-4">⭐ โปรโมชันบัตรสมาชิก ⭐</h2>
        <div class="row row-cols-1 row-cols-md-4 mb-3 text-center">
            @foreach ($membershipTiers as $tier)
                <div class="col">
                    <div class="card shadow-sm h-100 tier-card">
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

        <h2 class="display-6 text-center mb-4 mt-5">🕒 อัตราค่าเช่ารายชั่วโมง</h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ช่วงเวลา</th>
                                <th>อังคาร - ศุกร์</th>
                                <th>เสาร์ - อาทิตย์</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>09:00 - 18:00 น.</strong></td>
                                <td>350.- (กลางแจ้ง) / 450.- (ในร่ม)</td>
                                <td>400.- (กลางแจ้ง) / 500.- (ในร่ม)</td>
                            </tr>
                            <tr>
                                <td><strong>18:00 - 22:00 น.</strong></td>
                                <td>600.- (กลางแจ้ง) / 600.- (ในร่ม)</td>
                                <td>700.- (กลางแจ้ง) / 700.- (ในร่ม)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h2 class="display-6 text-center mb-4 mt-5">☀️ อัตราค่าเช่าเหมาวัน (08:00 - 18:00 น.)</h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ประเภทงาน</th>
                                <th>เหมา 2 สนาม</th>
                                <th>เหมาสนามกลางแจ้ง</th>
                                <th>เหมาสนามหลังคา</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>การกุศล</strong></td>
                                <td>5,000.-</td>
                                <td>2,000.-</td>
                                <td>3,500.-</td>
                            </tr>
                            <tr>
                                <td><strong>รายการแข่งขัน</strong></td>
                                <td>9,000.-</td>
                                <td>4,000.-</td>
                                <td>6,000.-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-4">
                    <h5 class="alert-heading"><i class="fas fa-info-circle"></i> หมายเหตุและเงื่อนไขสำคัญ</h5>
                    <ul class="mb-0">
                        <li><strong>การชำระเงิน:</strong> กรุณาชำระเงิน **เต็มจำนวน** ในวันที่ทำการจอง
                            เพื่อยืนยันสิทธิ์การจอง</li>
                        <li><strong>การเลื่อนวัน:</strong> สามารถขอเลื่อนวันใช้บริการได้ 1 ครั้ง
                            โดยต้องแจ้งล่วงหน้าอย่างน้อย 3 วัน</li>
                        <li>ทางสนามขอสงวนสิทธิ์ในการ **ไม่คืนเงิน** ในกรณียกเลิกการจอง</li>
                        <li>กรณีต้องการเวลาเพิ่มเติม (หลัง 18:00 น.) จะมีค่าบริการเพิ่มเติม กรุณาติดต่อสอบถาม</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

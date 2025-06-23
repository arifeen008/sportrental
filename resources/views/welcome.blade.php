@extends('layouts.app')
@section('styles')
    {{-- เพิ่ม Font Awesome CDN สำหรับไอคอนสวยๆ --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("{{ asset('images/DSC06294.jpg') }}");
            background-size: cover;
            background-position: center;
            height: 70vh;
            color: white;
        }

        .section-title {
            margin-bottom: 3rem;
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--bs-primary);
        }
    </style>
@endsection
@section('content')
    <div class="hero-section d-flex align-items-center justify-content-center text-center">
        <div class="container">
            <h1 class="display-3 fw-bold">SKF STADIUM SPORT CLUB</h1>
            <p class="lead col-lg-8 mx-auto">สนามฟุตบอลหญ้าเทียมมาตรฐาน บริการครบวงจร
                พร้อมเปิดประสบการณ์การเล่นฟุตบอลที่ดีที่สุดสำหรับคุณ</p>
            <a href="{{ route('user.booking.create') }}" class="btn btn-primary btn-lg mt-3 px-5 py-3 fw-bold">
                <i class="fas fa-calendar-check me-2"></i> จองสนามเลย!
            </a>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="text-center section-title fw-bold">ทำไมต้อง SKF STADIUM?</h2>
        <div class="row text-center g-4">
            <div class="col-md-4">
                <i class="fas fa-futbol feature-icon mb-3"></i>
                <h5>สนามหญ้าเทียมคุณภาพสูง</h5>
                <p class="text-muted">พื้นผิวสนามเรียบ นุ่ม ลดความเสี่ยงในการบาดเจ็บ ได้มาตรฐานการแข่งขัน</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-car feature-icon mb-3"></i>
                <h5>ที่จอดรถสะดวกสบาย</h5>
                <p class="text-muted">พื้นที่จอดรถกว้างขวาง รองรับรถได้จำนวนมาก ปลอดภัยไร้กังวล</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-store feature-icon mb-3"></i>
                <h5>สิ่งอำนวยความสะดวกครบครัน</h5>
                <p class="text-muted">มีคลับเฮาส์, ห้องน้ำ, ห้องอาบน้ำ, และร้านค้าไว้คอยบริการ</p>
            </div>
        </div>
    </div>

    <div class="bg-light">
        <div class="container py-5">
            <h2 class="text-center section-title fw-bold">ประเภทสนามของเรา</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <img src="{{ asset('images/DSC06291_0.jpg') }}" class="card-img-top" alt="สนามกลางแจ้ง"
                            height="250" style="object-fit: cover;">
                        {{-- <--- เปลี่ยนเป็นรูปสนามกลางแจ้งของคุณ --}}
                        <div class="card-body">
                            <h5 class="card-title">สนามกลางแจ้ง</h5>
                            <p class="card-text">สัมผัสบรรยากาศการเล่นฟุตบอลแบบ Open-air บนพื้นหญ้าเทียมมาตรฐาน
                                พร้อมระบบไฟส่องสว่างสำหรับช่วงเวลากลางคืน</p>
                            <a href="{{ route('user.booking.create') }}"
                                class="btn btn-outline-primary">ดูรายละเอียดและจอง</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <img src="{{ asset('images/DSC06293_0.jpg') }}" class="card-img-top" alt="สนามหลังคา" height="250"
                            style="object-fit: cover;">
                        {{-- <--- เปลี่ยนเป็นรูปสนามหลังคาของคุณ --}}
                        <div class="card-body">
                            <h5 class="card-title">สนามในร่ม (มีหลังคา)</h5>
                            <p class="card-text">ไม่ต้องกังวลกับสภาพอากาศ เล่นได้ทุกเวลา แดดไม่ร้อน ฝนไม่เปียก
                                เหมาะสำหรับการแข่งขันและเล่นกับเพื่อนฝูง</p>
                            <a href="{{ route('user.booking.create') }}"
                                class="btn btn-outline-primary">ดูรายละเอียดและจอง</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="text-center section-title fw-bold">อัตราค่าบริการ</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>ประเภทบริการ</th>
                                <th>ราคาเริ่มต้น</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>เช่ารายชั่วโมง</td>
                                <td><strong class="fs-5">350.-</strong> / ชั่วโมง</td>
                            </tr>
                            <tr>
                                <td>เช่าเหมาวัน</td>
                                <td><strong class="fs-5">2,000.-</strong> / วัน</td>
                            </tr>
                            <tr>
                                <td>บัตรสมาชิก (10 ชั่วโมง)</td>
                                <td><strong class="fs-5">2,500.-</strong> (เฉลี่ย 250.-/ชม.)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-link">ดูรายละเอียดราคาและโปรโมชันทั้งหมด</a>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid g-0">
        <div class="row g-0">
            <div class="col-md-6 bg-dark text-white p-5 d-flex flex-column justify-content-center">
                <h3>ติดต่อและเดินทาง</h3>
                <p>SKF STADIUM ตั้งอยู่ในทำเลที่เดินทางสะดวก พร้อมให้บริการทุกวัน</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt fa-fw me-2"></i>47 หมู่ 7 ตำบลคลองยาง อำเภอเกาะลันตา
                        จังหวัดกระบี่ 81120 </li>
                    <li class="mb-2"><i class="fas fa-phone fa-fw me-2"></i> 081-234-5678</li>
                    <li class="mb-2"><i class="fab fa-line fa-fw me-2"></i> @skfstadium</li>
                    <li class="mb-2"><i class="fas fa-clock fa-fw me-2"></i> เปิดบริการทุกวัน 09:00 - 22:00 น.
                        (หยุดวันจันทร์)</li>
                </ul>
            </div>
            <div class="col-md-6">
                {{-- วางโค้ด Embed ของ Google Map ที่นี่ --}}
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d869.4077309369442!2d99.08944055827!3d7.810541283461214!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3051fbba7bc981ef%3A0xbea056ee4ac4e1b9!2sSouthern%20football%20club%20Klongyang!5e1!3m2!1sth!2sth!4v1750664691950!5m2!1sth!2sth"
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
@endsection

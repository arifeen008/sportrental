@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-chart-bar"></i> รายงานสรุปการจอง</h1>

        {{-- ตัวเลือกช่วงเวลา --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">กรองข้อมูลตามช่วงเวลา</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports.summary') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="start_date" class="form-label">ตั้งแต่วันที่</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-md-5">
                            <label for="end_date" class="form-label">ถึงวันที่</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> แสดงรายงาน
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- สรุปภาพรวม --}}
        <div class="row">
            {{-- รายได้รวม --}}
            <div class="col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    รายได้รวมทั้งหมด</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalRevenue, 2) }} บาท
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ยอดค่ามัดจำ --}}
            <div class="col-md-4 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    รายได้จากค่ามัดจำเหมาวัน</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($depositRevenue, 2) }} บาท
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- จำนวนชั่วโมงที่จอง --}}
            <div class="col-md-4 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    รวมจำนวนชั่วโมงที่ถูกจอง</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalHoursBooked, 2) }} ชั่วโมง
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- รายงานการจอง --}}
        <div class="row">
            {{-- รายงานตามประเภทการจอง --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">สรุปตามประเภทการจอง</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($bookingsByType as $type => $data)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>
                                        @if ($type === 'hourly')
                                            จองรายชั่วโมง
                                        @elseif ($type === 'daily_package')
                                            เหมาวัน
                                        @elseif ($type === 'membership')
                                            บัตรสมาชิก
                                        @endif
                                    </strong>
                                    <div>
                                        <span>{{ $data['count'] }} รายการ</span>
                                        <span class="ms-3">{{ number_format($data['revenue'], 2) }} บาท</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            {{-- รายงานตามสนาม --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">สรุปตามสนามที่จอง</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($bookingsByField as $field)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>{{ $field['field_name'] }}</strong>
                                    <div>
                                        <span>{{ $field['count'] }} รายการ</span>
                                        <span class="ms-3">{{ number_format($field['revenue'], 2) }} บาท</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- ปุ่มสำหรับพิมพ์รายงาน --}}
        <div class="d-grid mt-4">
            <button onclick="window.print()" class="btn btn-secondary btn-lg"><i class="fas fa-print me-2"></i> พิมพ์รายงาน</button>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .container-fluid, .container-fluid * {
                visibility: visible;
            }
            .container-fluid {
                position: absolute;
                left: 0;
                top: 0;
            }
            .card-header, .btn-primary {
                display: none !important;
            }
        }
    </style>
@endpush
@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow-sm">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Admin Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-3">
                                <a href="{{ route('admin.rent-schedule') }}" class="btn btn-primary">ดูตารางการเช่าสนาม</a>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-3">
                                <a href="{{ route('admin.members') }}" class="btn btn-primary">ดูข้อมูลสมาชิก</a>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-primary">รายงานสรุป</a>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-3">
                                <a href="{{ route('admin.field.index') }}" class="btn btn-primary">เพิ่มสนามกีฬา</a>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-3">
                                <a href="{{ route('admin.rent-requests') }}" class="btn btn-primary">ดูคำร้องเช่าสนาม</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-sm">
                    <div class="card-header text-center bg-success text-white">
                        <h4>Register</h4>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" required autofocus
                                    autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- เพิ่มช่องสำหรับหมายเลขบัตรประชาชน --}}
                            <div class="mb-3">
                                <label for="id_card" class="form-label">เลขบัตรประชาชน</label>
                                <input type="text" id="id_card" name="id_card" value="{{ old('id_card') }}"
                                    class="form-control @error('id_card') is-invalid @enderror" required
                                    autocomplete="id-card-number" maxlength="13" pattern="\d{13}"
                                    title="กรุณากรอกเลขบัตรประชาชน 13 หลัก">
                                @error('id_card')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- เพิ่มช่องสำหรับเบอร์โทรศัพท์ --}}
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number') }}"
                                    class="form-control @error('phone_number') is-invalid @enderror" autocomplete="tel"
                                    maxlength="10" pattern="\d{10}" title="กรุณากรอกเบอร์โทรศัพท์ 10 หลัก">
                                @error('phone_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required
                                    autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" required autocomplete="new-password">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Register</button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
